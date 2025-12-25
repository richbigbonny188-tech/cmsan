<?php
/* --------------------------------------------------------------
   LegalTextVersionHelper.inc.php 2018-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductReviewsWriteContentControlOverload
 *
 * This overload is mainly used, in order to create agreement data and store it in the database.
 * That data contains information about the confirmed agreement of the customer like the customers name and email.
 */
class ProductReviewsWriteContentControlOverload extends ProductReviewsWriteContentControlOverload_parent
{
	public function proceed()
	{
		$response = parent::proceed();
		
		$coo_captcha = MainFactory::create_object('Captcha');
		
		if(isset($this->v_data_array['GET']['action'])
		   && $this->v_data_array['GET']['action'] === 'process'
		   && $coo_captcha->is_valid($this->v_data_array['POST'], 'GM_REVIEWS_VVCODE')
		   && $this->v_data_array['POST']['review'] >= REVIEW_TEXT_MIN_LENGTH)
		{
			$customerName  = new StringType('');
			$customerEmail = new AgreementCustomerEmail('');
			if((int)$this->customerId > 0)
			{
				$customerServiceFactory = MainFactory::create(CustomerServiceFactory::class,
				                                              StaticGXCoreLoader::getDatabaseQueryBuilder());
				$customerReadService    = $customerServiceFactory->createCustomerReadService();
				$customer               = $customerReadService->getCustomerById(new IdType((int)$this->customerId));
				$customerName           = new StringType($customer->getFirstname() . ' ' . $customer->getLastname());
				$customerEmail          = $customer->getEmail();
			}
			
			$languageId = new IdType($_SESSION['languages_id']);
			$configKey  = new NonEmptyStringType('LOG_IP_REVIEWS');
			
			$agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
			$agreementCustomer     = $agreementWriteService->createCustomer($customerName, $customerEmail);
			
			AgreementStoreHelper::store($languageId, LegalTextType::PRIVACY, $agreementCustomer, $configKey);
		}
		
		return $response;
	}
}