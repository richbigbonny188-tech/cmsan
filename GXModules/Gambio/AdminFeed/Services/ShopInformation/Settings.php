<?php
/* --------------------------------------------------------------
   Settings.php 2021-05-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation;

/**
 * Class Settings
 *
 * @package Gambio\AdminFeed\Services\ShopInformation
 */
class Settings
{
    /**
     * Returns the file systems path to the base directory of the shop.
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return defined('DIR_FS_CATALOG') ? DIR_FS_CATALOG : '';
    }
    
    
    /**
     * Returns the status of theme support for this shop.
     *
     * @return bool
     */
    public function areThemesAvailable()
    {
        return true;
    }
    
    
    /**
     * Returns the name of the currently selected template.
     * 
     * @deprecated since GX 4.5
     *
     * @return string
     */
    public function getActiveTemplate()
    {
        return '';
    }
    
    
    /**
     * Returns the version of the currently selected template.
     *
     * @return string
     */
    public function getActiveTemplateVersion()
    {
        return gm_get_env_info('TEMPLATE_VERSION');
    }
    
    
    /**
     * Returns the base URL of the shop, ending at the base web directory.
     *
     * @return string
     */
    public function getHttpServer()
    {
        return defined('HTTP_SERVER') ? HTTP_SERVER : '';
    }
    
    
    /**
     * Returns the path of the shop, starting from the base web directory.
     *
     * @return string
     */
    public function getShopDirectory()
    {
        return defined('DIR_WS_CATALOG') ? DIR_WS_CATALOG : '';
    }
    
    
    /**
     * Returns the complete URL of the shop.
     *
     * @return string
     */
    public function getShopUrl()
    {
        return $this->getHttpServer() . $this->getShopDirectory();
    }
    
    
    /**
     * Returns the shop key of the shop.
     *
     * @return string
     */
    public function getShopKey()
    {
        return defined('GAMBIO_SHOP_KEY') ? GAMBIO_SHOP_KEY : '';
    }
    
    
    /**
     * Returns the default language of the shop.
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        return defined('DEFAULT_LANGUAGE') ? DEFAULT_LANGUAGE : 'en';
    }
    
    
    /**
     * Returns the timeout time for curl requests regarding the Gambio Hub.
     *
     * @return array|bool|null
     */
    public function getGambioHubCurlTimeout()
    {
        return gm_get_conf('GAMBIO_HUB_CURL_TIMEOUT');
    }
    
    
    /**
     * Returns the URL for curl requests to the Gambio Hub.
     *
     * @return string
     */
    public function getGambioHubUrl()
    {
        return defined('MODULE_PAYMENT_GAMBIO_HUB_URL') ? MODULE_PAYMENT_GAMBIO_HUB_URL : '';
    }
    
    
    /**
     * Returns the URL for curl requests to Gambio Hub Config.
     *
     * @return string
     */
    public function getGambioHubConfigUrl()
    {
        return 'https://config-api.gambiohub.com/a/api.php/api/v1';
    }
    
    
    /**
     * Returns the hub client key of the shop.
     *
     * @return string
     */
    public function getHubClientKey()
    {
        return gm_get_conf('GAMBIO_HUB_CLIENT_KEY');
    }
}