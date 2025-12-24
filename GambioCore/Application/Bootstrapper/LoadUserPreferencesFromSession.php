<?php
/* --------------------------------------------------------------
 LoadUserPreferencesFromSession.php 2022-05-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Bootstrapper;

use Gambio\Admin\Modules\Language\Model\Exceptions\LanguageNotFoundException;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Language\Services\LanguageService;
use RuntimeException;

/**
 * Class LoadUserPreferencesFromSession
 * @package Gambio\Core\Application\Bootstrapper
 * @codeCoverageIgnore
 */
class LoadUserPreferencesFromSession implements Bootstrapper
{
    /**
     * @var LanguageService
     */
    private $languageService;
    
    /**
     * @var Url
     */
    private $url;
    
    /**
     * @var string
     */
    private $defaultLanguageCode;
    
    /**
     * @var array
     */
    private $availableLanguageCodes;
    
    
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $this->init($application);
        
        try {
            $languageCode = $this->determineLanguageChange() ?? $this->fetchLanguageCodeFromUrl();
            $language     = $this->languageService->getLanguageByCode($languageCode ?? $this->defaultLanguageCode);
        } catch (LanguageNotFoundException $e) {
            throw new RuntimeException('Could not initialize user preferences, because default language "'
                                       . $this->defaultLanguageCode . '" does not exist.');
        }
        
        if (!isset($_SESSION)) {
            $application->registerShared(UserPreferences::class)->addArgument(null)->addArgument($language->id());
            
            return;
        }
        
        if (array_key_exists('language', $_GET) || $languageCode || !array_key_exists('language', $_SESSION)) {
            $_SESSION['language']         = $language->directory();
            $_SESSION['languages_id']     = $language->id();
            $_SESSION['language_charset'] = $language->charset();
            $_SESSION['language_code']    = $language->code();
        }
        
        $customerId = $_SESSION['customer_id'] ?? null;
        if (!is_null($customerId)) {
            $customerId = (int)$customerId;
        }
        $languageId = (int)($_SESSION['languages_id'] ?? 2);
        
        $application->registerShared(UserPreferences::class)
            ->addArgument($customerId)
            ->addArgument($languageId);
    }
    
    
    private function init(Application $application): void
    {
        /** @var ConfigurationFinder $configurationFinder */
        $configurationFinder = $application->get(ConfigurationFinder::class);
        
        $this->languageService = $application->get(LanguageService::class);
        $this->url             = $application->get(Url::class);
        
        $this->defaultLanguageCode    = $configurationFinder->get('configuration/DEFAULT_LANGUAGE', 'de');
        $this->availableLanguageCodes = [];
        foreach ($this->languageService->getAvailableLanguages() as $language) {
            $this->availableLanguageCodes[] = $language->code();
        }
    }
    
    
    /**
     * @return string
     */
    private function fetchLanguageCodeFromUrl(): ?string
    {
        if ($this->url->path() === '') {
            $searchPattern = '/^\/(?<code>[a-zA-Z0-9]{2})(\/.*)?$/';
        } else {
            $searchPattern = '/^\/' . str_replace('/', '\/', substr($this->url->path(), 1))
                             . '\/(?<code>[a-zA-Z0-9]{2})(\/.*)?$/';
        }
        
        preg_match($searchPattern, $_SERVER['REQUEST_URI'], $matches);
        if (isset($matches['code']) && in_array($matches['code'], $this->availableLanguageCodes, true)) {
            return $matches['code'];
        }
        
        return null;
    }
    
    
    /**
     * @return string|null
     */
    private function determineLanguageChange(): ?string
    {
        if (isset($_GET['language']) === false || is_string($_GET['language']) === false
            || strlen($_GET['language']) !== 2) {
            return null;
        }
        
        return in_array($_GET['language'], $this->availableLanguageCodes, true) ? $_GET['language'] : null;
    }
}
