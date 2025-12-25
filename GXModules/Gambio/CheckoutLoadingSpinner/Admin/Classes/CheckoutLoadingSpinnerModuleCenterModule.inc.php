<?php
/* --------------------------------------------------------------
   CheckoutLoadingSpinnerModuleCenterModule.inc.php 2018-04-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing the checkout loading spinner module center module
 */
class CheckoutLoadingSpinnerModuleCenterModule extends AbstractModuleCenterModule
{
    /**
     * Initialize
     */
    protected function _init()
    {
        $this->title       = $this->languageTextManager->get_text('checkout_loading_spinner_title');
        $this->description = $this->languageTextManager->get_text('checkout_loading_spinner_description');
        $this->sortOrder   = 98981;
    }
}