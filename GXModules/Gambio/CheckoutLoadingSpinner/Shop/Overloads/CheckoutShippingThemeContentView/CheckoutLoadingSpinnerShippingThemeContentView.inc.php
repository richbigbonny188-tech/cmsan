<?php
/* --------------------------------------------------------------
   CheckoutLoadingSpinnerShippingThemeContentView.inc.php 2018-11-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class representing a checkout shipping content view overload
 */
class CheckoutLoadingSpinnerShippingThemeContentView extends CheckoutLoadingSpinnerShippingThemeContentView_parent
{
    /**
     * Add additional data to the content view
     */
    public function prepare_data()
    {
        parent::prepare_data();
        $configurationStorage = MainFactory::create('CheckoutLoadingSpinnerConfigurationStorage');
        $languageTextManager  = MainFactory::create('LanguageTextManager', 'module_center_module');
        
        $this->set_content_data('checkout_loading_spinner_is_enabled', (bool)$configurationStorage->get('is_enabled'));
        $this->set_content_data('checkout_loading_spinner_text',
                                $languageTextManager->get_text('checkout_loading_spinner_text'));
        $this->set_content_data('checkout_loading_spinner_timeout', (int)$configurationStorage->get('timeout'));
    }
}
