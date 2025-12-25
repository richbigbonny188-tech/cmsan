<?php

/* --------------------------------------------------------------
	DSGVOWithdrawalControl.inc.php 2022-07-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2022 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * Class representing the withdrawal control overload for DSGVO
 */
class DSGVOWithdrawalControl extends DSGVOWithdrawalControl_parent {
    /**
     * Save the withdrawal data
     * @param array $data Withdrawal data
     */
    public function save_withdrawal(array $data) {
        /**
         * @var $agreementWriteService AgreementWriteService
         */

        parent::save_withdrawal($data);
    
        $errors = $this->validate_form($data);
        
        if(!is_array($errors) && empty($errors) === true)
        {
            $languageId = new IdType($_SESSION['languages_id']);
            $configKey = new NonEmptyStringType('LOG_IP_WITHDRAWAL_WEB_FORM');
    
            $agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
    
            $customerName = new StringType($data['customer_firstname'] . ' ' . $data['customer_lastname']);
            $customerEmail = MainFactory::create('CustomerEmail', $data['customer_email']);
    
            $agreementCustomer = $agreementWriteService->createCustomer($customerName, $customerEmail);
    
            AgreementStoreHelper::store(
                $languageId,
                LegalTextType::PRIVACY,
                $agreementCustomer,
                $configKey
            );
        }
    }
    
    
    protected function set_form_data($p_withdrawal_data = null)
    {
        parent::set_form_data($p_withdrawal_data);
        
        if(gm_get_conf('GM_SHOW_PRIVACY_WITHDRAWAL_WEB_FORM') === '1')
        {
            $this->withdrawal_contentview->set_content_data('show_privacy', 1);
            $this->withdrawal_contentview->set_content_data('privacy_link', gm_get_privacy_link('GM_SHOW_PRIVACY_WITHDRAWAL_WEB_FORM'));
            $this->withdrawal_contentview->set_content_data('show_privacy_checkbox', gm_get_conf('PRIVACY_CHECKBOX_WITHDRAWAL_WEB_FORM'));
        }
    }
    
    
    protected function validate_form(array $p_withdrawal_data)
    {
        $t_error_array = parent::validate_form($p_withdrawal_data);
        
        if(gm_get_conf('GM_SHOW_PRIVACY_WITHDRAWAL_WEB_FORM') === '1'
           && gm_get_conf('PRIVACY_CHECKBOX_WITHDRAWAL_WEB_FORM') === '1'
           && ($p_withdrawal_data['privacy_accepted'] ?? null) !== '1')
        {
            $t_error_array['privacy_accepted'] = '__ERROR__';
        }
        
        return $t_error_array;
    }
}