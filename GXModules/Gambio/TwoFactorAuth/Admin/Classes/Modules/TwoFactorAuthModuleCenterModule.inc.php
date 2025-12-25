<?php

/* --------------------------------------------------------------
   TwoFactorAuthModuleCenterModule.inc.php 2018-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a two-factor-authorization module center module
 */
class TwoFactorAuthModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * Language phrase section
     */
    const LANGUAGE_PHRASE_SECTION = 'twofactorauth';
    
    
    /**
     * Initialize the module
     */
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('title', self::LANGUAGE_PHRASE_SECTION);
        $this->description = $this->languageTextManager->get_text('description', self::LANGUAGE_PHRASE_SECTION);
        $this->sortOrder   = 999998;
    }
}