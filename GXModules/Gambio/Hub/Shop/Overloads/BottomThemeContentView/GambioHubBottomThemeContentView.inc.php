<?php
/* --------------------------------------------------------------
   GambioHubBottomThemeContentView.inc.php 2020-05-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubBottomThemeContentView extends GambioHubBottomThemeContentView_parent
{
    public function get_modules_html()
    {
        $html = parent::get_modules_html();
        if(strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) !== 'true') {
            return;
        }
        
        $hubPayPalConfiguration = MainFactory::create('HubPayPalConfiguration');
        /** @var \shoppingCart_ORIGIN $shoppingCart */
        $shoppingCart = $_SESSION['cart'];
        $cartProducts = $shoppingCart->get_products();
        if(!empty($cartProducts) && $hubPayPalConfiguration->isECSDropdown())
        {
            $script = file_get_contents(dirname(dirname(__DIR__)) . '/Javascript/PayPalDropdownCartButton.js');
            if($script !== false)
            {
                $buttonConfigurationJson = json_encode($hubPayPalConfiguration->getECSButtonConfiguration());
                $script = str_replace(
                    'buttonConfiguration = {}',
                    'buttonConfiguration = ' . $buttonConfigurationJson,
                    $script
                );
                $text = MainFactory::create('LanguageTextManager', 'gambio_hub_paypal');
                $phrasesJson = json_encode(
                    [
                        'separatorLabel' => $text->get_text('ecs_button_intro'),
                    ]
                );
                $script = str_replace(
                    'phrases = {}',
                    'phrases = ' . $phrasesJson,
                    $script
                );

                $script = '<script>' . $script . '</script>';
                $html .= $script;
            }
        }
        
        return $html;
    }
}
