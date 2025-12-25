<?php

/* --------------------------------------------------------------
	DSGVOAddressBookProcessContentControl.inc.php 2018-05-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class representing the address book process content view overload for DSGVO
 */
class DSGVOAddressBookProcessContentControl extends DSGVOAddressBookProcessContentControl_parent
{
    /**
     * Process dataset update or new dataset creation
     */
    protected function process_update()
    {
        $isSuccessful = parent::process_update();

        if (!$isSuccessful) {
            return;
        }

        $customerId = new IdType($this->coo_address->get_('customers_id'));
        $customerName = new StringType($this->coo_address->get_('entry_firstname') . ' ' . $this->coo_address->get_('entry_lastname'));
        $customerEmail = StaticGXCoreLoader::getService('CustomerRead')->getCustomerById($customerId)->getEmail();

        $agreementCustomer = StaticGXCoreLoader::getService('AgreementWrite')->createCustomer(
            $customerName,
            $customerEmail
        );

        AgreementStoreHelper::store(
            new IdType($_SESSION['languages_id']),
            LegalTextType::PRIVACY,
            $agreementCustomer,
            new NonEmptyStringType('LOG_IP_ACCOUNT_ADDRESS_BOOK')
        );
    }
}