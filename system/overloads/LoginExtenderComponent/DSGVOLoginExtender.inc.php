<?php

/* --------------------------------------------------------------
   DSGVOLoginExtender.inc.php 2018-05-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the login extender for DSGVO
 */
class DSGVOLoginExtender extends DSGVOLoginExtender_parent
{
    /**
     * Proceed
     */
    public function proceed()
    {
        /**
         * @var $agreementWriteService AgreementWriteService
         * @var $agreementReadService AgreementReadService
         * @var $customerReadService CustomerReadService
         */

        parent::proceed();
        
        $cookieContent = gm_get_content('GM_COOKIE_CONTENT', $_SESSION['languages_id']);
        $cookieStatus = gm_get_conf('GM_COOKIE_STATUS');

        if($cookieContent && $cookieContent !== '' && ($cookieStatus === true || $cookieStatus === 'true'))
        {
            $agreementWriteService = StaticGXCoreLoader::getService('AgreementWrite');
            $agreementReadService = StaticGXCoreLoader::getService('AgreementRead');
            $customerReadService = StaticGXCoreLoader::getService('CustomerRead');
    
            $customerId = new IdType($this->v_data_array['customers_id']);
            $languageId = new IdType($_SESSION['languages_id']);
            $configKey  = new NonEmptyStringType('GM_LOG_IP_LOGIN');
    
            $hasHiddenCookieBar = $_COOKIE['hideCookieBar'] === 'true';
            $cookieBarText = gm_get_content('GM_COOKIE_CONTENT', $languageId->asInt());
    
            $customer = $customerReadService->getCustomerById($customerId);
            $customerName = new StringType($customer->getFirstname() . ' ' . $customer->getLastname());
            $customerEmail = $customer->getEmail();
    
            $agreementsByCustomer = $agreementReadService->getAgreementsByCustomerEmail(new StringType($customerEmail->__toString()));
            $agreementCustomer = $agreementWriteService->createCustomer($customerName, $customerEmail);
    
            $hasAlreadyAgreedCookieBarText = false;
    
            foreach ($agreementsByCustomer->getIterator() as $agreement) {
                if ($agreement->getText()->asString() === $cookieBarText) {
                    $hasAlreadyAgreedCookieBarText = true;
                    break;
                }
            }
    
            if ($hasHiddenCookieBar && !$hasAlreadyAgreedCookieBarText) {
                AgreementStoreHelper::store($languageId, LegalTextType::COOKIE, $agreementCustomer, $configKey);
            }
        }
    }
}