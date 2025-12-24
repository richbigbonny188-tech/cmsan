<?php
/* --------------------------------------------------------------
   CreateSSORegistreeController.inc.php 2017-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CreateSSORegistreeController extends CreateSSORegistreeController_parent
{
    /**
     * @param KeyValueCollection $customerCollection
     * @param KeyValueCollection $errorMessageCollection
     * @param bool               $p_process
     *
     * @return string
     */
    protected function _getMainContentHtml(
        KeyValueCollection $customerCollection = null,
        KeyValueCollection $errorMessageCollection = null,
        $p_process = false
    ) {
        $moduleInstalled = (bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED');
        if ($moduleInstalled === true) {
            if (isset($_SESSION['ssoData'], $_SESSION['ssoData']['customer_collection'])) {
                $customerCollectionArray           = $customerCollection
                                                     !== null ? $customerCollection->getArray() : [];
                $customerCollectionArray['useSso'] = true;
                $combinedCustomerCollection        = array_merge($customerCollectionArray,
                                                                 $_SESSION['ssoData']['customer_collection']);
                $customerCollection                = new KeyValueCollection($combinedCustomerCollection);
            }
        }
        $mainContent = parent::_getMainContentHtml($customerCollection, $errorMessageCollection, $p_process);
        
        return $mainContent;
    }
}
