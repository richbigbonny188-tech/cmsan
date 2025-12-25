<?php

/* --------------------------------------------------------------
   TwoFactorAuthLoginControllerConfiguration.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a configuration container for the login front-end controller
 */
class TwoFactorAuthLoginControllerConfiguration
{
    /**
     * Path to template directory
     */
    const TEMPLATE_DIRECTORY = TwoFactorAuthControllerConfiguration::TEMPLATE_DIRECTORY;
    
    /**
     * Token prompt URL
     */
    const TOKEN_PROMPT_URL = 'shop.php?do=TwoFactorAuthLogin';
    
    /**
     * Confirm URL
     */
    const CONFIRM_URL = 'shop.php?do=TwoFactorAuthLogin/Confirm';
    
    /**
     * Confirm URL method
     */
    const CONFIRM_URL_METHOD = 'POST';
    
    /**
     * Message stack class name
     */
    const MESSAGE_STACK_CLASS_NAME = 'login';
    
    /**
     * User session key
     */
    const USER_SESSION_KEY = 'twofactorauth_login_user';
    
    /**
     * Password session key
     */
    const PASSWORD_SESSION_KEY = 'twofactorauth_login_password';
    
    /**
     * Customer ID session key
     */
    const CUSTOMER_ID_SESSION_KEY = 'twofactorauth_login_customer_id';
    
    /**
     * POST array key name for token confirmation
     */
    const TOKEN_POST_KEY = 'token';
    
    /**
     * Account settings URL
     */
    const ACCOUNT_SETTINGS_URL = TwoFactorAuthControllerConfiguration::ACCOUNT_SETTINGS_URL;
    
    /**
     * Email address POST key
     */
    const EMAIL_POST_KEY = 'email_address';
    
    /**
     * Password POST key
     */
    const PASSWORD_POST_KEY = 'password';
}