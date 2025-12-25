<?php
/* --------------------------------------------------------------
   GambioHubCheckoutProcessProcess.inc.php 2023-07-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GambioHubCheckoutProcessProcess extends GambioHubCheckoutProcessProcess_parent
{
    public function send_order_mail()
    {
        if (($_SESSION['payment'] ?? null) === 'gambio_hub') {
            $sendOrderBlacklist = [
                'PayPal2Hub' => [
                    'bancontact',
                    'blik',
                    'eps',
                    'giropay',
                    'ideal',
                    'mybank',
                    'przelewy24',
                    'sofort',
                    'pui',
                ],
            ];
            $hubSelection       = (string)($_SESSION['gambio_hub_selection'] ?? '');
            $hubSubSelection    = (string)($_SESSION['gambio_hub_subselection'] ?? '');
            if (array_key_exists($hubSelection, $sendOrderBlacklist)
                && in_array($hubSubSelection, $sendOrderBlacklist[$hubSelection], true)) {
                return false;
            }
        }
        
        return parent::send_order_mail();
    }

    /**
     * @return bool
     */
    public function check_redirect()
    {
        if (parent::check_redirect()) {
            return true;
        }
        
        if (isset($_SESSION['payment']) && $_SESSION['payment'] === 'gambio_hub'
            && empty($_SESSION['gambio_hub_selection'])) {
            $this->coo_payment = new payment($_SESSION['payment']);
            
            // load the selected shipping module
            $this->coo_shipping = new shipping($_SESSION['shipping']);
            
            $GLOBALS['order'] = new order();
            $this->coo_order  = $GLOBALS['order'];
            
            // load the before_process function from the payment modules
            $this->coo_payment->before_process();
            
            $GLOBALS['order_total_modules'] = new order_total();
            $this->coo_order_total          = $GLOBALS['order_total_modules'];
            $this->order_totals_array       = $this->coo_order_total->process();
            $GLOBALS['order_totals']        =& $this->order_totals_array;
            
            $this->writeErrorLog(__FUNCTION__);
    
            $this->set_redirect_url(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            
            return true;
        }
        
        return false;
    }
    
    
    /**
     * don't store order, if session info got lost for unknown reason and redirect to payment page
     *
     * @return bool
     */
    public function save_order()
    {
        if ($this->coo_order->info['payment_method'] === 'gambio_hub' && empty($_SESSION['gambio_hub_selection'])) {
            $this->writeErrorLog(__FUNCTION__);
            
            xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
            
            return true;
        }
        
        return parent::save_order();
    }
    
    
    /**
     * restore payment settings got lost in very rare cases for unknown reason
     *
     * @return void
     */
    public function save_tracking_data()
    {
        parent::save_tracking_data();
        
        if ($this->coo_order->info['payment_method'] === 'gambio_hub') {
            $logControl = LogControl::get_instance();
            
            if (!$this->tmp_order) {
                $logControl->notice('CheckoutProcessProcess::tmp_order has been reset to true',
                                    '',
                                    'hub',
                                    'notice',
                                    'USER NOTICE',
                                    0,
                                    'Gambio Hub Session-Key: ' . ($_SESSION['gambio_hub_session_key'] ?? ''));
                
                $this->tmp_order = true;
            }
            
            if (!($this->coo_payment instanceof payment)) {
                $logControl->notice('CheckoutProcessProcess::$coo_payment has been reset',
                                    '',
                                    'hub',
                                    'notice',
                                    'USER NOTICE',
                                    0,
                                    'Gambio Hub Session-Key: ' . ($_SESSION['gambio_hub_session_key'] ?? ''));
                
                $this->coo_payment = new payment('gambio_hub');
            }
            
            if ($this->coo_payment->selected_module !== 'gambio_hub') {
                $logControl->notice('CheckoutProcessProcess::$coo_payment->selected_module has been reset to "gambio_hub"',
                                    '',
                                    'hub',
                                    'notice',
                                    'USER NOTICE',
                                    0,
                                    'Gambio Hub Session-Key: ' . ($_SESSION['gambio_hub_session_key'] ?? ''));
                
                $this->coo_payment->selected_module = 'gambio_hub';
            }
            
            if (($_SESSION['payment'] ?? null) !== 'gambio_hub') {
                $logControl->notice('$_SESSION[\'payment\'] has been reset to "gambio_hub"',
                                    '',
                                    'hub',
                                    'notice',
                                    'USER NOTICE',
                                    0,
                                    'Gambio Hub Session-Key: ' . ($_SESSION['gambio_hub_session_key'] ?? ''));
                
                $_SESSION['payment'] = 'gambio_hub';
            }
            
            if (!isset($GLOBALS['gambio_hub']) || !($GLOBALS['gambio_hub'] instanceof gambio_hub)) {
                $logControl->notice('$GLOBALS[\'gambio_hub\'] has been reset to "gambio_hub"',
                                    '',
                                    'hub',
                                    'notice',
                                    'USER NOTICE',
                                    0,
                                    'Gambio Hub Session-Key: ' . ($_SESSION['gambio_hub_session_key'] ?? ''));
                
                $GLOBALS['gambio_hub'] = new gambio_hub();
            }
        }
    }
    
    
    /**
     * Write error to hub log
     *
     * @param string $calledFrom
     *
     * @return void
     */
    protected function writeErrorLog($calledFrom)
    {
        $languageTextManager          = MainFactory::create_object('LanguageTextManager',
                                                                   ['checkout_payment', $_SESSION['languages_id']],
                                                                   true);
        $_SESSION['gm_error_message'] = urlencode($languageTextManager->get_text('checkout_process_failed'));
        
        $logControl = LogControl::get_instance();
        
        $total    = $this->order_totals_array[count($this->order_totals_array) - 1]['value'];
        $customer = $this->coo_order->customer['firstname'] . ' ' . $this->coo_order->customer['lastname'] . ' ('
                    . $this->coo_order->customer['email_address'] . ')';
        
        $hubPayments = [];
        
        if (!empty($_SESSION['gambio_hub_payments_submodules'])) {
            foreach ($_SESSION['gambio_hub_payments_submodules'] as $module => $submodules) {
                $hubPayments[$module] = array_keys($submodules);
            }
        }
        
        $logControl->notice("Order with a total of $total could not be stored, because Gambio Hub selection got lost in session for customer $customer. "
                            . 'Customer was redirected to checkout payment page to try it again.',
                            '',
                            'hub',
                            'notice',
                            'USER NOTICE',
                            0,
                            'Gambio Hub Session-Key: ' . var_export($_SESSION['gambio_hub_session_key'] ?? '--missing--', true) . "\n"
                            . 'Gambio Hub Payment Modules: ' . var_export($hubPayments, true) . "\n"
                            . '$_SESSION[\'payment\']: ' . var_export($_SESSION['payment'], true) . "\n"
                            . '$_SESSION[\'gambio_hub_selection\']: ' . var_export($_SESSION['gambio_hub_selection'] ?? '--missing--', true) . "\n"
                            . '$_SESSION[\'gambio_hub_subselection\']: ' . var_export($_SESSION['gambio_hub_subselection'] ?? '--missing--', true) . "\n"
                            . 'Called from: GambioHubCheckoutProcessProcess::' . $calledFrom);
    }
}
