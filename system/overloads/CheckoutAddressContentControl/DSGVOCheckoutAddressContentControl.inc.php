<?php

/* --------------------------------------------------------------
	DSGVOCheckoutAddressContentControl.inc.php 2018-05-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class representing the checkout address content control overload for DSGVO
 */
class DSGVOCheckoutAddressContentControl extends DSGVOCheckoutAddressContentControl_parent
{
	protected function process_new_address()
	{
		/**
		 * @var $agreementWriteService AgreementWriteService
		 * @var $customerReadService   CustomerReadService
		 * @var $address               AddressModel
		 */
		
		$value = parent::process_new_address();
		
		if($this->error)
		{
			return $value;
		}
		
		$languageId = new IdType($_SESSION['languages_id']);
		$configKey  = '';
		
		if($this->page_type === 'shipping')
		{
			$configKey = new NonEmptyStringType('LOG_IP_ORDER_SHIPPING_ADDRESS');
		}
		elseif($this->page_type === 'payment')
		{
			$configKey = new NonEmptyStringType('LOG_IP_ORDER_PAYMENT_ADDRESS');
		}
		
		$agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
		$customerReadService   = StaticGXCoreLoader::getService('CustomerRead');
		
		$customerName  = new StringType($this->coo_address->get_('entry_firstname') . ' '
		                                . $this->coo_address->get_('entry_lastname'));
		$customerEmail = $customerReadService->getCustomerById(new IdType($this->customer_id))->getEmail();
		
		$agreementCustomer = $agreementWriteService->createCustomer($customerName, $customerEmail);
		
		AgreementStoreHelper::store($languageId, LegalTextType::PRIVACY, $agreementCustomer, $configKey);
		
		return $value;
	}
}