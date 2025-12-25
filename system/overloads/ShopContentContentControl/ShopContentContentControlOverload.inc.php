<?php
/* --------------------------------------------------------------
  ShopContentContentControlOverload.php 2018-05-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2018 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class ShopContentContentControlOverload
 *
 * This overload is mainly used, in order to create agreement data and store it in the database.
 * That data contains information about the confirmed agreement of the customer like the customers name and email.
 */
class ShopContentContentControlOverload extends ShopContentContentControlOverload_parent
{
	protected function process_contact_us()
	{
		// the response is a boolean true, if no error happened. Else the response contains a text
		$response = parent::process_contact_us();
		
		if($response === true)
		{
			$languageId = new IdType($_SESSION['languages_id']);
			$configKey  = new NonEmptyStringType('LOG_IP_CONTACT');
			
			$agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
			$agreementCustomer     = $agreementWriteService->createCustomer(new StringType($this->name),
			                                                                new CustomerEmail($this->email_address));
			
			AgreementStoreHelper::store($languageId, LegalTextType::PRIVACY, $agreementCustomer, $configKey);
		}
		
		return $response;
	}
}