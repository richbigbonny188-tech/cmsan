<?php

class CheckoutConfirmationContentControlOverload extends CheckoutConfirmationContentControlOverload_parent
{
	public function proceed()
	{
		$error    = '';
		$response = parent::proceed();
		
		$transportConditionsSuccess = $this->_checkTransportConditions($error);
		
		if(!$this->get_redirect_url())
		{
			if($transportConditionsSuccess && gm_get_conf('DATA_TRANSFER_TO_TRANSPORT_COMPANIES_STATUS')
			   && $this->checkConditions()
			   && (($this->v_data_array['POST']['accept_transport_conditions'] ?? null) === '1' ||
			       (isset($_SESSION['transport_conditions']) && $_SESSION['transport_conditions'] === 'accepted')))
			{
				$languageId   = new IdType($_SESSION['languages_id']);
				$configKey    = new NonEmptyStringType('LOG_IP_SHIPPING');
				$customerName = new StringType($GLOBALS['order']->billing['firstname'] . ' '
				                               . $GLOBALS['order']->billing['lastname']);
				
				$agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
				$agreementCustomer     = $agreementWriteService->createCustomer($customerName,
				                                                                new CustomerEmail($GLOBALS['order']->customer['email_address']));
				
				AgreementStoreHelper::store($languageId, LegalTextType::TRANSPORT, $agreementCustomer, $configKey);
			}
		}
		return $response;
	}
	
	
	/**
	 * Returns whether the conditions are accepted, if activated.
	 *
	 * @return bool True, if conditions are activated and accepted
	 */
	private function checkConditions()
	{
		if(gm_get_conf('GM_CHECK_CONDITIONS') == 1 && $_REQUEST['conditions'] == false)
		{
			return false;
		}
		
		if(gm_get_conf('GM_CHECK_WITHDRAWAL') == 1 && $this->v_data_array['POST']['withdrawal'] == false)
		{
			return false;
		}
		
		if((gm_get_conf('GM_CHECK_WITHDRAWAL') == 1 && $this->v_data_array['POST']['withdrawal'] == false)
		   && (gm_get_conf('GM_CHECK_CONDITIONS') == 1 && $_REQUEST['conditions'] == false))
		{
			return false;
		}
		
		return true;
	}
}
