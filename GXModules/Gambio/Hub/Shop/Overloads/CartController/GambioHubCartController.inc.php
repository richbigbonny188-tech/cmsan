<?php
/* --------------------------------------------------------------
   GambioHubCartController.inc.php 2020-09-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubCartController extends GambioHubCartController_parent
{
    protected function _getCartJson()
    {
        $json                               = parent::_getCartJson();
        $shopId = gm_get_conf('GAMBIO_HUB_REMOTE_CONFIG_EASYCREDITHUB_SHOPID');
        if(empty($shopId) || strtolower((string)@constant('MODULE_PAYMENT_GAMBIO_HUB_STATUS')) !== 'true') {
            return $json;
        }
        /** @var \xtcPrice_ORIGIN $xtcPrice */
        $xtcPrice = $GLOBALS['xtPrice'];
        /** @var \shoppingCart_ORIGIN $cart */
        $cart = $_SESSION['cart'];
        $cartTotal = $cart->show_total();
        $globalsOrder     = $GLOBALS['order'];
        $GLOBALS['order'] = new order();
        $orderTotal       = new order_total();
        $orderTotal->process();
        $shippingCost                       = $xtcPrice->xtcFormat($GLOBALS['order']->info['shipping_cost'], false);
        $cartTotal                          += $shippingCost;
        $GLOBALS['order']                   = $globalsOrder;
        $triggerEasyCreditReload = <<<EOJ
if(typeof(rkPlugin) !== 'undefined') {
    document.querySelector('div.easycredit-rr-container').style.backgroundImage = 'url("https://static.easycredit.de/content/image/logo/ratenkauf_42_55.png")';
    rkPlugin.anzeige("easycredit-ratenrechner-cart", {
                webshopId: easyCreditParameters.shopId,
                finanzierungsbetrag: $cartTotal,
                euro: easyCreditParameters.euro,
                textVariante: easyCreditParameters.textVariante
            });
}
EOJ;
        $json['content']['button']['value'] .= '<script>' . $triggerEasyCreditReload . '</script>';
        
        return $json;
    }
    
    
    public function actionUseBalance()
    {
        $_SESSION['cot_gv'] = true;
        $shoppingCartUrl = xtc_href_link('shopping_cart.php');
        return MainFactory::create('RedirectHttpControllerResponse', $shoppingCartUrl);
    }
    
    public function actionDoNotUseBalance()
    {
        $_SESSION['cot_gv'] = false;
        $shoppingCartUrl = xtc_href_link('shopping_cart.php');
        return MainFactory::create('RedirectHttpControllerResponse', $shoppingCartUrl);
    }
}
