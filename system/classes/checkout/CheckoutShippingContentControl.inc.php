<?php
/* --------------------------------------------------------------
  CheckoutShippingContentControl.inc.php 2022-05-02
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------


  based on:
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2002-2003 osCommerce(checkout_shipping.php,v 1.15 2003/04/08); www.oscommerce.com
  (c) 2003	 nextcommerce (checkout_shipping.php,v 1.20 2003/08/20); www.nextcommerce.org
  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: checkout_shipping.php 1037 2005-07-17 15:25:32Z gwinger $)

  Released under the GNU General Public License
  -----------------------------------------------------------------------------------------
  Third Party contribution:

  Credit Class/Gift Vouchers/Discount Coupons (Version 5.10)
  http://www.oscommerce.com/community/contributions,282
  Copyright (c) Strider | Strider@oscworks.com
  Copyright (c  Nick Stanko of UkiDev.com, nick@ukidev.com
  Copyright (c) Andre ambidex@gmx.net
  Copyright (c) 2001,2002 Ian C Wilson http://www.phesis.org

  Released under the GNU General Public License
  --------------------------------------------------------------------------------------- */

require_once DIR_FS_INC . 'xtc_count_shipping_modules.inc.php';
require_once DIR_WS_CLASSES . 'shipping.php';

MainFactory::load_class('CheckoutControl');

class CheckoutShippingContentControl extends CheckoutControl
{
    /**
     * @return bool
     */
    public function proceed()
    {
        if (!$this->isCustomerLoggedIn()) {
            // redirect to the login page
            return true;
        }
        
        $textManager = $this->getLanguageTextManager();
        $xtcPrice    = $this->getXtcPrice();
        
        if (!$this->check_stock()) {
            $this->set_redirect_url(xtc_href_link(FILENAME_SHOPPING_CART));
        }
        
        $this->setShippingAddress();
        
        $order = $this->getOrder();
        
        $this->setCartId();
        
        if ($this->isVirtualCart($order)) {
            // redirect to checkout payment page
            return true;
        }
        
        $shippingModules = $this->getShippingModules($order);
        
        $freeShippingLimit = 0;
        $isFreeShipping    = $this->isFreeShipping($order, $xtcPrice, $textManager, $freeShippingLimit);
        
        $this->processShippingOptions();
        
        if ($this->processSelectedShippingMethod($isFreeShipping, $shippingModules)) {
            // redirect to checkout payment page
            return true;
        }
        
        $this->ensureShippingMethodIsSelected($shippingModules);
        $this->buildHtml($isFreeShipping, $freeShippingLimit, $xtcPrice, $shippingModules);
        
        return true;
    }
    
    
    /**
     * @return float
     */
    protected function getFreeAmountValue(): float
    {
        return (double)MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER;
    }
    
    
    /**
     * overloading info: method introduced in GX 4.5
     *
     * @return void
     */
    protected function setShippingAddress(): void
    {
        // if no shipping destination address was selected, use the customers own address as default
        if (!isset($_SESSION['sendto'])) {
            $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
        } else {
            // verify the selected shipping address
            $check_address_query = xtc_db_query("SELECT COUNT(*) AS total 
													FROM " . TABLE_ADDRESS_BOOK . " 
													WHERE
														customers_id = '" . (int)$_SESSION['customer_id'] . "' AND
														address_book_id = '" . (int)$_SESSION['sendto'] . "'",
                                                'db_link',
                                                false);
            $check_address       = xtc_db_fetch_array($check_address_query);
            
            if ($check_address['total'] != '1') {
                $_SESSION['sendto'] = $_SESSION['customer_default_address_id'];
                
                if (isset($_SESSION['shipping'])) {
                    unset($_SESSION['shipping']);
                }
            }
        }
    }
    
    
    protected function setCartId(): void
    {
        // register a random ID in the session to check throughout the checkout procedure
        // against alterations in the shopping cart contents
        $_SESSION['cartID'] = $_SESSION['cart']->cartID;
    }
    
    
    /**
     * @param order $order
     *
     * @return bool
     */
    protected function isVirtualCart(order $order): bool
    {
        // if the order contains only virtual products, forward the customer to the billing page as
        // a shipping address is not needed
        if ($order->content_type == 'virtual' || ($order->content_type == 'virtual_weight')
            || ($_SESSION['cart']->count_contents_non_virtual() == 0)) { // GV Code added
            $_SESSION['shipping'] = false;
            $_SESSION['sendto']   = false;
            $this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            
            return true;
        }
        
        return false;
    }
    
    
    /**
     * @return LanguageTextManager|mixed
     */
    protected function getLanguageTextManager()
    {
        $coo_lang_file_master = MainFactory::create_object('LanguageTextManager', [], true);
        
        return $coo_lang_file_master;
    }
    
    
    /**
     * @return xtcPrice
     */
    protected function getXtcPrice(): xtcPrice
    {
        $coo_xtc_price = new xtcPrice($_SESSION['currency'], $_SESSION['customers_status']['customers_status_id']);
        
        return $coo_xtc_price;
    }
    
    
    /**
     * @return order
     */
    protected function getOrder(): order
    {
        $order = new order();
        
        return $order;
    }
    
    
    /**
     * @param order $order
     *
     * @return shipping
     */
    protected function getShippingModules(order $order): shipping
    {
        // used in shipping class
        $GLOBALS['total_weight'] = $_SESSION['cart']->show_weight();
        $GLOBALS['total_count']  = $_SESSION['cart']->count_contents();
        
        if ($order->delivery['country']['iso_code_2'] != '') {
            $_SESSION['delivery_zone'] = $order->delivery['country']['iso_code_2'];
        }
        
        // load all enabled shipping modules
        return new shipping();
    }
    
    
    /**
     * @param order               $order
     * @param xtcPrice            $xtcPrice
     * @param LanguageTextManager $textManager
     * @param                     $freeShippingLimit
     *
     * @return bool
     */
    protected function isFreeShipping(
        order $order,
        xtcPrice $xtcPrice,
        LanguageTextManager $textManager,
        &$freeShippingLimit
    ): bool {
        if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING')
            && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {
            $pass = false;
            
            switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
                case 'national' :
                    if ($order->delivery['country_id'] == STORE_COUNTRY) {
                        $pass = true;
                    }
                    break;
                case 'international' :
                    if ($order->delivery['country_id'] != STORE_COUNTRY) {
                        $pass = true;
                    }
                    break;
                case 'both' :
                    $pass = true;
                    break;
                default :
                    $pass = false;
                    break;
            }
            
            $isFreeShipping = false;
            
            // Xycons-2021 (Make it more flexible for Overloads) (Start)
            $freeShippingLimit = $this->getFreeAmountValue();
            // Xycons-2021 (Make it more flexible for Overloads) (Ende)
            
            if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0
                && (int)MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS > 0) {
                $freeShippingLimit = $freeShippingLimit / (1 + $xtcPrice->TAX[MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS]
                                                               / 100);
            }
            
