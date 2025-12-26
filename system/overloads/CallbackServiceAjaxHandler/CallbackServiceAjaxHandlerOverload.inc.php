<?php
/* --------------------------------------------------------------
   CallbackServiceAjaxHandler.inc.php 2016-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CallbackServiceAjaxHandlerOverload
 *
 * This overload is mainly used, in order to create agreement data and store it in the database.
 * That data contains information about the confirmed agreement of the customer like the customers name and email.
 */
class CallbackServiceAjaxHandlerOverload extends CallbackServiceAjaxHandlerOverload_parent
{
	function proceed()
	{
		$success = parent::proceed();
		
		$t_action_request = $this->v_data_array['GET']['action'];
		
		if($t_action_request === 'send')
		{
			$languageId      = new IdType($_SESSION['languages_id']);
			$configKey       = new NonEmptyStringType('LOG_IP_CALLBACK');
			$name            = xtc_db_prepare_input($this->v_data_array['POST']['name']);
			$email           = xtc_db_prepare_input($this->v_data_array['POST']['email']) ? : '';
			$telephone       = xtc_db_prepare_input($this->v_data_array['POST']['telephone']);
			$nameOrTelephone = $name ? : $telephone;
			
			$agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
			$agreementCustomer     = $agreementWriteService->createCustomer(new StringType($nameOrTelephone),
			                                                                new AgreementCustomerEmail($email));
			
			AgreementStoreHelper::store($languageId, LegalTextType::PRIVACY, $agreementCustomer, $configKey);
		}
		
		return $success;
	}
}