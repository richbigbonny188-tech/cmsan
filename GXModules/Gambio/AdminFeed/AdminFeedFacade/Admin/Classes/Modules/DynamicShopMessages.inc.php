<?php
/* --------------------------------------------------------------
   DynamicShopMessages.inc.php 2018-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\AdminFeed\CurlClient;
use Gambio\AdminFeed\RequestControl;

/**
 * Class DynamicShopMessages
 */
class DynamicShopMessages
{
    /**
     * @var DataCache
     */
    protected $dataCache;
    
    /**
     * @var string
     */
    protected $cacheKey = 'dynamic-shop-messages';
    
    /**
     * @var string URL to fetch dynamic shop messages
     */
    protected $endpointUrl;
    
    
    /**
     * ExternalSnippetsControl constructor.
     */
    public function __construct()
    {
        $this->dataCache   = DataCache::get_instance();
        $this->endpointUrl = AdminFeedLinks::DYNAMIC_SHOP_MESSAGES_URL;
    }
    
    
    /**
     * Returns the snippets as a json response.
     *
     * @return JsonHttpControllerResponse
     */
    public function messages()
    {
        try {
            // Check if a there is a cached response.
            $jsonString = $this->_getCacheResponse();
            
            if ($jsonString === false) {
                $jsonString = $this->_getRequestResponse(); // There is no cached response so make a new request.
            }
            
            $response = json_decode($jsonString, true);
        } catch (Exception $ex) {
            $response = AjaxException::response($ex);
        }
        
        if ($response === null) {
            $response = [
                'SOURCES'  => [],
                'MESSAGES' => []
            ]; // We must not pass a null value to the JsonHttpControllerResponse object.
        }
        
        return MainFactory::create('JsonHttpControllerResponse', $response);
    }
    
    
    /**
     * Get the cached response.
     *
     * @return bool|string Returns the JSON string or false if the cache is outdated.
     */
    protected function _getCacheResponse()
    {
        if (!$this->dataCache->key_exists($this->cacheKey, true)) {
            return false; // There is no cache file.
        }
        
        $cacheData = $this->dataCache->get_persistent_data($this->cacheKey);
        
        return date('Ymd') === date('Ymd', $cacheData['timestamp']) ? $cacheData['response'] : false;
    }
    
    
    /**
     * Get dynamic messages with cURL request to Gambio's servers.
     *
     * @return bool|string Returns the response JSON string or false if an error occurred.
     */
    protected function _getRequestResponse()
    {
        include DIR_FS_CATALOG . 'release_info.php';
        
        // Create data source URL.
        $params   = [];
        $params[] = 'shop_version=' . rawurlencode($gx_version);
        $params[] = 'news_type=DOM';
        
        if (gm_get_conf('SHOP_KEY_VALID') === '1') {
            $params[] = 'shop_url=' . rawurlencode(HTTP_SERVER . DIR_WS_CATALOG);
            $params[] = 'shop_key=' . rawurlencode(GAMBIO_SHOP_KEY);
            $params[] = 'language=' . rawurlencode($_SESSION['language_code']);
        }
        
        if (gm_get_conf('ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING') === 'true') {
            $requestControl = new RequestControl(new CurlClient());
            $adminFeedToken = $requestControl->createRequestToken();
            $params[]       = 'adminFeedToken=' . rawurlencode($adminFeedToken);
            
            if (gm_get_conf('SHOP_KEY_VALID') !== '1') {
                $params[] = 'shop_url=' . rawurlencode(HTTP_SERVER . DIR_WS_CATALOG);
            }
        }
        
        $url        = $this->endpointUrl . '?' . implode('&', $params);
        $loadUrl    = MainFactory::create('LoadUrl');
        $jsonString = $loadUrl->load_url($url, ['Accept: application/json'], '', false, false);
        
        $cacheData = [
            'timestamp' => time(),
            'response'  => $jsonString
        ];
        
        $this->dataCache->write_persistent_data($this->cacheKey, $cacheData);
        
        return $jsonString;
    }
}