            if ($pass
                && ($order->info['total'] - $order->info['shipping_cost'] >= $xtcPrice->xtcFormat($freeShippingLimit,
                                                                                                  false,
                                                                                                  0,
                                                                                                  true))) {
                $isFreeShipping = true;
                $textManager->init_from_lang_file('lang/' . $_SESSION['language']
                                                  . '/modules/order_total/ot_shipping.php');
            }
        } else {
            $isFreeShipping = false;
        }
        
        return $isFreeShipping;
    }
    
    
    /**
     * Stores selected shipping options in $_SESSION['shipping_options'].
     *
     */
    protected function processShippingOptions(): void
    {
        if (!empty($this->v_data_array['POST']) && isset($this->v_data_array['POST']['shipping_options'])) {
            $_SESSION['shipping_options'] = $this->v_data_array['POST']['shipping_options'];
        }
    }
    
    
    /**
     * @param bool     $isFreeShipping
     * @param shipping $shippingModules
     *
     * @return bool true, if redirection to checkout payment is needed
     */
    protected function processSelectedShippingMethod(bool $isFreeShipping, shipping $shippingModules): bool
    {
        // process the selected shipping method
        if (isset($this->v_data_array['POST']['action']) && ($this->v_data_array['POST']['action'] === 'process')) {
            if (xtc_count_shipping_modules() > 0 || $isFreeShipping) {
                if (isset($this->v_data_array['POST']['shipping'])
                    && strpos($this->v_data_array['POST']['shipping'],
                              '_')) {
                    $_SESSION['shipping'] = $this->v_data_array['POST']['shipping'];
                    $isSelfpickup         = $_SESSION['shipping'] === 'selfpickup_selfpickup';
                    
                    [$module, $method] = explode('_', $_SESSION['shipping']);
                    
                    if (isset($GLOBALS[$module]) && is_object($GLOBALS[$module])) {
                        if (!isset($$module) || !is_object($$module)) {
                            $$module = $GLOBALS[$module];
                        }
                    }
                    
                    if ((isset($$module) && is_object($$module)) || $isFreeShipping) {
                        if ($_SESSION['shipping'] === 'free_free') {
                            $quote[0]['methods'][0]['title'] = FREE_SHIPPING_TITLE;
                            $quote[0]['methods'][0]['cost']  = '0';
                        } elseif (is_object($$module)) {
                            $quote = $shippingModules->quote($method, $module);
                        } else {
                            $quote['error'] = 'error';
                        }
                        if (isset($quote['error'])) {
                            unset($_SESSION['shipping']);
                        } elseif ((isset($quote[0]['methods'][0]['title']))
                                  && (isset($quote[0]['methods'][0]['cost']))) {
                            $_SESSION['shipping'] = [
                                'id'    => $_SESSION['shipping'],
                                'title' => (($isFreeShipping
                                             && !$isSelfpickup) ? $quote[0]['methods'][0]['title'] : $quote[0]['module']
                                                                                                     . ' ('
                                                                                                     . $quote[0]['methods'][0]['title']
                                                                                                     . ')'),
                                'cost'  => $quote[0]['methods'][0]['cost']
                            ];
                            
                            $this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
                            
                            return true;
                        }
                    } else {
                        unset($_SESSION['shipping']);
                    }
                }
            } else {
                $_SESSION['shipping'] = false;
                
                $this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
                
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * @param shipping $shippingModules
     *
     * @return array
     */
    protected function getShippingMethods(shipping $shippingModules): array
    {
        // get all available shipping quotes
        return $shippingModules->quote();
    }
    
    
    /**
     * @param shipping $shippingModules
     */
    protected function ensureShippingMethodIsSelected(shipping $shippingModules): void
    {
        // if no shipping method has been selected, automatically select the cheapest method.
        // if the modules status was changed when none were available, to save on implementing
        // a javascript force-selection method, also automatically select the cheapest shipping
        // method if more than one module is now enabled
        if (!isset($_SESSION['shipping'])
            || (isset($_SESSION['shipping']) && $_SESSION['shipping'] == false
                && xtc_count_shipping_modules() > 1)) {
            $_SESSION['shipping'] = $shippingModules->cheapest();
        }
    }
    
    
    /**
     * @param bool     $isFreeShipping
     * @param float    $freeShippingLimit
     * @param xtcPrice $xtcPrice
     * @param shipping $shippingModules
     */
    protected function buildHtml(
        bool $isFreeShipping,
        float $freeShippingLimit,
        xtcPrice $xtcPrice,
        shipping $shippingModules
    ): void {
        $checkoutShippingContentView = MainFactory::create_object('CheckoutShippingThemeContentView');
        $checkoutShippingContentView->set_('free_shipping', $isFreeShipping);
        $checkoutShippingContentView->set_('quotes_array', $this->getShippingMethods($shippingModules));
        $checkoutShippingContentView->set_('shipping_free_over', $freeShippingLimit);
        $checkoutShippingContentView->set_('coo_xtc_price', $xtcPrice);
        $checkoutShippingContentView->set_('address_book_id', $_SESSION['sendto']);
        $checkoutShippingContentView->set_('customer_id', $_SESSION['customer_id']);
        $checkoutShippingContentView->set_('language', $_SESSION['language']);
        
        if (isset($_SESSION['shipping']['id'])) {
            $checkoutShippingContentView->set_('selected_shipping_method', $_SESSION['shipping']['id']);
        }
        
        $checkoutShippingContentView->set_('style_edit_active', StyleEditServiceFactory::service()->isEditing());
        
        $this->v_output_buffer = $checkoutShippingContentView->get_html();
    }
    
    
    /**
     * if the customer is not logged in, redirect to the login page
     *
     * @return bool
     */
    protected function isCustomerLoggedIn(): bool
    {
        if (isset($_SESSION['customer_id']) === false) {
            if (ACCOUNT_OPTIONS === 'guest') {
                $this->set_redirect_url(xtc_href_link('shop.php', 'do=CreateGuest&checkout_started=1', 'SSL'));
            } else {
                $this->set_redirect_url(xtc_href_link(FILENAME_LOGIN, 'checkout_started=1', 'SSL'));
            }
            
            return false;
        }
        
        return true;
    }
}
