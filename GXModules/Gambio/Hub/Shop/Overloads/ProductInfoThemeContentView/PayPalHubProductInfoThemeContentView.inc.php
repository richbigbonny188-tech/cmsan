<?php
/* --------------------------------------------------------------
   PayPalHubProductInfoThemeContentView.inc.php 2022-02-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PayPalHubProductInfoThemeContentView extends PayPalHubProductInfoThemeContentView_parent
{
    public function build_html($p_content_data_array = false, $p_template_file = false)
    {
        $html = parent::build_html($p_content_data_array, $p_template_file);
        if(strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) !== 'true') {
            return $html;
        }
    
        $price = (float)$this->product->data['products_price'];
        if ($price < 0.01) {
            return $html;
        }
        
        /** @var \HubPayPalConfiguration $hubPayPalConfiguration */
        $hubPayPalConfiguration = MainFactory::create('HubPayPalConfiguration');
        if ($hubPayPalConfiguration->isECSProduct()) {
            $productECSScript = file_get_contents(dirname(dirname(__DIR__)) . '/Javascript/PayPalProductECS.js');
            if ($productECSScript !== false) {
                $buttonConfigurationJson = json_encode($hubPayPalConfiguration->getECSButtonConfiguration());
                $productECSScript        = str_replace(
                    'buttonConfiguration = {}',
                    'buttonConfiguration = ' . $buttonConfigurationJson,
                    $productECSScript
                );
                $text = MainFactory::create('LanguageTextManager', 'gambio_hub_paypal');
                $phrasesJson = json_encode(
                    [
                        'separatorLabel' => $text->get_text('ecs_button_intro'),
                    ]
                );
                $productECSScript = str_replace(
                    'phrases = {}',
                    'phrases = ' . $phrasesJson,
                    $productECSScript
                );
                $html                    .= '<script>' . $productECSScript . '</script>';
            }
        }
        
        return $html;
    }
}
