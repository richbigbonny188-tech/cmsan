<?php
/* --------------------------------------------------------------
   TwoFactorAuthAccountThemeContentView.inc.php 2018-11-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Overloading class that extends the account content view
 */
class TwoFactorAuthAccountThemeContentView extends TwoFactorAuthAccountThemeContentView_parent
{
    /**
     * Add additional data
     */
    public function prepare_data()
    {
        parent::prepare_data();
        
        $isModuleInstalled = (bool)gm_get_conf('MODULE_CENTER_TWOFACTORAUTH_INSTALLED');
        
        $data = [
            'is_installed'      => $isModuleInstalled,
            'configuration_url' => DIR_WS_CATALOG . 'shop.php?do=TwoFactorAuth'
        ];
        
        $this->set_content_data('twofactorauth_data', $data);
    }
}
