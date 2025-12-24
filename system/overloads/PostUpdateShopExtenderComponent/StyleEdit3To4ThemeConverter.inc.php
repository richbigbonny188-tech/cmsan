<?php
/* --------------------------------------------------------------
   StyleEdit3To4ThemeConverter.inc.php 2022-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Curl\Curl;
use Firebase\JWT\JWT;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

/**
 * Class StyleEdit3To4ThemeConverter
 */
class StyleEdit3To4ThemeConverter extends StyleEdit3To4ThemeConverter_parent
{
    protected const URL_CONTAINS_STYLE_EDIT_API_PAT   = '#GXModules/Gambio/StyleEdit/Api/api.php#';
    protected const STYLEEDIT_4_API_URL               = 'GXModules/Gambio/StyleEdit/Api/api.php/';
    protected const STYLEEDIT_3_CONFIGURATION_REQUEST = self::STYLEEDIT_4_API_URL
                                                        . 'styleedit/de/configuration/{current-theme}/styleedit3';
    protected const STYLEEDIT_4_SAVE_THEME_REQUEST    = self::STYLEEDIT_4_API_URL . 'styleedit/de/theme/{current-theme}';
    
    /**
     * @var string
     */
    protected $shopRoot;
    
    /**
     *
     */
    public function proceed()
    {
        parent::proceed();
    
        /*if (file_exists(__DIR__ . '/../../../version_info/cloud.php') === false) {
            $this->shopRoot    = dirname(__DIR__, 3);
            $exceptionOccurred = null;
    
            try {
                if ($this->currentThemeInheritsHoneygrid() && $this->currentThemeIsNotInitialized()
                    && $this->requestIsNotSendFromStyleEdit()) {
                    $exceptionOccurred       = false;
                    $configuration           = $this->activeThemeStyleEdit3Style();
                    $styleEdit4Configuration = $this->styleEdit4Configuration($configuration);
            
                    $this->updateCurrentThemeSettingsJson($styleEdit4Configuration);
                }
            } catch (NoActiveStyleEdit3StyleExceptions|ApiAnsweredWithAnErrorException $exception) {
                $this->logException($exception);
                $exceptionOccurred = true;
        
                unset($exception);
            }
    
            if ($exceptionOccurred === false) {
                $this->clearCache();
                $this->refreshCurrentPage();
            }
        }*/
    }
    
    
    /**
     * @param stdClass[] $configurations
     *
     * @throws ErrorException
     */
    protected function updateCurrentThemeSettingsJson(array $configurations): void
    {
        $options = [];
    
        foreach ($configurations as $configuration) {
        
            $options[$configuration->name] = (object)[
                'type'  => $configuration->type,
                'value' => $configuration->value,
                'group' => $configuration->group
            ];
        }
    
        $data = json_encode((object)['options' => $options]);
    
        $this->curl()->patch($this->getEndpoint('STYLEEDIT_4_SAVE_THEME_REQUEST'), $data);
    }
    
    
    /**
     * @return stdClass[]
     * @throws ErrorException
     * @throws ApiAnsweredWithAnErrorException
     */
    protected function styleEdit3ThemeConfigurations(): array
    {
        $endpoint = $this->getEndpoint('STYLEEDIT_3_CONFIGURATION_REQUEST');
        $json = $this->curl()->get($endpoint);
        
        $this->validateResponse($json, $endpoint);
        
        return json_decode($json, false);
    }
    
    
    /**
     * @return stdClass
     * @throws NoActiveStyleEdit3StyleExceptions
     * @throws ApiAnsweredWithAnErrorException
     * @throws ErrorException
     */
    protected function activeThemeStyleEdit3Style(): stdClass
    {
        $configurations = $this->styleEdit3ThemeConfigurations();
        
        if (count($configurations)) {
    
            $currentTheme              = $this->currentThemeName();
            $activeThemeConfigurations = array_filter($configurations,
                static function (stdClass $configuration) use ($currentTheme) {
            
                    return $configuration->name === $currentTheme && $configuration->isActive;
                });
            
            if (count($activeThemeConfigurations)) {
        
                return current($activeThemeConfigurations);
            }
        }
        
        throw NoActiveStyleEdit3StyleExceptions::forTheme($this->currentThemeName());
    }
    
    
    /**
     * @return bool
     */
    protected function currentThemeInheritsHoneygrid(): bool
    {
        $themeControl      = StaticGXCoreLoader::getThemeControl();
        
        if (!$themeControl->isThemeSystemActive()) {
            
            return false;
        }
        
        $currentTheme = $this->currentThemeName();
        
        if ($currentTheme === 'Honeygrid') {
         
        	return true;
        }
    
        $themeJsonPath   = $this->shopRoot . '/themes/' . $currentTheme . '/theme.json';
        $themeJsonString = file_get_contents($themeJsonPath);
        $themeJson       = json_decode($themeJsonString, false);
        
        return $themeJson instanceof stdClass && isset($themeJson->extends) && $themeJson->extends === 'Honeygrid';
    }
    
    
    /**
     * @return string
     */
    protected function currentThemeName(): string
    {
        return StaticGXCoreLoader::getThemeControl()->getCurrentTheme();
    }
    
