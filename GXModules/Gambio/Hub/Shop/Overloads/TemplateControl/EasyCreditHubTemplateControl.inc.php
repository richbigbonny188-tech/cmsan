<?php
/* --------------------------------------------------------------
   EasyCreditHubTemplateControl.inc.php 2020-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class EasyCreditHubTemplateControl extends EasyCreditHubTemplateControl_parent
{
    public function findSettingValueByName($p_settingName)
    {
        $shopId = gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_EASYCREDITHUB_SHOPID');
        if (!empty($shopId)
            && $p_settingName === 'gx-product-info-details-sticky'
            && strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) === 'true') {
            return false;
        }
        
        return parent::findSettingValueByName($p_settingName);
    }
}
