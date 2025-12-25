<?php
/* --------------------------------------------------------------
	amazonadvpay.php 2023-04-28
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2023 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);

class amazonadvpay_ORIGIN
{
    public $code;
    public $title;
    public $description;
    public $tmpOrders = true;
    public $tmpStatus = 0;
    public $enabled;
    public $_coo_apa;
    public $sort_order;
    public $order_status;
    public $info;
    
    
    public function __construct()
    {
        /** @var \AmazonAdvancedPayment _coo_apa */
        $this->_coo_apa = MainFactory::create_object('AmazonAdvancedPayment');
        $t_order        = $GLOBALS['order'] ?? null;
        $this->code     = 'amazonadvpay';
        $this->title    = defined('MODULE_PAYMENT_AMAZONADVPAY_TEXT_TITLE') ? MODULE_PAYMENT_AMAZONADVPAY_TEXT_TITLE : '';
        
        $this->description = '';
        if (defined('MODULE_PAYMENT_AMAZONADVPAY_TEXT_DESCRIPTION') && defined('DIR_WS_ADMIN')) {
            $t_config_button   = '<div class="add-margin-top-20"><a class="btn" href="' . GM_HTTP_SERVER . DIR_WS_ADMIN
                                 . 'admin.php?do=AmazonPayConfiguration">' . $this->_coo_apa->get_text('configure')
                                 . '</a></div>';
            $this->description = MODULE_PAYMENT_AMAZONADVPAY_TEXT_DESCRIPTION . $t_config_button;
        }
        
        $this->sort_order   = defined('MODULE_PAYMENT_AMAZONADVPAY_SORT_ORDER') ? MODULE_PAYMENT_AMAZONADVPAY_SORT_ORDER : '0';
        $this->enabled      = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                              && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->order_status = defined('MODULE_PAYMENT_AMAZONADVPAY_ORDER_STATUS_ID') ? MODULE_PAYMENT_AMAZONADVPAY_ORDER_STATUS_ID : '0';
        if (!empty($_COOKIE['amazon_Login_accessToken'])) {
            $this->info = 'Sie werden noch einmal zur Adressauswahl geleitet und können anschließend Ihre Amazon-Zahlungsweise wählen.';
        } else {
            $this->info = defined('MODULE_PAYMENT_AMAZONADVPAY_TEXT_INFO') ? MODULE_PAYMENT_AMAZONADVPAY_TEXT_INFO : '';
        }
        
        if (is_object($t_order)) {
            $this->update_status();
        }
    }
    
    
    public function update_status()
    {
        $t_order = $GLOBALS['order'];
    }
    
    
    public function javascript_validation()
    {
        return false;
    }
    
    
    public function selection()
    {
        $selection = [
            'id'          => $this->code,
            'module'      => $this->title,
            'description' => $this->info,
        ];
        
        return $selection;
    }
    
    
    public function pre_confirmation_check()
    {
        if (isset($_SESSION['amazonadvpay_order_ref_id']) === false) {
            if ($_SESSION['payment'] === $this->code) {
                if (isset($_COOKIE['amazon_Login_accessToken'])) {
                    $redirectTarget = xtc_href_link('checkout_shipping.php');
                } else {
                    $redirectTarget = xtc_href_link('shopping_cart.php#amazonlogin');
                }
                xtc_redirect($redirectTarget);
            } else {
                xtc_redirect(xtc_href_link('shopping_cart.php'));
            }
        }
        
        return false;
    }
    
    
    public function confirmation()
    {
        $confirmation = [
            'title' => MODULE_PAYMENT_AMAZONADVPAY_TEXT_DESCRIPTION,
        ];
        
        return $confirmation;
    }
    
    
    public function refresh()
    {
    }
    
    
    public function process_button()
    {
        $pb = '';
        
        $t_order_reference_data = $this->_coo_apa->get_order_reference_details($_SESSION['amazonadvpay_order_ref_id']);
        $this->_coo_apa->log($t_order_reference_data->asXML());
        
        if ($_SESSION['cart']->get_content_type() !== 'virtual') {
            $t_shipping_destination_iso2 = (string)$t_order_reference_data->GetOrderReferenceDetailsResult->OrderReferenceDetails->Destination->PhysicalDestination->CountryCode;
            $t_country_is_allowed        = $this->_coo_apa->country_is_allowed($t_shipping_destination_iso2);
            if ($t_country_is_allowed !== true) {
                xtc_redirect(xtc_href_link('checkout_shipping.php'));
            }
        }
        
        $order     = $GLOBALS['order'];
        $oroAmount = (float)(string)$t_order_reference_data->GetOrderReferenceDetailsResult->OrderReferenceDetails->OrderTotal->Amount;
        if (abs($order->info['total'] - $oroAmount) > 0.01) {
            $this->_coo_apa->log("Order amount {$order->info['total']} does not match ORO amount $oroAmount ({$_SESSION['amazonadvpay_order_ref_id']}");
            try {
                $this->_coo_apa->set_order_amount($_SESSION['amazonadvpay_order_ref_id'],
                                                  $order->info['total'],
                                                  $order->info['currency']);
            } catch (AmazonAdvancedPaymentException $e) {
                // cannot get amount to match, must abort
                $this->_coo_apa->cancel_order($_SESSION['amazonadvpay_order_ref_id'], 'could not set order amount');
                unset($_SESSION['amazonadvpay_order_ref_id'], $_SESSION['sendto'], $_SESSION['billto'], $_SESSION['payment']);
                
                if (isset($_SESSION['amazonadvpay_guest'])) {
                    unset($_SESSION['account_type'], $_SESSION['customer_id'], $_SESSION['customer_first_name'], $_SESSION['customer_last_name'], $_SESSION['customer_default_address_id'], $_SESSION['customer_country_id'], $_SESSION['customer_zone_id'], $_SESSION['customer_vat_id'], $_SESSION['amazonadvpay_guest'], $_SESSION['amazonadvpay_logout_guest']);
                }
                $_SESSION['info_message'] = $this->_coo_apa->get_text('mfa_failure');
                xtc_redirect('shopping_cart.php');
            }
        }
        
        return $pb;
    }
    
    
    public function payment_action()
    {
        // order saved, finalize
        $insert_id = $GLOBALS['insert_id'];
        $order     = new order($insert_id);
        try {
            $this->_coo_apa->confirm_order($_SESSION['amazonadvpay_order_ref_id'],
                                           $insert_id,
                                           $order->info['pp_total'],
                                           $order->info['currency']);
            $t_update_customer_data = (bool)($_SESSION['amazonadvpay_guest'] ?? false) === true;
            $this->_coo_apa->update_delivery_address($_SESSION['amazonadvpay_order_ref_id'],
                                                     $insert_id,
                                                     $t_update_customer_data);
            echo $this->getConfirmationFlowRedirect();
            exit;
        } catch (Exception $e) {
            $_SESSION['amazonadvpay_error'] = $this->_coo_apa->get_text('confirmation_failed');
            $orderWrite                     = StaticGXCoreLoader::getService('OrderWrite');
            $orderWrite->updateOrderStatus(new IdType((int)$insert_id),
                                           new IdType((int)$this->_coo_apa->orders_status_auth_declined),
                                           new StringType($this->_coo_apa->get_text('confirmation_declined') . "\n"
                                                          . $e->getMessage()),
                                           new BoolType(false));
            xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error=' . $this->code, 'SSL'));
        }
        
        xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL'));
    }
    
    
    protected function getConfirmationFlowRedirect()
    {
        // redirect the customer to Amazon for Strong Customer Authentication (SCA)/Multi-Factor Authorization (MFA)
        ob_clean();
        $scriptSource = $this->_coo_apa->get_widgets_url();
        if ((bool)gm_get_conf('MODULE_CENTER_SINGLESIGNON_INSTALLED') === true) {
            $ssoConfig = MainFactory::create('SingleSignonConfigurationStorage');
            if ((bool)$ssoConfig->get('services/amazon/active') === true) {
                $scriptSource = $this->_coo_apa->get_lpa_widgets_url();
            }
        }
        $sellerId             = $this->_coo_apa->seller_id;
        $orderReference       = $_SESSION['amazonadvpay_order_ref_id'];
        $confirmationFlowPage = <<<EOHTML
<!DOCTYPE html>
<html>
<head>
    <script src="$scriptSource"></script>
</head>
<body>
    <script>OffAmazonPayments.initConfirmationFlow('$sellerId', '$orderReference', function(confirmationFlow) {
    	confirmationFlow.success();
    });</script>
</body>
</html>
EOHTML;
        
        return $confirmationFlowPage;
    }
    
    
    public function before_process()
    {
        if (isset($_SESSION['amzadvpay_retry'])) {
            if ($_SESSION['cart']->cartID === $_SESSION['amzadvpay_retry']['cart_id']) {
                $this->_coo_apa->log("Re-trying payment for order {$_SESSION['amzadvpay_retry']['insert_id']}");
                
                if (isset($_GET['AuthenticationStatus'])) {
                    $_SESSION['tmp_oID'] = $_SESSION['amzadvpay_retry']['insert_id'];
                } else {
                    $this->_coo_apa->log('Redirection for MFA (ConfirmationFlow), re-tried payment');
                    $insert_id = $_SESSION['amzadvpay_retry']['insert_id'];
                    $order     = new order($insert_id);
                    try {
                        $this->_coo_apa->log('Re-confirming order');
                        $this->_coo_apa->confirm_order($_SESSION['amazonadvpay_order_ref_id'],
                                                       $insert_id,
                                                       $order->info['pp_total'],
                                                       $order->info['currency']);
                    } catch (Exception $e) {
                        $_SESSION['amazonadvpay_error'] = $this->_coo_apa->get_text('confirmation_failed');
                        $orderWrite                     = StaticGXCoreLoader::getService('OrderWrite');
                        $orderWrite->updateOrderStatus(new IdType((int)$insert_id),
                                                       new IdType((int)$this->_coo_apa->orders_status_auth_declined),
                                                       new StringType($this->_coo_apa->get_text('confirmation_declined')
                                                                      . "\n" . $e->getMessage()),
                                                       new BoolType(false));
                        unset($_SESSION['amzadvpay_retry']);
                        xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error=' . $this->code, 'SSL'));
                    }
                    ob_clean();
                    echo $this->getConfirmationFlowRedirect();
                    exit;
                }
            } else {
                $this->_coo_apa->log('Aborting re-try mode, cart has been modified.');
                $this->_coo_apa->log("expected ID: {$_SESSION['amzadvpay_retry']['cartID']} vs. current cartID: {$_SESSION['cart']->cartID}");
                unset($_SESSION['amzadvpay_retry']);
            }
        }
        
        return true;
    }
    
    
    public function after_process()
    {
        $insert_id = $_SESSION['tmp_oID'];
        $order     = new order($_SESSION['tmp_oID']);
        /** @var \OrderWriteService $orderWrite */
        $orderWrite = StaticGXCoreLoader::getService('OrderWrite');
        $this->_coo_apa->delete_amazon_address_book_entries($_SESSION['customer_id']);
        if (isset($_GET['AuthenticationStatus'])) {
            $this->_coo_apa->log('Order ' . $_SESSION['amazonadvpay_order_ref_id'] . ' MFA AuthenticationStatus = '
                                 . $_GET['AuthenticationStatus']);
            if (in_array($_GET['AuthenticationStatus'], ['Success', 'Skipped'], true)) {
                if ((bool)$this->_coo_apa->erp_mode === false && $this->_coo_apa->authorization_mode !== 'manual') {
                    $this->authorizePayment($order, $insert_id);
                }
                $orderWrite->updateOrderStatus(new IdType((int)$insert_id),
                                               new IdType((int)$this->order_status),
                                               new StringType(''),
                                               new BoolType(false));
            } elseif (in_array($_GET['AuthenticationStatus'], ['Abandoned'], true)) {
                $_SESSION['amzadvpay_retry']        = [
                    'insert_id' => $insert_id,
                    'cart_id'   => $_SESSION['cart']->cartID,
                ];
                $abandonedOrderStatusHistoryComment = $this->_coo_apa->get_text('mfa_abandoned_osh');
                $orderWrite->updateOrderStatus(new IdType((int)$insert_id),
                                               new IdType((int)$this->_coo_apa->orders_status_auth_declined),
                                               new StringType($abandonedOrderStatusHistoryComment),
                                               new BoolType(false));
                $_SESSION['amazonadvpay_error'] = $this->_coo_apa->get_text('mfa_abandoned');
                xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error=' . $this->code));
            } elseif (in_array($_GET['AuthenticationStatus'], ['Failure'], true)) {
                $orderWrite->updateOrderStatus(new IdType((int)$insert_id),
                                               new IdType((int)$this->_coo_apa->orders_status_auth_declined),
                                               new StringType('MFA ' . $_GET['AuthenticationStatus']),
                                               new BoolType(false));
                $_SESSION['info_message'] = $this->_coo_apa->get_text('mfa_'
                                                                      . strtolower($_GET['AuthenticationStatus']));
                xtc_redirect(xtc_href_link('shopping_cart.php#amazonpaymentsfailed'));
            } else {
                $orderWrite->updateOrderStatus(new IdType((int)$insert_id),
                                               new IdType((int)$this->_coo_apa->orders_status_auth_declined),
                                               new StringType('Unhandled MFA status ' . $_GET['AuthenticationStatus']),
                                               new BoolType(false));
                $_SESSION['info_message'] = $this->_coo_apa->get_text('mfa_failed');
                xtc_redirect(xtc_href_link('shopping_cart.php#amazonpaymentsfailed'));
            }
        } else {
            $_SESSION['info_message'] = $this->_coo_apa->get_text('mfa_failed');
            unset($_SESSION['amazonadvpay_order_ref_id'], $_SESSION['amzadvpay_retry']);
            xtc_redirect(xtc_href_link('shopping_cart.php#amazonpaymentsfailed'));
        }
        unset($_SESSION['amazonadvpay_order_ref_id'], $_SESSION['amzadvpay_retry']);
        
        return true;
    }
    
    
    protected function authorizePayment($order, $insert_id)
    {
        $t_immediate_capture     = $this->_coo_apa->capture_mode === 'immediate';
        $t_authorization_timeout = $this->_coo_apa->get_authorization_timeout();
        $t_authorization_note    = '';
        if ($this->_coo_apa->mode === 'sandbox' && strpos((string)$_SESSION['comments'], '{') === 0) {
            $t_authorization_note = $_SESSION['comments'];
            $this->_coo_apa->log('Authorization in sandbox simulation mode, SellerAuthorizationNote: '
                                 . $t_authorization_note);
        }
        $t_authorization_response = $this->_coo_apa->authorize_payment($_SESSION['amazonadvpay_order_ref_id'],
                                                                       $order->info['pp_total'],
                                                                       $order->info['currency'],
                                                                       $t_authorization_timeout,
                                                                       $t_immediate_capture,
                                                                       $t_authorization_note);
        unset($_SESSION['amazonadvpay_authrejected']);
        $t_authorization_details = $t_authorization_response->AuthorizeResult->AuthorizationDetails;
        if (empty($t_authorization_details->AuthorizationBillingAddress) === true) {
            // no billing address in authorization details - this happens if Amazon does not have a valid VAT ID on file for the merchant
            // use delivery address instead
            $_SESSION['billto'] = $_SESSION['sendto'];
            $this->_coo_apa->copy_delivery_address_to_billing_address($insert_id);
        } else {
            $t_billing_address      = $t_authorization_details->AuthorizationBillingAddress;
            $t_billing_address_data = [
                'name'         => (string)$t_billing_address->Name,
                'street1'      => (string)$t_billing_address->AddressLine1,
                'street2'      => (string)$t_billing_address->AddressLine2,
                'street3'      => (string)$t_billing_address->AddressLine3,
                'city'         => (string)$t_billing_address->City,
                'postcode'     => (string)$t_billing_address->PostalCode,
                'country_iso2' => (string)$t_billing_address->CountryCode,
            ];
            $this->_coo_apa->update_billing_address($insert_id, $t_billing_address_data);
        }
        
        $t_state       = (string)$t_authorization_details->AuthorizationStatus->State;
        $t_reason_code = (string)$t_authorization_details->AuthorizationStatus->ReasonCode;
        if ($t_state === 'Declined') {
            $this->handleDeclinedAuthorization($insert_id, $t_reason_code);
            xtc_redirect(xtc_href_link('shopping_cart.php#amazonpaymentsfailed'));
        }
    }
    
    
    protected function handleDeclinedAuthorization($insertedOrderId, $reasonCode)
    {
        $this->_coo_apa->log('authorization declined in checkout: ' . $reasonCode);
        
        if ($reasonCode === 'InvalidPaymentMethod') {
            /** @var \OrderWriteService $orderWrite */
            $orderWrite = StaticGXCoreLoader::getService('OrderWrite');
            $orderWrite->updateOrderStatus(new IdType((int)$insertedOrderId),
                                           new IdType((int)$this->_coo_apa->orders_status_auth_declined),
                                           new StringType($this->_coo_apa->get_text('authorization_declined') . ': '
                                                          . $reasonCode),
                                           new BoolType(false));
            $_SESSION['amazonadvpay_error'] = $this->_coo_apa->get_text('mfa_abandoned');
            $_SESSION['amzadvpay_retry']    = [
                'insert_id' => $insertedOrderId,
                'cart_id'   => $_SESSION['cart']->cartID,
            ];
            $this->_coo_apa->log('Redirecting to payment widget for retry');
            xtc_redirect(xtc_href_link('checkout_payment.php', 'payment_error=' . $this->code));
        }
        
        $this->_coo_apa->cancel_order($_SESSION['amazonadvpay_order_ref_id'], 'declined: ' . $reasonCode);
        unset($_SESSION['amazonadvpay_order_ref_id'], $_SESSION['sendto'], $_SESSION['billto'], $_SESSION['payment']);
        
        if (isset($_SESSION['amazonadvpay_guest'])) {
            unset($_SESSION['account_type'], $_SESSION['customer_id'], $_SESSION['customer_first_name'], $_SESSION['customer_last_name'], $_SESSION['customer_default_address_id'], $_SESSION['customer_country_id'], $_SESSION['customer_zone_id'], $_SESSION['customer_vat_id'], $_SESSION['amazonadvpay_guest'], $_SESSION['amazonadvpay_logout_guest']);
        }
        $_SESSION['info_message'] = $this->_coo_apa->get_text('note_authorization_rejected');
        
        /** @var \OrderWriteService $orderWrite */
        $orderWrite = StaticGXCoreLoader::getService('OrderWrite');
        $orderWrite->updateOrderStatus(new IdType((int)$insertedOrderId),
                                       new IdType((int)$this->_coo_apa->orders_status_auth_declined),
                                       new StringType($this->_coo_apa->get_text('authorization_declined') . ': '
                                                      . $reasonCode),
                                       new BoolType(false));
    }
    
    
    public function get_error()
    {
        $error = false;
        if (isset($_SESSION['amazonadvpay_error'])) {
            $error = ['error' => $_SESSION['amazonadvpay_error']];
            unset($_SESSION['amazonadvpay_error']);
        }
        
        return $error;
    }
    
    
    public function check()
    {
        if (!isset ($this->_check)) {
            $check_query  = xtc_db_query("select `value` from `gx_configurations` where `key` = 'configuration/MODULE_PAYMENT_"
                                         . strtoupper($this->code) . "_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    public function install()
    {
        $config     = $this->_configuration();
        $sort_order = 0;
        foreach ($config as $key => $data) {
            $install_query = "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) "
                             . "values ('configuration/MODULE_PAYMENT_" . strtoupper($this->code) . "_" . $key . "', '"
                             . ($data['value'] ?? '') . "', '" . $sort_order . "', '" . addslashes($data['type'] ?? '')
                             . "', now())";
            xtc_db_query($install_query);
            $sort_order++;
        }
    }
    
    
    public function _configuration()
    {
        $config = [
            'STATUS'          => [
                'value' => 'True',
                'type'  => 'switcher',
            ],
            'ALLOWED'         => [
                'value' => '',
            ],
            'SORT_ORDER'      => [
                'value' => '0',
            ],
            'ORDER_STATUS_ID' => [
                'value' => '1',
                'type'  => 'order-status',
            ],
        ];
        
        return $config;
    }
    
    
    public function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    public function keys()
    {
        $ckeys = array_keys($this->_configuration());
        $keys  = [];
        foreach ($ckeys as $k) {
            $keys[] = 'configuration/MODULE_PAYMENT_' . strtoupper($this->code) . '_' . $k;
        }
        
        return $keys;
    }
    
}

MainFactory::load_origin_class('amazonadvpay');
