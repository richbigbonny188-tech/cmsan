<?php
/* --------------------------------------------------------------
   AdminInfobox.inc.php 2018-08-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\AdminFeed\CurlClient;
use Gambio\AdminFeed\RequestControl;

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

/**
 * Class AdminInfobox
 */
class AdminInfobox
{
    /**
     * @var string
     */
    protected $url = '';
    
    /**
     * @var array
     */
    protected $parameters = [];
    
    /**
     * @var bool
     */
    protected $hasShopKey;
    
    
    /**
     * AdminInfobox constructor.
     */
    public function __construct()
    {
        $this->hasShopKey = defined('GAMBIO_SHOP_KEY') && trim(GAMBIO_SHOP_KEY) !== '';
        
        $this->_setUrl();
        $this->_setParameters();
    }
    
    
    /**
     * Returns the messages for the admin infobox.
     *
     * @return mixed
     */
    public function messages()
    {
        /* @var LoadUrl $loadUrl */
        $loadUrl = MainFactory::create_object('LoadUrl');
        
        $url = $this->url . '?' . implode('&', $this->parameters);
        
        $serverResponse   = $loadUrl->load_url($url, ['Accept: application/json'], '', false, false);
        $c_serverResponse = (string)$serverResponse;
        
        $json          = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        $responseArray = $json->decode($c_serverResponse);
        
        return $responseArray;
    }
    
    
    /**
     * Sets the url for the curl request.
     */
    protected function _setUrl()
    {
        if ($this->hasShopKey) {
            $this->url = AdminFeedLinks::DYNAMIC_SHOP_MESSAGES_URL;
            
            return;
        }
        
        $this->url = AdminFeedLinks::SHOP_MESSAGES;
    }
    
    
    /**
     * Sets the parameters for the curl request.
     */
    protected function _setParameters()
    {
        include(DIR_FS_CATALOG . 'release_info.php');
        
        $parameters   = [];
        $parameters[] = 'shop_version=' . rawurlencode($gx_version);
        
        if ($this->hasShopKey) {
            $parameters[] = 'shop_url=' . rawurlencode(HTTP_SERVER . DIR_WS_CATALOG);
            $parameters[] = 'shop_key=' . rawurlencode(GAMBIO_SHOP_KEY);
            $parameters[] = 'language=' . rawurlencode($_SESSION['language_code']);
            $parameters[] = 'server_path=' . rawurlencode(rtrim(DIR_FS_CATALOG, '/'));
        }
        
        if (gm_get_conf('ADMIN_FEED_ACCEPTED_SHOP_INFORMATION_DATA_PROCESSING') === 'true') {
            $requestControl = new RequestControl(new CurlClient());
            $adminFeedToken = $requestControl->createRequestToken();
            $parameters[]   = 'adminFeedToken=' . rawurlencode($adminFeedToken);
            
            if (!$this->hasShopKey) {
                $parameters[] = 'shop_url=' . rawurlencode(HTTP_SERVER . DIR_WS_CATALOG);
            }
        }
        
        $this->parameters = $parameters;
    }
}
