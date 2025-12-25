<?php
/* --------------------------------------------------------------
   UpdaterRedirectAdminApplicationTopPrimalExtender.inc.php 2021-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class UpdaterRedirectAdminApplicationTopPrimalExtender extends UpdaterRedirectAdminApplicationTopPrimalExtender_parent
{
    public function proceed()
    {
        parent::proceed();
        
        // do not use gm_get_conf() to avoid caching problems
        $query  = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "gm_configuration/INSTALLED_VERSION" LIMIT 1';
        $result = xtc_db_query($query);
        if (xtc_db_num_rows($result) == 1) {
            $row              = xtc_db_fetch_array($result);
            $installedVersion = $row['value'];
        } else {
            $installedVersion = '';
        }
    
        $gx_version = include DIR_FS_CATALOG . 'release_info.php';
        if ($gx_version !== $installedVersion && $_SESSION['customers_status']['customers_status_id'] === '0'
            && $this->isRedirectAllowed()) {
            $redirectUrl = DIR_WS_CATALOG . 'gambio_updater';
            if (ENABLE_SSL_CATALOG === 'true' || ENABLE_SSL_CATALOG === true) {
                $redirectUrl = HTTPS_CATALOG_SERVER . $redirectUrl;
            }
            header("HTTP/1.1 302 Found");
            header('Cache-Control: no-cache');
            xtc_redirect($redirectUrl);
        }
    }
    
    
    /**
     * @return bool
     */
    protected function isRedirectAllowed(): bool
    {
        // no redirect during database restore
        if (isset($this->v_data_array['GET']['module'])
            && ($this->v_data_array['GET']['module'] === 'DBBackup'
                || $this->v_data_array['GET']['module'] === 'LightboxPluginAdmin')) {
            return false;
        }
        
        // no redirect during database restore
        if (isset($this->v_data_array['GET']['section'])
            && $this->v_data_array['GET']['section'] === 'db_backup_restore') {
            return false;
        }
        
        if (basename(gm_get_env_info('SCRIPT_NAME')) === 'backup.php') {
            return false;
        }
        
        if (basename(gm_get_env_info('SCRIPT_NAME')) === 'request_port.php') {
            return false;
        }
        
        return true;
    }
}