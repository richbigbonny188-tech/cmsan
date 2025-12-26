<?php

/* --------------------------------------------------------------
   postfinance.php 2023-07-07 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

/* -----------------------------------------------------------------------------------------
   $Id: postfinance.php, v.2.1 swisswebXperts GmbH
   2014-07-18 swisswebXperts GmbH

	 Copyright (c) 2009 swisswebXperts GmbH www.swisswebxperts.ch
	 Released under the GNU General Public License (Version 2)
	 [http://www.gnu.org/licenses/gpl-2.0.html]
   ---------------------------------------------------------------------------------------*/

class postfinance_ORIGIN
{
    var $title, $description, $enabled, $orderid, $productive;
    
    public $code          = 'postfinance';
    public $codeUpperCase = '';
    public $pspid         = '';
    
    protected $paymentMethod     = '';
    protected $paymentBrand      = '';
    protected $paymentMethodList = [];
    
    protected $shaMode = 'sha512';
    
    public $info;
    public $charset;
    public $sort_order;
    public $order_status;
    public $order_status_error;
    public $tmpOrders;
    public $form_action_url;
    public $tmpStatus;
    
    
    public function __construct()
    {
        global $order;
        
        $this->codeUpperCase = strtoupper($this->code);
    
        $this->title       = defined('MODULE_PAYMENT_' . $this->codeUpperCase
                                     . '_TEXT_TITLE') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
                                                                 . '_TEXT_TITLE') : '';
        $this->description = defined('MODULE_PAYMENT_' . $this->codeUpperCase
                                     . '_TEXT_DESCRIPTION') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
                                                                       . '_TEXT_DESCRIPTION') : '';
        $this->info        = defined('MODULE_PAYMENT_' . $this->codeUpperCase
                                     . '_TEXT_INFO') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
                                                                . '_TEXT_INFO') : '';
        $this->sort_order  = defined('MODULE_PAYMENT_' . $this->codeUpperCase
                                     . '_SORT_ORDER') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
                                                                 . '_SORT_ORDER') : 0;
        $this->enabled     = defined('MODULE_PAYMENT_' . $this->codeUpperCase . '_STATUS')
                             && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        
        if (defined('MODULE_PAYMENT_POSTFINANCEAG_BASIC_ORDER_STATUS_ID')
            && (int)MODULE_PAYMENT_POSTFINANCEAG_BASIC_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_POSTFINANCEAG_BASIC_ORDER_STATUS_ID;
        }
        
        if (defined('MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR_ORDER_STATUS_ID')
            && (int)MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR_ORDER_STATUS_ID > 0) {
            $this->order_status_error = MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR_ORDER_STATUS_ID;
        }
        
        if (is_object($order)) {
            $this->update_status();
        }
        
        $this->productive = defined('MODULE_PAYMENT_POSTFINANCEAG_BASIC_PRODUCTIVE') ? MODULE_PAYMENT_POSTFINANCEAG_BASIC_PRODUCTIVE : 'False';
        $this->charset    = defined('MODULE_PAYMENT_POSTFINANCEAG_BASIC_UTF8') ? MODULE_PAYMENT_POSTFINANCEAG_BASIC_UTF8 : 'UTF8';
        
        if ($this->productive == 'True') {
            // PRODUCTIVE LINK
            if ($this->charset == 'UTF8') {
                $this->form_action_url = 'https://e-payment.postfinance.ch/ncol/prod/orderstandard_utf8.asp'; //Link UTF8
            } else {
                $this->form_action_url = 'https://e-payment.postfinance.ch/ncol/prod/orderstandard.asp'; // Link ISO
            }
            $this->pspid = MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_PRODUCTIVE;
        } else {
            // TEST LINK
            if ($this->charset == 'UTF8') {
                $this->form_action_url = 'https://e-payment.postfinance.ch/ncol/test/orderstandard_utf8.asp'; //Link UTF8
            } else {
                $this->form_action_url = 'https://e-payment.postfinance.ch/ncol/test/orderstandard.asp'; // Link ISO
            }
            
            $this->pspid = defined('MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_TEST') ? MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_TEST : '';
        }
        
        $this->tmpOrders = true;
        $this->tmpStatus = 0;
    }
    
    
    function update_status()
    {
        global $order;
        
        if (($this->enabled == true) && ((int)constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_ZONE') > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_ZONE')
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
    
    
    function javascript_validation()
    {
        return false;
    }
    
    
    function selection()
    {
        global $order;
        $this->info = '';
        
        if (MODULE_PAYMENT_POSTFINANCEAG_BASIC_CURRENCY != 'Selected Currency') {
            
            if ($order instanceof order && MODULE_PAYMENT_POSTFINANCEAG_BASIC_CURRENCY != $order->info['currency']) {
                return false;
            }
        }
        
        // SWIX return array ('id' => $this->code, 'module' => $this->title, 'description' => $this->info);
        //SWIX Modul Zahlungsgebühren
        $selection = ['id' => $this->code, 'module' => $this->title, 'description' => $this->info];
        
        if (defined('MODULE_OT_PAYMENTFEE_STATUS') && MODULE_OT_PAYMENTFEE_STATUS === 'True') {
            include_once(DIR_FS_CATALOG . '/includes/modules/order_total/ot_paymentfee.php');
            
            $arrCosts                 = ot_paymentfee::getPaymentCosts($this->code);
            $selection['module_cost'] = $arrCosts['text'];
        }
        
        return $selection;
        // swix end
    }
    
    
    function pre_confirmation_check()
    {
        return false;
    }
    
    
    function confirmation()
    {
        return false;
    }
    
    
    function process_button()
    {
        return false;
    }
    
    
    function payment_action()
    {
        global $order, $xtPrice, $insert_id;
    
        if (!defined('MODULE_PAYMENT_' . $this->codeUpperCase . '_CURRENCY')
            || constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_CURRENCY') === 'Selected Currency') {
            $currency = $_SESSION['currency'];
        } else {
            $currency = constant('MODULE_PAYMENT_' . $this->codeUpperCase . '_CURRENCY');
        }
        
        if (MODULE_PAYMENT_POSTFINANCEAG_BASIC_LANGUAGE == 'Selected language') {
            $language = $_SESSION['language_code'];
            
            switch ($language) {
                case 'en':
                    $language = 'en_US';
                    break;
                case 'fr':
                    $language = 'fr_FR';
                    break;
                case 'nl':
                    $language = 'nl_NL';
                    break;
                case 'be':
                    $language = 'nl_BE';
                    break;
                case 'it':
                    $language = 'it_IT';
                    break;
                case 'de':
                    $language = 'de_DE';
                    break;
                case 'es':
                    $language = 'es_ES';
                    break;
                case 'no':
                    $language = 'no_NO';
                    break;
                case 'tr':
                    $language = 'tr_TR';
                    break;
                default :
                    $language = 'en_US';
                    break;
            }
        } else {
            $language = MODULE_PAYMENT_POSTFINANCEAG_BASIC_LANGUAGE;
        }
        
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0
            && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
            $amount = round($order->info['total'] + $order->info['tax'], $xtPrice->get_decimal_places($currency));
        } else {
            $amount = round($order->info['total'], $xtPrice->get_decimal_places($currency));
        }
        $amount = $amount * 100;
        
        if (ENABLE_SSL == true) {
            $homeurl = HTTPS_SERVER;
        } else {
            $homeurl = HTTP_SERVER;
        }
        
        $catalogurl = $homeurl . DIR_WS_CATALOG;
        
        // Alphabetisch sortiert
        $arrParams = [
            'amount'       => $amount,
            'currency'     => $currency,
            'language'     => $language,
            'homeurl'      => 'none',
            'orderID'      => $insert_id,
            'PSPID'        => $this->pspid,
            'CN'           => $order->customer['firstname'] . ' ' . $order->customer['lastname'],
            'EMAIL'        => $order->customer['email_address'],
            'owneraddress' => $order->customer['street_address'],
            'ownerZIP'     => $order->customer['postcode'],
            'ownertown'    => $order->customer['city'],
            'ownercty'     => $order->customer['country']['iso_code_2'],
            'TITLE'        => STORE_NAME,
            'accepturl'    => $catalogurl . 'checkout_process.php',
            'declineurl'   => $catalogurl . 'checkout_payment.php',
            'exceptionurl' => $catalogurl . 'checkout_payment.php',
            'cancelurl'    => $catalogurl . 'checkout_payment.php',
            'backurl'      => $catalogurl . 'checkout_payment.php',
            'COMPLUS'      => $catalogurl,
        ];
        
        if (strlen($this->paymentMethod) > 0) {
            $arrParams['PM'] = $this->paymentMethod;
        }
        
        if (strlen($this->paymentBrand) > 0) {
            $arrParams['BRAND'] = $this->paymentBrand;
        }
        
        if (count($this->paymentMethodList) > 0) {
            $arrParams['PMLIST'] = implode(';', $this->paymentMethodList);
        }
        
        if (strlen($order->customer['telephone']) > 0) {
            $arrParams['ownertelno'] = $order->customer['telephone'];
        }
        
        // Alphabetisch sortieren
        function my_sort($a, $b)
        {
            $a = strtolower($a);
            $b = strtolower($b);
            
            if ($a == $b) {
                return 0;
            }
            
            return ($a < $b) ? -1 : 1;
        }
        
        uksort($arrParams, "my_sort");
        
        $query  = '';
        $shaStr = '';
        
        foreach ($arrParams as $key => $value) {
            $query  .= $key . '=' . urlencode($value) . '&';
            $shaStr .= strtoupper($key) . '=' . $value . MODULE_PAYMENT_POSTFINANCEAG_BASIC_SHA_SIGNATURE;
        }
        
        $shasign = hash("sha512", $shaStr);
        $query   .= 'SHASign=' . strtoupper($shasign);
        
        xtc_redirect($this->form_action_url . '?' . $query);
        exit;
    }
    
    
    function before_process()
    {
        return false;
    }
    
    
    function after_process()
    {
        if ($this->checkResponse($_GET)) {
            $this->setPaymentInfo($_GET['orderID'], $_GET);
            $this->setOrderStatus($_GET['orderID'], $this->order_status);
        }
    }
    
    
    function checkResponse($params)
    {
        if (isset($params['SHASIGN'])) {
            $shasign = $params['SHASIGN'];
            unset($params['SHASIGN']);
            
            if (isset($params['tpl'])) {
                unset($params['tpl']);
            }
            
            return strtoupper($this->getSHAFromData($params)) == strtoupper($shasign);
        }
        
        return false;
    }
    
    
    function setPaymentInfo($orders_id, $params)
    {
        $payment_transaction_no = '';
        if (isset($params['PAYID'])) {
            $payment_transaction_no = $params['PAYID'];
        }
        $cc_type = '';
        if (isset($params['BRAND'])) {
            $cc_type = $params['BRAND'];
        }
        
        if ($payment_transaction_no != '' || $cc_type != '') {
            xtc_db_query("UPDATE " . TABLE_ORDERS . "
                SET orders_ident_key = '" . xtc_db_input($payment_transaction_no) . "',
                cc_type = '" . xtc_db_input($cc_type) . "' WHERE orders_id='" . (int)$orders_id . "'");
        }
    }
    
    
    function setOrderStatus($orders_id, $orders_status)
    {
        $insertId = new IdType((int)$orders_id);
        /** @var OrderWriteServiceInterface $orderWriteService */
        $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
        $orderWriteService->updateOrderStatus($insertId,
                                              new IntType((int)$orders_status),
                                              new StringType(''),
                                              new BoolType(false));
    }
    
    
    function getSHAFromData($data)
    {
        $shaStr = '';
        uksort($data, [$this, 'shaSort']);
        
        foreach ($data as $key => $value) {
            if ($value == '') {
                continue;
            }
            
            $value = stripslashes($value);
            
            $shaStr .= strtoupper($key) . '=' . $value . MODULE_PAYMENT_POSTFINANCEAG_BASIC_SHA_SIGNATURE;
        }
        
        return hash($this->shaMode, $shaStr);
    }
    
    
    function output_error()
    {
        $error = [
            'title' => MODULE_PAYMENT_POSTFINANCEAG_BASIC_TEXT_ERROR,
            'error' => MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR
        ];
    }
    
    
    function check()
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
        $configSQL = "INSERT INTO `gx_configurations`
            (
                `key`,
                `value`,
                `sort_order`,
                `type`,
                `last_modified`
            ) VALUES
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_STATUS',
                'True',
                10,
                'switcher ',
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_SORT_ORDER',
                '0',
                20,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_UTF8',
                'UTF8',
                25,
                'utf8-iso',
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_PRODUCTIVE',
                'False',
                30,
                'switcher ',
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_TEST',
                '',
                40,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_PSPID_PRODUCTIVE',
                '',
                50,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_SHA_SIGNATURE',
                '',
                60,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_LANGUAGE',
                'Selected language',
                70,
                'postfinance-basic-lang',
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_ALLOWED',
                'CH,LI',
                80,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_CURRENCY',
                'CHF',
                90,
                'chf-eur-usd',
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_ZONE',
                '0',
                100,
                'geo-zone',
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_ORDER_STATUS_ID',
                '0',
                110,
                'order-status',
                now()
            ),
            ('configuration/MODULE_PAYMENT_POSTFINANCEAG_BASIC_ERROR_ORDER_STATUS_ID',
                '0',
                120,
                'order-status',
                now()
            )
        ";
        xtc_db_query($configSQL);
    }
    
    
    function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    function keys()
    {
        $resultSet = xtc_db_query("SELECT * FROM `gx_configurations` where `key` LIKE 'configuration/MODULE_PAYMENT_"
                                  . strtoupper($this->code) . "%'
            ORDER BY `sort_order`");
        
        $keys = [];
        while ($config = xtc_db_fetch_array($resultSet)) {
            $keys[] = $config['key'];
        }
        
        return $keys;
    }
    
    
    function processCallback()
    {
        include_once(DIR_FS_CATALOG . 'gm/inc/gm_get_conf.inc.php');
        
        //$this->setDebug();
        
        if ($this->checkResponse($_POST)) {
            
            if (isset($_POST['COMPLUS']) && $_POST['COMPLUS'] != HTTP_SERVER . DIR_WS_CATALOG
                && $_POST['COMPLUS'] != HTTPS_SERVER . DIR_WS_CATALOG) {
                $this->redirectCallback($_POST);
            }
            
            $orderId = (int)$_POST['orderID'];
            
            $order = new order($orderId);
            
            if ($this->isAccepted($_POST['STATUS'])) {
                
                if (!$this->isOrderSent($orderId)) {
                    
                    $this->setPaymentInfo($orderId, $_POST);
                    
                    $language = $this->getOrderLanguage($orderId);
                    
                    $_SESSION['language'] = $language;
                    
                    if (!defined('DATE_FORMAT_LONG')) {
                        define('DATE_FORMAT_LONG', '%d.%m.%Y');
                    }
                    
                    $coo_recreate_order = MainFactory::create_object('RecreateOrder', [$orderId]);
                    $coo_recreate_order->getHtml();
                    
                    $this->setOrderStatus($orderId, $this->order_status);
                    
                    // create subject
                    include_once(DIR_FS_CATALOG . 'gm/inc/gm_get_content.inc.php');
                    
                    $t_subject = gm_get_content('EMAIL_BILLING_SUBJECT_ORDER', $_SESSION['languages_id']);
                    if (empty($t_subject)) {
                        $t_subject = EMAIL_BILLING_SUBJECT_ORDER;
                    }
                    $order_subject = str_replace('{$nr}', $orderId, $t_subject);
    
                    if (extension_loaded('intl')) {
                        $order_date = DateFormatter::formatAsFullDate(new DateTime(),
                                                                      new LanguageCode(new StringType($_SESSION['language_code'])));
                    } else {
                        $order_date = date(DATE_FORMAT_LONG);
                    }
                    $order_subject = str_replace('{$date}', $order_date, $order_subject);
                    
                    $html_mail = $coo_recreate_order->getHtml();
                    $txt_mail  = $coo_recreate_order->getTxtMailBody();
                    
                    // send mail to admin
                    // BOF GM_MOD:
                    
                    if (SEND_EMAILS == 'true') {
                        // get the sender mail adress. e.g. Host Europe has problems with the customer mail adress.
                        $from_email_address = $order->customer['email_address'];
                        if (SEND_EMAIL_BY_BILLING_ADRESS == 'SHOP_OWNER') {
                            $from_email_address = EMAIL_BILLING_ADDRESS;
                        }
                        xtc_php_mail($from_email_address,
                            $order->customer['firstname'] . ' ' . $order->customer['lastname'], EMAIL_BILLING_ADDRESS,
                            STORE_NAME, EMAIL_BILLING_FORWARDING_STRING, $order->customer['email_address'],
                            $order->customer['firstname'] . ' ' . $order->customer['lastname'], '', '', $order_subject,
                            $html_mail, $txt_mail);
                    }
                    // send mail to customer
                    // BOF GM_MOD:
                    if (SEND_EMAILS == 'true') {
                        $gm_mail_status = xtc_php_mail(EMAIL_BILLING_ADDRESS, EMAIL_BILLING_NAME,
                            $order->customer['email_address'],
                            $order->customer['firstname'] . ' ' . $order->customer['lastname'], '',
                            EMAIL_BILLING_REPLY_ADDRESS, EMAIL_BILLING_REPLY_ADDRESS_NAME, '', '', $order_subject,
                            $html_mail, $txt_mail);
                        
                        if ($gm_mail_status) {
                            xtc_db_query("
											UPDATE
												" . TABLE_ORDERS . "
											SET
												gm_send_order_status		= '1',
												gm_order_send_date			= NOW()
											WHERE
												orders_id = '" . (int)$orderId . "'
										");
                        }
                    }
                    
                    //Clear Cart
                    $customer_id = $order->customer['ID'];
                    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id
                                 . "'");
                    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '"
                                 . (int)$customer_id . "'");
                }
            }
        } else {
            throw new Exception('Ungültiger SHAIN');
        }
    }
    
    
    function redirectCallback($postData)
    {
        $requestURI = $postData['COMPLUS'] . 'callback/postfinance/callback.php';
        
        $request = curl_init($requestURI);
        
        curl_setopt($request, CURLOPT_POST, 1);
        curl_setopt($request, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($request);
        curl_close($request);
        
        echo $response;
        exit;
    }
    
    
    function isAccepted($status)
    {
        return in_array($status, [5, 9, 91]);
    }
    
    
    function isOrderSent($orderId)
    {
        $resultSet = xtc_db_query("SELECT * FROM " . TABLE_ORDERS . " WHERE orders_id =" . (int)$orderId
                                  . " AND gm_send_order_status = 1");
        
        return xtc_db_num_rows($resultSet) >= 1;
    }
    
    
    function getOrderLanguage($orderId)
    {
        $resultSet = xtc_db_query("SELECT language FROM " . TABLE_ORDERS . " WHERE orders_id =" . (int)$orderId);
        $record    = xtc_db_fetch_array($resultSet);
        
        return $record['language'];
    }
    
    
    function getOrderStatus($orderId)
    {
        $resultSet = xtc_db_query("SELECT orders_status FROM " . TABLE_ORDERS . " WHERE orders_id =" . (int)$orderId);
        $record    = xtc_db_fetch_array($resultSet);
        
        return $record['orders_status'];
    }
    
    
    function shaSort($a, $b)
    {
        $a = strtolower($a);
        $b = strtolower($b);
        
        if ($a == $b) {
            return 0;
        }
        
        return ($a < $b) ? -1 : 1;
    }
    
    
    private function setDebug()
    {
        $_POST = [
            "orderID"     => '400382',
            "currency"    => 'CHF',
            "amount"      => '31.08',
            "PM"          => 'PostFinance Card',
            "ACCEPTANCE"  => 'TEST',
            "STATUS"      => '9',
            "CARDNO"      => '',
            "ED"          => '',
            "CN"          => 'Sabine N�f',
            "TRXDATE"     => '02/18/14',
            "PAYID"       => '28182456',
            "NCERROR"     => '0',
            "BRAND"       => 'PostFinance Card',
            "CREDITDEBIT" => '',
            "IPCTY"       => 'CH',
            "CCCTY"       => 'CH',
            "ECI"         => '5',
            "CVCCheck"    => '',
            "AAVCheck"    => '',
            "VC"          => '',
            "AAVZIP"      => 'NO',
            "AAVADDRESS"  => 'NO',
            "COMPLUS"     => 'http://swixtest.ch/gambio-dev/',
            "IP"          => '46.14.156.123',
            "SHASIGN"     => 'EFE533ACE6C32FF1FCE2891D3B60F57C248385E77FF08443786E14D91311613DD24ED0D07B126A1926E18FFFBF86F5557D0676CE9684E98351B6B2A439150FCB',
        ];
    }
}

MainFactory::load_origin_class('postfinance');
