<?php

/* --------------------------------------------------------------
   DirectHelpManualAdminLayoutHttpControllerResponse.inc.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the admin layout controller response overload for direct help
 */
class DirectHelpManualAdminLayoutHttpControllerResponse extends DirectHelpManualAdminLayoutHttpControllerResponse_parent
{
    protected function _setInitialMessages()
    {
        parent::_setInitialMessages();
        
        $configStorage = MainFactory::create('DirectHelpConfigurationStorage');
        
        $scripts = $this->contentView->get_content_array()['dynamic_script_assets'] ?? '';
        $styles  = $this->contentView->get_content_array()['dynamic_style_assets'] ?? '';
        
        $assetsBase    = DirectHelpConfiguration::ASSETS_BASE;
        $assetsPostfix = file_exists(DIR_FS_CATALOG . '.dev-environment') ? '' : '.min';
        $scriptAsset   = "{$assetsBase}/Admin/Javascript/extenders/online_manual{$assetsPostfix}.js";
        $styleAsset    = "{$assetsBase}/Admin/Styles/online_manual{$assetsPostfix}.css";
        
        $proxyUrl = DirectHelpConfiguration::PROXY_URL;
        
        if ($configStorage->isModuleActive()) {
            $script = $scripts . PHP_EOL . "<script data-url='{$proxyUrl}' src='{$scriptAsset}'></script>";
            $style  = $styles . PHP_EOL . "<link rel='stylesheet' href='{$styleAsset}'/>";
            
            $this->contentView->set_content_data('dynamic_script_assets', $script);
            $this->contentView->set_content_data('dynamic_style_assets', $style);
        }
    }
}