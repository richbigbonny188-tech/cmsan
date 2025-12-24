<?php
/* --------------------------------------------------------------
   ProductQuestionControllerOverload.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductQuestionControllerOverload
 *
 * This overload is mainly used, in order to create agreement data and store it in the database.
 * That data contains information about the confirmed agreement of the customer like the customers name and email.
 */
class ProductQuestionControllerOverload extends ProductQuestionControllerOverload_parent
{
	function actionSend()
	{
		$httpResponse = parent::actionSend();
		
		$languageId = new IdType($_SESSION['languages_id']);
		$configKey  = new NonEmptyStringType('LOG_IP_TELL_A_FRIEND');
		
		$agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
		$agreementCustomer     = $agreementWriteService->createCustomer(new StringType($_POST['name']),
		                                                                new CustomerEmail($_POST['email']));
		
		AgreementStoreHelper::store($languageId, LegalTextType::PRIVACY, $agreementCustomer, $configKey);
		
		return $httpResponse;
	}
}