<?php

/* --------------------------------------------------------------
   TwoFactorAuthControllerConfiguration.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a configuration container for the front-end controller
 */
class TwoFactorAuthControllerConfiguration
{
    /**
     * Path to template directory
     */
    const TEMPLATE_DIRECTORY = 'GXModules/Gambio/TwoFactorAuth/Shop/Html';
    
    /**
     * Controller base URL
     */
    const BASE_URL = 'shop.php?do=TwoFactorAuth';
    
    /**
     * Edit action URL
     */
    const EDIT_URL = 'shop.php?do=TwoFactorAuth/Edit';
    
    /**
     * Configuration action URL
     */
    const CONFIGURATION_URL = 'shop.php?do=TwoFactorAuth/Configure';
    
    /**
     * Deactivation action URL
     */
    const DEACTIVATION_URL = 'shop.php?do=TwoFactorAuth/Deactivate';
    
    /**
     * Account settings URL
     */
    const ACCOUNT_SETTINGS_URL = 'account.php';
    
    /**
     * Message stack class name
     */
    const MESSAGE_STACK_CLASS_NAME = 'account';
    
    /**
     * Default OTP issuer
     */
    const DEFAULT_ISSUER = 'Gambio';
    
    /**
     * Session key for the secret
     */
    const SECRET_SESSION_KEY = 'twofactorauth_secret';
}