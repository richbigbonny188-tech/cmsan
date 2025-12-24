<?php

/* --------------------------------------------------------------
   TwoFactorAuthLoginContentControl.inc.php 2023-06-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Overloading class that extends the login content control
 */
class TwoFactorAuthLoginContentControl extends TwoFactorAuthLoginContentControl_parent
{
    /**
     * Redirect to the token prompt page
     */
    public function proceed()
    {
        $alreadyConfirmed = isset($_POST[TwoFactorAuthLoginControllerConfiguration::TOKEN_POST_KEY]);
        
        if ($this->isUpdateInProgress()) {
            return parent::proceed();
        }
        
        if (gm_get_conf('GM_SHOP_OFFLINE') === 'checked') {
            return parent::proceed();
        }
        
        if (!$this->isModuleInstalled()) {
            return parent::proceed();
        }
        
        if ($alreadyConfirmed) {
            return parent::proceed();
        }
        
        $twoFactorAuthServiceFactory = MainFactory::create('TwoFactorAuthServiceFactory');
        
        try {
            $emailAddress = new EmailAddress($_POST['email_address'] ?? null);
            $password     = new NonEmptyStringType($_POST['password'] ?? null);
            $customerId   = $this->customerIdFromEmailAddress($emailAddress);
            
            $isUsing2fa = $twoFactorAuthServiceFactory->read()->usageStatusForCustomer($customerId);
            
            if (!$isUsing2fa) {
                return parent::proceed();
            }
            $getParams = http_build_query($_GET);
            $this->assignSessionVariables($emailAddress, $password, $customerId)->set_redirect_url(DIR_WS_CATALOG
                                                                                                   . TwoFactorAuthLoginControllerConfiguration::TOKEN_PROMPT_URL
                                                                                                   . '&' . $getParams);
        } catch (Exception $exception) {
            return parent::proceed();
        }
    }
    
    
    /**
     * Assign session variables
     *
     * @param EmailAddress       $emailAddress Customer email address
     * @param NonEmptyStringType $password     Customer password
     * @param IdType             $customerId   Customer ID
     *
     * @return $this Same instance
     */
    protected function assignSessionVariables(
        EmailAddress $emailAddress,
        NonEmptyStringType $password,
        IdType $customerId
    ) {
        $_SESSION[TwoFactorAuthLoginControllerConfiguration::USER_SESSION_KEY]        = (string)$emailAddress;
        $_SESSION[TwoFactorAuthLoginControllerConfiguration::PASSWORD_SESSION_KEY]    = $password->asString();
        $_SESSION[TwoFactorAuthLoginControllerConfiguration::CUSTOMER_ID_SESSION_KEY] = $customerId->asInt();
        
        return $this;
    }
    
    
    /**
     * Return the customer ID found by the provided email address
     *
     * @param EmailAddress $emailAddress Email address
     *
     * @return IdType Customer ID
     */
    protected function customerIdFromEmailAddress(EmailAddress $emailAddress)
    {
        /**
         * @var CustomerService $customerService Customer service
         */
        
        $customerService = StaticGXCoreLoader::getService('Customer');
        $customers       = $customerService->filterCustomers(['customers_email_address' => $emailAddress]);
        $customerId      = false;
        
        if (count($customers) === 1) {
            $customerId = array_pop($customers)->getId();
        }
        
        return new IdType($customerId);
    }
    
    
    /**
     * Return whether the module is installed
     *
     * @return bool Module installed status
     */
    protected function isModuleInstalled()
    {
        return (bool)gm_get_conf('MODULE_CENTER_TWOFACTORAUTH_INSTALLED');
    }
    
    
    /**
     * Return whether the module is installed
     *
     * @return bool Module installed status
     */
    protected function isUpdateInProgress()
    {
        // do not use gm_get_conf() to avoid caching problems
        $query  = 'SELECT `value` FROM `gx_configurations` WHERE `key` = "gm_configuration/INSTALLED_VERSION" LIMIT 1';
        $result = xtc_db_query($query);
        if (xtc_db_num_rows($result) == 1) {
            $row              = xtc_db_fetch_array($result);
            $installedVersion = $row['value'];
        } else {
            $installedVersion = '';
        }
        
        include(DIR_FS_CATALOG . 'release_info.php');
        if ($gx_version != $installedVersion) {
            return true;
        }
        
        return false;
    }
}