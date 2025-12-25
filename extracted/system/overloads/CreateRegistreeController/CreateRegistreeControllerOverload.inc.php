<?php
/* --------------------------------------------------------------
   CreateRegistreeControllerOverload.inc.php 2024-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2024 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpViewController');

/**
 * Class CreateRegistreeControllerOverload
 *
 * This overload is mainly used, in order to create agreement data and store it in the database.
 * That data contains information about the confirmed agreement of the customer like the customers name and email.
 */
class CreateRegistreeControllerOverload extends CreateRegistreeControllerOverload_parent
{
	public function actionProceed()
	{
		$httpResponse = parent::actionProceed();
		
		if($httpResponse instanceof RedirectHttpControllerResponse)
		{
            if($this instanceof CreateRegistreeController && $this->forceNextRedirect === true)
            {
                $this->forceNextRedirect = false;
                return $httpResponse;
            }
            
			try
			{
				$inputTransformer = MainFactory::create('CustomerInputToCollectionTransformer');
				/** * @var CountryService $countryService */
				$countryService     = StaticGXCoreLoader::getService('Country');
				$customerCollection = $inputTransformer->getRegistreeCollectionFromInputArray($this->_getPostDataCollection()
				                                                                                   ->getArray(),
				                                                                              $countryService);
				if(!isset($_SESSION['customer_id']))
				{
					throw new InvalidCustomerDataException('Key "customer_id" is not set in SESSION variable');
				}
				
				$queryBuilder           = StaticGXCoreLoader::getDatabaseQueryBuilder();
				$customerId             = $_SESSION['customer_id'];
				$customerServiceFactory = MainFactory::create(CustomerServiceFactory::class, $queryBuilder);
				$customerReadService    = $customerServiceFactory->createCustomerReadService();
				$customer               = $customerReadService->getCustomerById(new IdType((int)$customerId));
				$customerName           = $customer->getFirstname() . ' ' . $customer->getLastname();
				$customerEmail          = $customer->getEmail();
				
				$languageId = new IdType($_SESSION['languages_id']);
				$configKey  = new NonEmptyStringType('GM_LOG_IP_LOGIN');
				
				$agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
				$agreementCustomer     = $agreementWriteService->createCustomer(new StringType($customerName),
				                                                                $customerEmail);
				
				AgreementStoreHelper::store($languageId, LegalTextType::PRIVACY, $agreementCustomer, $configKey);
                
                $cookieContent = gm_get_content('GM_COOKIE_CONTENT', $_SESSION['languages_id']);
                $cookieStatus = gm_get_conf('GM_COOKIE_STATUS');
                
                if($cookieContent && $cookieContent !== '' && ($cookieStatus === true || $cookieStatus === 'true') && $_COOKIE['hideCookieBar'] === 'true')
				{
					AgreementStoreHelper::store($languageId, LegalTextType::COOKIE, $agreementCustomer, $configKey);
				}
				
				return $httpResponse;
			}
			catch(InvalidCustomerDataException $exception)
			{
				return $this->_getResponse($this->_getMainContentHtml($customerCollection,
				                                                      $exception->getErrorMessageCollection(), true));
			}
		}
		
		return $httpResponse;
	}
}