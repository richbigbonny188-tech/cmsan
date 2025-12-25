<?php
/* --------------------------------------------------------------
   payone_master.php 2022-05-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);

class payone_master_ORIGIN
{
    public $code, $title, $description, $enabled;
    //var $form_action_url;
    public $tmpOrders = true;
    public $tmpStatus; // = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_TMPORDER_STATUS_ID');
    public $payone;
    public $config;
    public $global_config;
    public $pg_config;
    public $sort_order;
    public $info;
    public $order_status;
    
    
    public function __construct()
    {
        $order = $GLOBALS['order'];
        
        $this->payone        = new GMPayOne();
        $this->config        = $this->payone->getConfig();
        $this->pg_config     = $this->config[$this->_getActiveGenreIdentifier()] ?? null;
        $this->global_config = ($this->pg_config['global_override'] ?? null)
                               === 'true' ? ($this->pg_config['global'] ?? null) : ($this->config['global'] ?? null);
        $this->tmpStatus     = $this->config['orders_status']['tmp'];
    
        !empty($this->code) or $this->code = 'payone';
        $this->title       = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                     . '_TEXT_TITLE') ? constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                 . '_TEXT_TITLE') : ''; # .' TITLE';
        $this->description = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                     . '_TEXT_DESCRIPTION') ? constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                       . '_TEXT_DESCRIPTION') : ''; # . ' DESCRIPTION';
        $this->description .= '<div class="add-margin-top-20"><a class="btn" href="' . xtc_href_link('admin.php',
                'do=PayOneModuleCenterModule') . '">' . (defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                 . '_TEXT_EXTD_CONFIG') ? constant('MODULE_PAYMENT_'
                                                                                                   . strtoupper($this->code)
                                                                                                   . '_TEXT_EXTD_CONFIG')
                                                                                          : '') . '</a></div><br>';
        $this->sort_order  = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                     . '_SORT_ORDER') ? constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                 . '_SORT_ORDER') : 0;
        $this->enabled     = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->info        = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                     . '_TEXT_INFO') ? constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                . '_TEXT_INFO') : ''; #. ' INFO';
        /*
        if ((int) @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ORDER_STATUS_ID') > 0) {
            $this->order_status = @constant('MODULE_PAYMENT_'.strtoupper($this->code).'_ORDER_STATUS_ID');
        }
        */
        $this->order_status = $this->config['orders_status']['paid'];
        
        if (is_object($order)) {
            $this->update_status();
        }
    }
    
    
    public function _getActiveGenreIdentifier()
    {
        $available_genres = $this->payone->getAvailablePaymentGenres();
        $active_genre     = false;
        foreach ($available_genres as $identifier => $ag) {
            if ($ag['genre'] == $this->payone_genre) {
                $active_genre = $identifier;
            }
        }
        
        return $active_genre;
    }
    
    
    public function update_status()
    {
        $order = $GLOBALS['order'];
        
        if (($this->enabled == true) && ((int)constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ZONE') > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ZONE')
                                        . "' and zone_country_id = '" . $order->billing['country']['id']
                                        . "' order by zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }
            
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }
    
    
    public function _updateOrdersStatus($orders_id, $txid, $txaction, $comment = '')
    {
        if (in_array($txaction, $this->payone->getStatusNames())) {
            $orders_status_id    = $this->config['orders_status'][$txaction];
            /** @var OrderWriteServiceInterface $orderWriteService */
            $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
            $orderWriteService->updateOrderStatus(new IdType((int)$orders_id),
                                                  new IntType((int)$orders_status_id),
                                                  new StringType($comment),
                                                  new BoolType(false));
        }
    }
    
    
    public function _checkRequirements()
    {
        $out = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                       . '_SYSTEM_REQUIREMENTS') ? (constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                             . '_SYSTEM_REQUIREMENTS') . ':<br>') : '';
        if (defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], constant('DIR_WS_ADMIN')) !== false
            && defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_OK')
            && defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_MISSING')) {
            $has_curl = in_array('curl', get_loaded_extensions());
            $out      .= "cURL: " . ($has_curl ? '<span style="color:green">' . constant('MODULE_PAYMENT_'
                                                                                         . strtoupper($this->code)
                                                                                         . '_OK')
                                                 . '</span>' : '<span style="color:red">' . constant('MODULE_PAYMENT_'
                                                                                                     . strtoupper($this->code)
                                                                                                     . '_MISSING')
                                                               . '</span><br>');
        }
    
        return $out;
    }
    
    
    public function javascript_validation()
    {
        return false;
    }
    
    
    public function selection()
    {
        $active_genre = $this->_getActiveGenreIdentifier();
        if ($active_genre === false) {
            return false;
        }
        $pg_cart_min = (int)$this->config[$active_genre]['min_cart_value'];
        $pg_cart_max = (int)$this->config[$active_genre]['max_cart_value'];
        $cart_total  = $_SESSION['cart']->show_total();
        if ($cart_total < $pg_cart_min || $cart_total > $pg_cart_max) {
            return false;
        }
        
        // address check
        $_SESSION['payone_ac_billing_hash']  = isset($_SESSION['payone_ac_billing_hash']) ? $_SESSION['payone_ac_billing_hash'] : '';
        $_SESSION['payone_ac_delivery_hash'] = isset($_SESSION['payone_ac_delivery_hash']) ? $_SESSION['payone_ac_delivery_hash'] : '';
        if (!$this->_addressesAreValidated()) {
            if ($cart_total >= $this->config['address_check']['min_cart_value']
                && $cart_total <= $this->config['address_check']['max_cart_value']) {
                $check_required = false;
                
                if ($this->config['address_check']['billing_address'] != 'none'
                    && $_SESSION['payone_ac_billing_hash'] != $this->payone->getAddressHash($_SESSION['billto'])) {
                    $check_required = true;
                }
                
                if ($this->config['address_check']['delivery_address'] != 'none'
                    && $_SESSION['payone_ac_delivery_hash'] != $this->payone->getAddressHash($_SESSION['sendto'])) {
                    $check_required = true;
                }
                
                if ($check_required) {
                    $this->payone->log('selection() redirecting customer ' . $_SESSION['customer_id']
                                       . ' to address check');
                    xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_payone_addresscheck.php');
                }
            } else {
                // skip address check, assume addresses as validated
                $_SESSION['payone_ac_billing_hash']  = $this->payone->getAddressHash($_SESSION['billto']);
                $_SESSION['payone_ac_delivery_hash'] = $this->payone->getAddressHash($_SESSION['sendto']);
            }
        }
        
        // credit check
        if ($this->config['credit_risk']['operating_mode'] == 'test' && isset($_GET['resetcr'])) {
            unset($_SESSION['payone_cr_result']);
            unset($_SESSION['payone_cr_hash']);
        }
        $_SESSION['payone_cr_result'] = isset($_SESSION['payone_cr_result']) ? $_SESSION['payone_cr_result'] : $this->config['credit_risk']['newclientdefault'];
        if ($this->config['credit_risk']['active'] == 'true'
            && $this->config['credit_risk']['timeofcheck'] == 'before') {
            $_SESSION['payone_cr_hash'] = isset($_SESSION['payone_cr_hash']) ? $_SESSION['payone_cr_hash'] : '';
            $credit_risk_checked        = $_SESSION['payone_cr_hash']
                                          == $this->payone->getAddressHash($_SESSION['billto']);
            if (!$credit_risk_checked && !isset($_GET['p1crskip'])) {
                // risk check has not been performed and user has not actively chosen to skip it
                xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_payone_cr.php');
            }
        }
        
        if ($this->config['credit_risk']['active'] == 'true') {
            if ($this->config[$active_genre]['allow_' . $_SESSION['payone_cr_result']] != 'true') {
                // payment genre not allowed with user's credit score
                return false;
            }
        }
        
        $selection = [
            'id'          => $this->code,
            'module'      => $this->config[$active_genre]['name'], //$this->title,
            'description' => $this->info,
            'fields'      => [],
        ];
        if (method_exists($this, '_paymentDataForm')) {
            $pdf = $this->_paymentDataForm($active_genre);
            if (!empty($pdf[0])) {
                $selection['description'] .= $pdf[0]['field'];
            }
        }
        
        return $selection;
    }
    
    
    public function _addressesAreValidated()
    {
        if ($this->config['address_check']['active'] == 'true') {
            $billto_hash             = $this->payone->getAddressHash($_SESSION['billto']);
            $sendto_hash             = $this->payone->getAddressHash($_SESSION['sendto']);
            $billto_match            = $billto_hash == $_SESSION['payone_ac_billing_hash'];
            $sendto_match            = $sendto_hash == $_SESSION['payone_ac_delivery_hash'];
            $addresses_are_validated = $billto_match && $sendto_match;
        } else {
            // address check is inactive, treat addresses as validated
            $addresses_are_validated = true;
        }
        
        return $addresses_are_validated;
    }
    
    
    public function pre_confirmation_check()
    {
        if ($this->config['address_check']['active'] == 'true' && !$this->_addressesAreValidated()) {
            $_SESSION['payone_error'] = 'address_changed';
            xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT);
        }
        if ($this->config['credit_risk']['active'] == 'true'
            && $this->config['credit_risk']['timeofcheck'] == 'after') {
            $_SESSION['payone_cr_hash'] = isset($_SESSION['payone_cr_hash']) ? $_SESSION['payone_cr_hash'] : '';
            $credit_risk_checked        = $_SESSION['payone_cr_hash']
                                          == $this->payone->getAddressHash($_SESSION['billto']);
            if (!$credit_risk_checked && !isset($_GET['p1crskip'])) {
                // risk check has not been performed and user has not actively chosen to skip it
                xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_payone_cr.php');
            }
            $active_genre         = $this->_getActiveGenreIdentifier();
            $paymentgenre_allowed = false;
            foreach ($this->config['credit_risk']['checkforgenre'] as $checkforgenre) {
                if ($checkforgenre == $active_genre) {
                    $paymentgenre_allowed = $this->config[$active_genre]['allow_' . $_SESSION['payone_cr_result']]
                                            == 'true';
                    break;
                }
            }
            if ($paymentgenre_allowed == false) {
                $this->payone->log("credit_risk, after-selection mode: fail");
                $_SESSION['payone_error'] = $this->payone->get_text('credit_risk_failed');
                xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?payment_error='
                             . $this->code);
            }
        }
        
        return false;
    }
    
    
    public function confirmation()
    {
        $confirmation = [
            'title' => defined('MODULE_PAYMENT_' . strtoupper($this->code)
                               . '_TEXT_DESCRIPTION') ? (constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                  . '_TEXT_DESCRIPTION') . ' conf_DESC') : '',
        ];
        
        return $confirmation;
    }
    
    
    public function refresh()
    {
    }
    
    
    public function process_button()
    {
        $order = $GLOBALS['order'];
        $pb    = '';
        
        return $pb;
    }
    
    
    public function payment_action()
    {
        // $GLOBALS['order'], $_SESSION['tmp_oID'], $GLOBALS['order_totals']
    }
    
    
    public function _getInvoicingTransaction($orders_id)
    {
        $products  = $GLOBALS['order']->products;
        $totaldata = $GLOBALS['order']->getTotalData($orders_id);
        $invtrans  = new Payone_Api_Request_Parameter_Invoicing_Transaction();
        $temptotal = 0;
        foreach ($products as $product) {
            $item = new Payone_Api_Request_Parameter_Invoicing_Item();
            $item->setIt('goods');
            $item->setId($product['id']);
            $item->setPr(round($product['price'], 2));
            $item->setNo($product['qty']);
            $item->setDe($product['name']);
            $item->setVa($product['tax']);
            $temptotal += $product['qty'] * $product['price'];
            $invtrans->addItem($item);
        }
        foreach ($totaldata['data'] as $td) {
            if ($td['CLASS'] == 'ot_shipping') {
                $item = new Payone_Api_Request_Parameter_Invoicing_Item();
                $item->setIt('shipment');
                $item->setId('SHIPMENT');
                $item->setPr(round($td['VALUE'], 2));
                $item->setNo(1);
                $item->setDe($this->payone->get_text('shipping_cost'));
                $item->setVa(0);
                $temptotal += $td['VALUE'];
                $invtrans->addItem($item);
            }
        }
        $correction = round($totaldata['total'] - $temptotal, 2);
        $this->payone->log("correction required: $correction");
        if ($correction > 0) {
            $item = new Payone_Api_Request_Parameter_Invoicing_Item();
            $item->setIt('handling');
            $item->setId('HANDLING');
            $item->setPr(round($correction, 2));
            $item->setNo(1);
            $item->setDe($this->payone->get_text('misc_handling'));
            $item->setVa(0);
            $invtrans->addItem($item);
        } elseif ($correction < 0) {
            $item = new Payone_Api_Request_Parameter_Invoicing_Item();
            $item->setIt('voucher');
            $item->setId('VCHRDSCNT');
            $item->setPr(round($correction, 2));
            $item->setNo(1);
            $item->setDe($this->payone->get_text('voucher_or_discount'));
            $item->setVa(0);
            $invtrans->addItem($item);
        }
        
        return $invtrans;
    }
    
    
    public function before_process()
    {
        //$this->payone->log("before_process _GET:\n".print_r($_GET, true));
        $tmporder_exists = isset($_SESSION['tmp_oID']) && is_numeric($_SESSION['tmp_oID']);
        if ($this->config['address_check']['active'] == 'true' && !$tmporder_exists
            && !$this->_addressesAreValidated()) {
            // user changed billto/sendto address since we last checked -> go back to payment selection
            $this->payone->log("address change during checkout detected");
            $_SESSION['payone_error'] = 'address_changed';
            xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . "?payment_error=payone");
        }
        $returning_ok    = !empty($_GET['status']) && !empty($_GET['txid']) && !empty($_GET['userid']);
        $returning_error = !empty($_GET['status']) && !empty($_GET['errorcode']);
        if ($tmporder_exists && $returning_ok) {
            $this->payone->saveTransaction($_SESSION['tmp_oID'], $_GET['status'], $_GET['txid'], $_GET['userid']);
            if (strtoupper($_GET['status']) == 'REDIRECT' && !empty($_GET['redirecturl'])) {
                $this->payone->log("redirecting to " . $_GET['redirecturl']);
                xtc_redirect($_GET['redirecturl']);
            }
        }
        if ($tmporder_exists && $returning_error) {
            $this->payone->log($_GET['status'] . " for orders_id " . $_SESSION['tmp_oID'] . ": " . $_GET['errorcode']
                               . " - " . $_GET['errormessage'] . " - " . $_GET['customermessage']);
            $_SESSION['payone_error_message'] = strip_tags($_GET['customermessage']);
            unset($_SESSION['tmp_oID']);
            xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . "?payment_error=payone");
        }
        
        return false;
    }
    
    
    public function after_process()
    {
        $insert_id = $GLOBALS['insert_id'];
    }
    
    
    public function get_error()
    {
        if (isset($_SESSION['payone_error'])) {
            $error = ['error' => $_SESSION['payone_error']];
            unset($_SESSION['payone_error']);
            
            return $error;
        }
        
        return false;
    }
    
    
    public function check()
    {
        if (!isset ($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_PAYMENT_"
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
                             . $data['value'] . "', '" . $sort_order . "', '" . addslashes($data['type'])
                             . "', now())";
            xtc_db_query($install_query);
            $sort_order++;
        }
    }
    
    
    public function _configuration()
    {
        $config = [
            'STATUS'     => [
                'value' => 'True',
                'type'  => 'switcher ',
            ],
            'ALLOWED'    => [
                'value' => '',
            ],
            'ZONE'       => [
                'value' => '0',
                'type'  => 'geo-zone',
            ],
            /*
            'MERCHANT_ID' => array(
                'configuration_value' => '',
            ),
            'MERCHANT_LICENSE' => array(
                'configuration_value' => '',
            ),
            'SANDBOX' => array(
                'configuration_value' => 'True',
                'set_function' => 'switcher ',
            ),
            'TMPORDER_STATUS_ID' => array(
                'configuration_value' => '',
                'set_function' => 'xtc_cfg_pull_down_order_statuses(',
                'use_function' => 'xtc_get_order_status_name',
            ),
            'ORDER_STATUS_ID' => array(
                'configuration_value' => '',
                'set_function' => 'xtc_cfg_pull_down_order_statuses(',
                'use_function' => 'xtc_get_order_status_name',
            ),
            */
            'SORT_ORDER' => [
                'value' => '0',
            ],
        ];
        
        return $config;
    }
    
    
    public function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    /**
     * Determines the module's configuration keys
     *
     * @return array
     */
    public function keys()
    {
        $ckeys = array_keys($this->_configuration());
        $keys  = [];
        foreach ($ckeys as $k) {
            $keys[] = 'configuration/MODULE_PAYMENT_' . strtoupper($this->code) . '_' . $k;
        }
        
        return $keys;
    }
    
    
    public function isInstalled()
    {
        foreach ($this->keys() as $key) {
            if (!defined($key)) {
                return false;
            }
        }
        
        return true;
    }
    
    
    /* in $response:
        [clearing_bankaccountholder:protected] => PAYONE GmbH & Co. KG
        [clearing_bankcountry:protected] => DE
        [clearing_bankaccount:protected] => 022182003
        [clearing_bankcode:protected] => 21070024
        [clearing_bankiban:protected] => DE37210700240022182003
        [clearing_bankbic:protected] => DEUTDEDB210
        [clearing_bankcity:protected] => Kiel
        [clearing_bankname:protected] => Deutsche Bank
    */
    
    public function _saveClearingData($p_orders_id, $p_response)
    {
        if ($p_response instanceof Payone_Api_Response_Preauthorization_Approved
            || $p_response instanceof Payone_Api_Response_Authorization_Approved) {
            $cd_query = "INSERT INTO `payone_clearingdata` SET
				`bankaccountholder` = ':bankaccountholder',
				`bankcountry` = ':bankcountry',
				`bankaccount` = ':bankaccount',
				`bankcode` = ':bankcode',
				`bankiban` = ':bankiban',
				`bankbic` = ':bankbic',
				`bankcity` = ':bankcity',
				`bankname` = ':bankname',
				`orders_id` = ':orders_id'";
            $cd_query = strtr($cd_query,
                              [
                                  ':bankaccountholder' => xtc_db_input($p_response->getClearingBankaccountholder()),
                                  ':bankcountry'       => xtc_db_input($p_response->getClearingBankcountry()),
                                  ':bankaccount'       => xtc_db_input($p_response->getClearingBankaccount()),
                                  ':bankcode'          => xtc_db_input($p_response->getClearingBankcode()),
                                  ':bankiban'          => xtc_db_input($p_response->getClearingBankiban()),
                                  ':bankbic'           => xtc_db_input($p_response->getClearingBankbic()),
                                  ':bankcity'          => xtc_db_input($p_response->getClearingBankcity()),
                                  ':bankname'          => xtc_db_input($p_response->getClearingBankname()),
                                  ':orders_id'         => (int)$p_orders_id,
                              ]);
            xtc_db_query($cd_query);
            
            $orderValue = 0;
            /** @var \OrderReadService $orderReadService */
            $orderReadService = StaticGXCoreLoader::getService('OrderRead');
            $order            = $orderReadService->getOrderById(new IdType((int)$p_orders_id));
            /** @var OrderTotal $orderTotal */
            foreach ($order->getOrderTotals() as $orderTotal) {
                if ($orderTotal->getClass() === 'ot_total') {
                    $orderValue = $orderTotal->getValue();
                }
            }
            
            $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $db->insert('orders_payment_instruction',
                        [
                            'orders_id'      => (int)$p_orders_id,
                            'reference'      => (string)(int)$p_orders_id,
                            'bank_name'      => $p_response->getClearingBankname(),
                            'account_holder' => $p_response->getClearingBankaccountholder(),
                            'iban'           => $p_response->getClearingBankiban(),
                            'bic'            => $p_response->getClearingBankbic(),
                            'value'          => $orderValue,
                            'currency'       => $order->getCurrencyCode()->getCode(),
                        ]);
        }
    }
}

MainFactory::load_origin_class('payone_master');
