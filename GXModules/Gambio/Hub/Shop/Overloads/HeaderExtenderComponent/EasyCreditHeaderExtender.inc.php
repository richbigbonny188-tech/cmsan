<?php
/* --------------------------------------------------------------
   EasyCreditHeaderExtender.inc.php 2020-06-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class EasyCreditHeaderExtender extends EasyCreditHeaderExtender_parent
{
    public function proceed()
    {
        parent::proceed();
        
        if (strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) !== 'true') {
            return;
        }
        
        if ($_SESSION['currency'] !== 'EUR') {
            return;
        }
        
        $shopId = gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_EASYCREDITHUB_SHOPID');
        if (empty($shopId)) {
            return;
        }
        $euro = (string)gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_EASYCREDITHUB_EURO');
        if (empty($euro)) {
            $euro = 'ISO';
        }
        $textVariante = (string)gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_EASYCREDITHUB_TEXTVARIANTE');
        if (empty($textVariante)) {
            $textVariante = 'OHNE_RATE';
        }
        
        $langText = MainFactory::create('LanguageTextManager', 'gambio_hub_easycredit');
        
        $easyCreditParameters = [
            'shopId'              => $shopId,
            'finanzierungsbetrag' => 0,
            'euro'                => $euro,
            'textVariante'        => $textVariante,
            'linkText'            => $langText->get_text('widget_linktext'),
            'fromText'            => $langText->get_text('widget_fromtext'),
            'installmentTemplate' => $langText->get_text('widget_installmenttemplate'),
            'widgetTemplate'      => $langText->get_text('widget_widgettemplate'),
            'hasVariants'         => false,
            'placement'           => '',
        ];
        /** @var \product_ORIGIN $product */
        $product = $GLOBALS['product'];
        /** @var \xtcPrice_ORIGIN $xtcPrice */
        $xtcPrice = $GLOBALS['xtPrice'];
        if ($product->isProduct()) {
            if (strtolower((string)gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_EASYCREDITHUB_PRODUCTBANNER')) === 'false') {
                return;
            }
            $easyCreditParameters['finanzierungsbetrag'] = 0;
            $easyCreditParameters['placement']           = 'product';
            $hasVariants                                 = $product->getPropertiesCount() > 0
                                                           || $product->getAttributesCount() > 0;
            $easyCreditParameters['hasVariants']         = $hasVariants;
            if (!$hasVariants) {
                $productsPrice                               = (float)$product->data['products_price'];
                $productsTaxClassId                          = $product->data['products_tax_class_id'];
                $easyCreditParameters['finanzierungsbetrag'] = $xtcPrice->xtcGetPrice($product->data['products_id'],
                    false, 1, $productsTaxClassId, $productsPrice);
            }
        } elseif (strpos($_SERVER['REQUEST_URI'], 'shopping_cart.php') !== false) {
            if (strtolower((string)gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_EASYCREDITHUB_CARTBANNER')) === 'false') {
                return;
            }
            $easyCreditParameters['placement'] = 'cart';
            /** @var \shoppingCart_ORIGIN $cart */
            $cart                = $_SESSION['cart'];
            $finanzierungsBetrag = $cart->show_total();
            if ($finanzierungsBetrag > 0) {
                $globalsOrder     = $GLOBALS['order'];
                $GLOBALS['order'] = new order();
                $orderTotal       = new order_total();
                $orderTotals      = $orderTotal->process();
                $GLOBALS['order'] = $globalsOrder;
                foreach ($orderTotals as $orderTotalsEntry) {
                    if ($orderTotalsEntry['code'] === 'ot_total') {
                        $finanzierungsBetrag = $orderTotalsEntry['value'];
                    }
                }
                $easyCreditParameters['finanzierungsbetrag'] = $finanzierungsBetrag;
            }
        } else {
            return;
        }
        
        if (!is_array($this->v_output_buffer)) {
            $this->v_output_buffer = [];
        }
        
        $this->v_output_buffer[] = '<script>let easyCreditParameters = ' . json_encode($easyCreditParameters)
                                   . ';</script>';
    }
}

