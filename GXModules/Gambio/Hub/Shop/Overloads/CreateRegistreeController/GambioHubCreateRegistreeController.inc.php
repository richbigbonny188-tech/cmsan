<?php
/* --------------------------------------------------------------
   GambioHubCreateRegistreeController.inc.php 2018-11-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubCreateRegistreeController extends GambioHubCreateRegistreeController_parent
{
    /**
     * @param KeyValueCollection $customerCollection
     * @param KeyValueCollection $errorMessageCollection
     * @param bool               $p_process
     *
     * @return string
     */
    protected function _getMainContentHtml(KeyValueCollection $customerCollection = null,
        KeyValueCollection $errorMessageCollection = null,
        $p_process = false)
    {
        if(!empty($_SESSION['paypalCustomerCollection']))
        {
            $customerCollectionArray    = $customerCollection !== null ? $customerCollection->getArray() : [];
            $combinedCustomerCollection = array_merge(
                $customerCollectionArray,
                $_SESSION['paypalCustomerCollection']
            );
            $customerCollection         = new KeyValueCollection($combinedCustomerCollection);
        }
        $mainContent = parent::_getMainContentHtml($customerCollection, $errorMessageCollection, $p_process);
        return $mainContent;
    }
}
