<?php
/* --------------------------------------------------------------
   UpdaterRedirectApplicationTopPrimalExtender.inc.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class UpdaterRedirectApplicationTopPrimalExtender extends UpdaterRedirectApplicationTopPrimalExtender_parent
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
        $customerStatus = $_SESSION['customers_status'] ?? [];
        if ($gx_version != $installedVersion && ($customerStatus['customers_status_id'] ?? '') === '0'
            && $this->isRedirectAllowed()) {
            $redirectUrl = DIR_WS_CATALOG . 'gambio_updater';
            if (ENABLE_SSL === true) {
                $redirectUrl = HTTPS_SERVER . $redirectUrl;
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
        if (basename(gm_get_env_info('SCRIPT_NAME')) === 'gm_javascript.js.php'
            || strpos(gm_get_env_info('REQUEST_URI'), '/gambio_updater') !== false) {
            return false;
        }
        
        return true;
    }
}