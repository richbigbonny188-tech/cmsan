<?php
/* --------------------------------------------------------------
   HubPayPalSettings.inc.php 2023-02-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Core\Cache\Services\CacheFactory;
use HubPublic\Exceptions\CurlRequestException;
use HubPublic\Http\CurlRequest;
use League\Container\Exception\NotFoundException;

class HubPayPalSettings
{
    protected $config;
    protected $cache;
    
    const CACHE_TTL = 3600;
    const CACHE_FILE = 'paypal2hub.json.cache';
    
    public function __construct(HubPayPalConfiguration $configuration)
    {
        $this->config = $configuration;
        $this->cache = null;
        if (class_exists(LegacyDependencyContainer::class)) {
            try {
                /** @var CacheFactory $cacheFactory */
                $cacheFactory = LegacyDependencyContainer::getInstance()->get(CacheFactory::class);
                $this->cache = $cacheFactory->createCacheFor('paypal2hub');
            } catch (NotFoundException $e) {
                $this->cache = null;
            }
        }
    }
    
    
    protected function getJavascriptSource($position = 'cart')
    {
        if (empty(gm_get_conf('GAMBIO_HUB_CLIENT_KEY'))) {
            return ''; // shop is not connected to the hub
        }
        
        if (!gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_PAYPAL2HUB_ECS')) {
            return ''; // The module is not installed.
        }
        
        $cacheKey = 'paypalhub_jssrc_' . $position;
        $jssrc = $this->getJavascriptSourceFromCache($cacheKey);
        
        if ($jssrc === null) {
            try {
                $jssrc = $this->getJavascriptSourceFromHub($position);
                $this->setJavascriptSourceInCache($cacheKey, $jssrc);
            } catch(\UnexpectedValueException $e) {
                return '';
            } catch(RuntimeException $e) {
                return '';
            } catch (CurlRequestException $e) {
                return '';
            }
        }
    
        $url = parse_url($jssrc);
        parse_str($url['query'], $query);
        $query['currency'] = $_SESSION['currency'];
        if (strpos($_SERVER['SCRIPT_NAME'], 'shopping_cart') === false) {
            unset($query['disable-funding']);
        }
        $builtQuery = http_build_query($query, '', '&');
        $jssrc = "{$url['scheme']}://{$url['host']}{$url['path']}?{$builtQuery}";
        
        return $jssrc;
    }
    
    protected function getJavascriptSourceFromHub($position = 'cart')
    {
        $query = [
            'client_key' => gm_get_conf('GAMBIO_HUB_CLIENT_KEY'),
            'devmode'    => file_exists(DIR_FS_CATALOG . '.dev-environment') ? 'true' : 'false',
        ];
        
        $maxTimeout = 5;
        $timeout = min($maxTimeout, (int)gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT'));
        /** @var HubSettings $hubSettings */
        $hubSettings = MainFactory::create('HubSettings', $timeout);
        
        /** @var \HubCallbackApiClient $hubCallbackApiClient */
        $hubCallbackApiClient = MainFactory::create(
            'HubCallbackApiClient',
            MODULE_PAYMENT_GAMBIO_HUB_URL,
            new CurlRequest(),
            LogControl::get_instance(),
            $hubSettings
        );
        
        $params = ['source' => 'get_javascript_source', 'position' => (string)$position];
        /** @var \HubPublic\ValueObjects\HttpResponse $response */
        $response = $hubCallbackApiClient->execute('PayPal2Hub', true, $params, $query);
    
        if ($response->getStatusCode() !== 200) {
            return null;
        }
    
        $responseBody = json_decode($response->getBody(), true);
        if (!empty($responseBody['jssrc']) && is_array($responseBody) && json_last_error() === JSON_ERROR_NONE) {
            return $responseBody['jssrc'];
        }
        
        return null;
    }
    
    protected function getJavascriptSourceFromCache($cacheKey)
    {
        if ($this->cache !== null && $this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        $cacheFile = DIR_FS_CATALOG . 'cache/' . static::CACHE_FILE;
        if ($this->cache === null && file_exists($cacheFile)) {
            $cachedData = json_decode(file_get_contents($cacheFile), true);
            if (isset($cachedData['expires_at']) && (int)$cachedData['expires_at'] > time()) {
                return $cachedData['jssrc'];
            }
        }
        return null;
    }
    
    
    protected function setJavascriptSourceInCache($cacheKey, $jssrc)
    {
        if ($this->cache !== null) {
            $this->cache->set($cacheKey, $jssrc, static::CACHE_TTL);
            
            return;
        }
        $cacheFile = DIR_FS_CATALOG . 'cache/' . static::CACHE_FILE;
        file_put_contents($cacheFile,
            json_encode([
                'expires_at' => time() + static::CACHE_TTL,
                'jssrc'      => $jssrc,
            ]));
    }
}
