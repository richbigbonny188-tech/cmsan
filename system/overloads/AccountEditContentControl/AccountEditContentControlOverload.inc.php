<?php
/* --------------------------------------------------------------
  AccountEditContentControlOverload.inc.php 2018-05-16
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2018 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class AccountEditContentControlOverload
 *
 * This overload is mainly used, in order to create agreement data and store it in the database.
 * That data contains information about the confirmed agreement of the customer like the customers name and email.
 */
class AccountEditContentControlOverload extends AccountEditContentControlOverload_parent
{
	protected function process()
	{
		parent::process();
		
		if($this->error === false)
		{
			$languageId = new IdType($_SESSION['languages_id']);
			$configKey  = new NonEmptyStringType('LOG_IP_ACCOUNT_CONTACT');
			
			$agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
			$agreementCustomer     = $agreementWriteService->createCustomer(new StringType($this->v_data_array['POST']['firstname']
			                                                                               . ' '
			                                                                               . $this->v_data_array['POST']['lastname']),
			                                                                new CustomerEmail($this->v_data_array['POST']['email_address']));
			
			AgreementStoreHelper::store($languageId, LegalTextType::PRIVACY, $agreementCustomer, $configKey);
		}
	}
}