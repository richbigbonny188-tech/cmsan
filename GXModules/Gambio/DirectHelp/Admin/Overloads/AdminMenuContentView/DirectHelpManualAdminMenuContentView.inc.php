<?php
/* --------------------------------------------------------------
   DirectHelpManualAdminMenuContentView.inc.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

/**
 * Class representing the legacy menu content view overload for direct help
 */
class DirectHelpManualAdminMenuContentView extends DirectHelpManualAdminMenuContentView_parent
{
    public function get_html()
    {
        $html = parent::get_html();
        
        $configStorage = MainFactory::create('DirectHelpConfigurationStorage');
        
        $assetsBase    = DirectHelpConfiguration::ASSETS_BASE;
        $assetsPostfix = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
        $scriptAsset   = "{$assetsBase}/Admin/Javascript/extenders/online_manual{$assetsPostfix}.js";
        $styleAsset    = "{$assetsBase}/Admin/Styles/online_manual{$assetsPostfix}.css";
        
        $proxyUrl = DirectHelpConfiguration::PROXY_URL;
        
        if ($configStorage->isModuleActive()) {
            $script = PHP_EOL . "<script data-url='{$proxyUrl}' src='{$scriptAsset}'></script>";
            $style  = PHP_EOL . "<link rel='stylesheet' href='{$styleAsset}' />";
            
            $html .= $script . $style;
        }
        
        return $html;
    }
}