<?php

/* --------------------------------------------------------------
   TwoFactorAuthThemeContentView.inc.php 2018-12-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization front-end content view
 */
class TwoFactorAuthThemeContentView extends ThemeContentView
{
    /**
     * TwoFactorAuthThemeContentView constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->set_template_dir(DIR_FS_CATALOG . TwoFactorAuthLoginControllerConfiguration::TEMPLATE_DIRECTORY);
        $this->set_flat_assigns(true);
        $this->set_caching_enabled(false);
        $this->set_content_data('alert_message_template_path', $this->alertMessageTemplatePath());
    }
    
    
    /**
     * Return the alert message template path
     *
     * @return string
     */
    protected function alertMessageTemplatePath()
    {
        return DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemeHtmlPath() . 'alert_message.html';
    }
}