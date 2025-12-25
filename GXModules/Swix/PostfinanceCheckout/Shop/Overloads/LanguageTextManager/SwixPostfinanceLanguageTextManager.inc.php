<?php
/* --------------------------------------------------------------
   SwixPostfinanceLanguageTextManager.inc.php 2021-07-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SwixPostfinanceLanguageTextManager extends SwixPostfinanceLanguageTextManager_parent
{
    protected function _initMappingArray()
    {
        parent::_initMappingArray();
        
        $modules = ['creditdebitcard', 'paypal', 'postfinancecard', 'postfinanceefinance', 'powerpay', 'twint'];
        
        if (isset(self::$languages)) {
            foreach (self::$languages as $language) {
                foreach ($modules as $module) {
                    self::$sectionMappings['lang/' . $language['directory']
                                           . "/modules/payment/swixpostfinancecheckout_{$module}.php"] = "swixpostfinancecheckout_{$module}";
                }
            }
        } else {
            foreach ($this->languages as $language) {
                foreach ($modules as $module) {
                    $this->sectionMappings['lang/' . $language['directory']
                                           . "/modules/payment/swixpostfinancecheckout_{$module}.php"] = "swixpostfinancecheckout_{$module}";
                }
            }
        }
    }
}
