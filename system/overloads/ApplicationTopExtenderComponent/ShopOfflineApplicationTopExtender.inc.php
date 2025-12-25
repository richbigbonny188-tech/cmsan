<?php
/* --------------------------------------------------------------
   ShopOfflineApplicationTopExtender.inc.php 2022-06-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('ShopOfflinePageHelper');

class ShopOfflineApplicationTopExtender extends ShopOfflineApplicationTopExtender_parent
{
    /**
     * Proceed.
     */
    public function proceed()
    {
        parent::proceed();
        
        if (ShopOfflinePageHelper::pageRebuildNeeded()) {
            ShopOfflinePageHelper::rebuildShopOfflinePage();
        }
        
        if ($this->toShowOfflineMessage()) {
            header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
            header('Retry-After: 3600');
            header('Cache-Control: no-cache');
            header('Content-Type: text/html; charset=' . $_SESSION['language_charset']);
            header('x-maintenance-mode: true');
            echo $this->getOfflinePageHtml();
            exit;
        }
        
        define('SHOP_OFFLINE', !$this->releaseInfoMatchInstalledVersion());
    }
    
    
    /**
     * @return bool
     */
    protected function toShowOfflineMessage()
    {
        return !$this->userLoggedIn()
               && $this->shopIsOffline()
               && !$this->attemptLogin()
               && !$this->updaterRequest()
               && !$this->cssRequest()
               && !$this->gambioHubRequest()
               && !$this->gambioStoreRequest()
               && !$this->cronJobRequest()
               && !$this->apiRequest()
               && !$this->callbackRequest();
    }
    
    
    /**
     * @return string
     */
    protected function getGxShopVersion()
    {
        $gx_version = null;
        
        include DIR_FS_CATALOG . 'release_info.php';
        
        return $gx_version;
    }
    
    
    /**
     * @return string|null
     */
    protected function getInstalledShopVersion()
    {
        // do not use gm_get_conf() to avoid caching problems
        $query  = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "gm_configuration/INSTALLED_VERSION" LIMIT 1';
        $result = xtc_db_query($query);
        
        if (1 === xtc_db_num_rows($result)) {
            $row              = xtc_db_fetch_array($result);
            $installedVersion = $row['value'];
        }
        
        return isset($installedVersion) ? $installedVersion : null;
    }
    
    
    /**
     * @return bool
     */
    protected function shopIsOffline()
    {
        return 'checked' === gm_get_conf('GM_SHOP_OFFLINE');
    }
    
    
    /**
     * @return bool
     */
    protected function userLoggedIn()
    {
        return '0' === $_SESSION['customers_status']['customers_status_id'];
    }
    
    
    /**
     * @return bool
     */
    protected function releaseInfoMatchInstalledVersion()
    {
        return $this->getGxShopVersion() === $this->getInstalledShopVersion();
    }
    
    
    /**
     * Checks if login is attempted.
     *
     * @return bool True if login is attempted.
     */
    protected function attemptLogin()
    {
        return isset($_POST['email_address'], $_POST['password'])
               || isset($_POST['2fa_token'], $_POST['cid'], $_POST['cemail'], $_POST['cpassword']);
    }
    
    
    /**
     * Returns true if the current request comes from GambioHub e.g (shop.php?do=shopinfo).
     *
     * @return bool
     */
    protected function gambioHubRequest()
    {
        if ('shop.php' !== basename(gm_get_env_info('SCRIPT_NAME'))) {
            return false;
        }
        
        $hubRequest = (string)array_search('shopinfo', array_map('strtolower', $_GET), true);
        
        return !empty($hubRequest);
    }
    
    
    /**
     * Returns true if the current request comes from stylesheet request.
     *
     * @return bool
     */
    protected function cssRequest()
    {
        if ('dynamic_theme_style.css.php' !== basename(gm_get_env_info('SCRIPT_NAME'))) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Returns true if the current request comes from updater request.
     *
     * @return bool
     */
    protected function updaterRequest()
    {
        if (strpos(gm_get_env_info('SCRIPT_NAME'),'gambio_updater/request_port.php') === false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @return string
     */
    protected function getOfflinePageHtml()
    {
        return ShopOfflinePageHelper::getShopOfflineHtml();
    }
    
    
    /**
     * @return bool
     */
    protected function gambioStoreRequest()
    {
        if ('shop.php' !== basename(gm_get_env_info('SCRIPT_NAME'))) {
            return false;
        }
        
        return isset($_GET['do']) && $_GET['do'] === 'GambioStoreCallback/verify';
    }
	
	
	/**
	 * @return bool
	 */
    protected function apiRequest()
	{
		return 'api.php' === basename(gm_get_env_info('SCRIPT_NAME'));
	}
    
    
    /**
     * @return bool
     */
	protected function cronJobRequest(): bool
    {
        $isShopPhpScript = basename(gm_get_env_info('SCRIPT_NAME')) === 'shop.php';
        
        if ($isShopPhpScript && isset($_GET['do'])) {
            
            return 0 === strpos($_GET['do'], 'Cronjob');
        }
        
        return false;
    }


    /**
     * @return bool
     */
    protected function callbackRequest(): bool
    {
        return 'magnaCallback.php' === basename(gm_get_env_info('SCRIPT_NAME'));
    }
}
