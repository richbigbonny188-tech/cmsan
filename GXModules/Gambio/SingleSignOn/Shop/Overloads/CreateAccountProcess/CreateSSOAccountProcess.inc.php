<?php
/* --------------------------------------------------------------
   CreateSSOAccountProcess.inc.php 2017-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CreateSSOAccountProcess extends CreateSSOAccountProcess_parent
{
    public function proceedRegistree(KeyValueCollection $customerCollection, GMLogoManager $logoManager)
    {
        $rc              = parent::proceedRegistree($customerCollection, $logoManager);
        $moduleInstalled = (bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED');
        
        if ($moduleInstalled === true) {
            if (isset($_SESSION['customer_id'], $_SESSION['ssoData'])
                && $_SESSION['customers_status']['customers_status_id'] !== DEFAULT_CUSTOMERS_STATUS_ID_GUEST) {
                $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
                $db->replace('customers_sso',
                             [
                                 'customers_id' => $_SESSION['customer_id'],
                                 'issuer'       => $_SESSION['ssoData']['iss'],
                                 'subject'      => $_SESSION['ssoData']['sub'],
                             ]);
                
                if ($_SESSION['ssoData']['iss'] === 'amazon.com') {
                    $tokensData = $_SESSION['ssoData']['tokens'];
                    setcookie('amazon_Login_accessToken', $tokensData['access_token'], 0, '/', '', true);
                    setcookie('amazon_Login_state_cache', '');
                }
            }
        }
        
        return $rc;
    }
}
