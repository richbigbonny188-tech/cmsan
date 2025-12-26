<?php
/* --------------------------------------------------------------
   GambioHubShoppingCartThemeContentView.inc.php 2023-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubShoppingCartThemeContentView extends GambioHubShoppingCartThemeContentView_parent
{
    public function prepare_data()
    {
        if (strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) === 'true') {
            $this->_setPayPalHubButton();
        }
        parent::prepare_data();
    }


    protected function _setPayPalHubButton()
    {
        unset($_SESSION['PayPal2Hub'], $_SESSION['payment'], $_SESSION['gambio_hub_selection']);
        
        /** @var \HubPayPalConfiguration $config */
        $config = MainFactory::create('HubPayPalConfiguration');

        $buttonCode = '';

        if ($config->isECS() === true) {
            $text       = MainFactory::create('LanguageTextManager', 'gambio_hub_paypal', $_SESSION['languages_id']);
            $buttonCode .= '<div class="ecs_intro">' . $text->get_text('ecs_button_intro') . '</div>';
            $buttonCode .= '<div id="paypal-button-container"></div>';
        }

        $buttonCode .= '<div class="paypal-installments paypal-installments-cartbottom" data-ppinst-pos="cartBottom" data-partner-attribution-id="GambioGmbH_Cart_Hub_PPXO"></div>';

        if (isset($_GET['display_mode']) && $_GET['display_mode'] === 'ecs') {
            $this->set_content_data('paypal_ecs_mode', true);
        }

        $this->_setCheckoutButton([
            'script' => $buttonCode,
        ]);
    }


    protected function _getPayPalLocale()
    {
        $paypalLocaleFactory = MainFactory::create('HubPayPalLocaleFactory');
        $locale = $paypalLocaleFactory->getLocaleByLanguageAndCountry($_SESSION['language_code'], $_SESSION['delivery_zone']);
        return $locale;
    }
    
}
