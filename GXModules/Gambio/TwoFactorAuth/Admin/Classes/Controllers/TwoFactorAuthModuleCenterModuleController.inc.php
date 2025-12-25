<?php

/* --------------------------------------------------------------
   TwoFactorAuthModuleCenterModuleController.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization module center module controller
 */
class TwoFactorAuthModuleCenterModuleController extends AbstractModuleCenterModuleController
{
    /**
     * Language phrase section
     */
    const LANGUAGE_PHRASE_SECTION = 'twofactorauth';
    
    /**
     * Configuration URL
     */
    const CONFIGURATION_URL = 'shop.php?do=TwoFactorAuth';
    
    
    /**
     * Initialize the module
     */
    protected function _init()
    {
        $this->pageTitle   = $this->languageTextManager->get_text('title', self::LANGUAGE_PHRASE_SECTION);
        $this->redirectUrl = DIR_WS_CATALOG . self::CONFIGURATION_URL;
    }
}