    /**
     * @return bool
     */
    protected function currentThemeIsNotInitialized(): bool
    {
        $currentThemeSettingsJsonPath = $this->shopRoot . str_replace('/',
                                                             DIRECTORY_SEPARATOR,
                                                             '/themes/' . $this->currentThemeName() . '/settings.json');
    
        return file_exists($currentThemeSettingsJsonPath) === false;
    }
    
    
    /**
     * @param stdClass $configuration
     *
     * @return stdClass[]
     * @throws ErrorException
     * @throws ApiAnsweredWithAnErrorException
     */
    protected function styleEdit4Configuration(stdClass $configuration): array
    {
        $data   = json_encode($configuration);
        $result = $this->curl()->post($this->getEndpoint('STYLEEDIT_3_CONFIGURATION_REQUEST'), $data);
        
        $this->validateResponse($result, $this->getEndpoint('STYLEEDIT_3_CONFIGURATION_REQUEST'));
        
        return json_decode($result, false);
    }
    
    
    /**
     * @return Curl
     * @throws ErrorException
     */
    protected function curl(): Curl
    {
        $shopUrl = (ENABLE_SSL ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG;
        $curl    = new Curl($shopUrl);
    
        $jwtToken = $this->createJwt();
        $bearer   = 'Bearer ' . $jwtToken;
        
        $curl->setHeader('Authorization', $bearer);
        $curl->setHeader('x-auth-token', $bearer);
        $curl->setJsonDecoder(static function(string $json) {
            return $json;
        });
        
        return $curl;
    }
    
    
    /**
     * @return string
     */
    protected function createJwt(): string
    {
        $customerServiceFactory = MainFactory::create(CustomerServiceFactory::class,
                                                      StaticGXCoreLoader::getDatabaseQueryBuilder());
        $customerReadService    = $customerServiceFactory->createCustomerReadService();
        $customerId             = 1;
        $customer               = $customerReadService->getCustomerById(new IdType($customerId));
        $firstName              = (string)$customer->getFirstname();
        $lastName               = (string)$customer->getLastname();
        $statusId               = (int)$customer->getStatusId();
        $tokenArray             = [
            'customer_id'         => $customerId,
            'customers_status_id' => $statusId,
            'customer_first_name' => trim($firstName),
            'customer_last_name'  => trim($lastName),
        ];
    
        return JWT::encode($tokenArray, StyleEdit4AuthenticationController::getSecret());
    }
    
    
    /**
     * @param        $response
     * @param string $request
     *
     * @throws ApiAnsweredWithAnErrorException
     */
    protected function validateResponse($response, string $request): void
    {
        if (is_bool($response)) {
    
            throw ApiAnsweredWithAnErrorException::forRequest($request, 'Api did not answer with a JSON');
        }
        
        if ($response instanceof stdClass && isset($response->status) && $response->status === 'error') {
            
            $message = $response->message ?? '';
            
            throw ApiAnsweredWithAnErrorException::forRequest($request, $message);
        }
    }
    
    
    /**
     * @param Exception $exception
     */
    protected function logException(Exception $exception): void
    {
        $rootPath = $this->shopRoot;
        $logPath  = $rootPath . str_replace('/', DIRECTORY_SEPARATOR, '/logfiles/se3to4.log');
        $message  = date('Y-m-d H:i:s') . ' ';
        $message  .= get_class($exception) . ': with the message: ' . $exception->getMessage() . PHP_EOL;
        
        if (file_exists($logPath) === false) {
            
            touch($logPath);
        }
        
        file_put_contents($logPath, $message, FILE_APPEND);
        
        $sentryInitScript = $rootPath . '/system/core/logging/SentryErrorHandler.inc.php';
        
        if ($exception instanceof ApiAnsweredWithAnErrorException && file_exists($sentryInitScript)) {
            
            try {
                include $sentryInitScript;
                
                /** @var Raven_Client|null $sentryClient */
                /** @var array $sentryConfig */
                if ($sentryConfig['active'] && $sentryClient instanceof Raven_Client) {
                    
                    $sentryClient->captureException($exception);
                }
                
            } catch (Throwable $throwable) {
                unset($throwable);
            }
        }
    }
    
    
    protected function clearCache(): void
    {
        $cacheControl = MainFactory::create('CacheControl');
        $cacheControl->clear_content_view_cache();
        $cacheControl->clear_templates_c();
    
        $permissionMap = [
            'file' => [
                'public'  => 0777,
                'private' => 0700,
            ],
            'dir'  => [
                'public'  => 0777,
                'private' => 0700,
            ],
        ];
        $visibility = PortableVisibilityConverter::fromArray($permissionMap);
        
        $filesystemAdapter = new LocalFilesystemAdapter(dirname(__DIR__, 3), $visibility, LOCK_EX, LocalFilesystemAdapter::DISALLOW_LINKS);
        $filesystem        = new Filesystem($filesystemAdapter);
        $adapter           = MainFactory::create(FilesystemAdapter::class, $filesystem);
        
        $dir = 'public/theme';
    
        if ($adapter->has($dir)) {
            
            $adapter->deleteDir($dir);
            @$adapter->createDir($dir, ['visibility' => 'public']);
        }
    }
    
    
    protected function refreshCurrentPage(): void
    {
        header('Location: '.$_SERVER['REQUEST_URI']);
        die;
    }
    
    
    /**
     * @return bool
     */
    protected function requestIsNotSendFromStyleEdit(): bool
    {
        return preg_match(self::URL_CONTAINS_STYLE_EDIT_API_PAT, $this->getCurrentUrl(true)) === 0;
    }
    
    
    /**
     * @param bool $withQuery
     *
     * @return string
     */
    protected function getCurrentUrl($withQuery = true): string
    {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === false ? 'http' : 'https';
        $uri = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        return $withQuery ? $uri : str_replace('?' . $_SERVER['QUERY_STRING'], '', $uri);
    }
    
    
    protected function getEndpoint(string $constantName): string
    {
        return str_replace('{current-theme}',
                           $this->currentThemeName(),
                           constant(get_class($this) . '::' . $constantName));
    }
}