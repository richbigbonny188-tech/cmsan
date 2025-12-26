<?php
/*--------------------------------------------------------------------------------------------------
    SwixPostfinanceCheckoutBase.php 2023-03-29
    swisswebXperts GmbH
    https://www.swisswebxperts.ch
    Copyright (c) 2023 swisswebXperts GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

use Gambio\Core\Logging\LoggerBuilder;
use SwixPostfinanceCheckout\ApiClient;

class SwixPostfinanceCheckoutBase
{
    public $title;
    public $description;
    public $info;
    public $enabled;
    public $orderid;
    public $productive;
    public $modulConfButton;

    public $code          = '';
    public $codeUpperCase = '';
    public $sort_order = 0;
    protected $alias = '';
    protected $allowedCountries = '';

    protected $apiClient;
    protected $paymentMethodId;
    protected $paymentMethodBrandIds = [];

    protected $userId;
    protected $authenticationKey;
    protected $spaceId;

    /** @var $configurationStorage GXModuleConfigurationStorage */
    protected $configurationStorage;
    
    /**
     * @var LanguageTextManager|mixed
     */
    private $text;
    
    
    public function __construct()
    {
        global $order;

        $this->configurationStorage = MainFactory::create('GXModuleConfigurationStorage', 'Swix/PostfinanceCheckout');
        $this->text = MainFactory::create('LanguageTextManager', 'swixpostfinancecheckout', $_SESSION['languages_id']);
    
        $this->userId = $this->configurationStorage->get('user_id');
        $this->authenticationKey = $this->configurationStorage->get('authentication_key');
        $this->spaceId = $this->configurationStorage->get('space_id');

        if ($this->userId != '' && $this->authenticationKey != '') {
            $this->apiClient = new ApiClient($this->userId, $this->authenticationKey);
        }

        $this->codeUpperCase = strtoupper($this->code);

        $this->title = defined('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_TEXT_TITLE') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_TEXT_TITLE') : '';
        $this->description = defined('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_TEXT_DESCRIPTION') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_TEXT_DESCRIPTION') : '';
        $this->info  = defined('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_TEXT_INFO') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_TEXT_INFO') : '';
        $this->sort_order = defined('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_SORT_ORDER') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_SORT_ORDER') : $this->sort_order;
        $this->enabled = defined('MODULE_PAYMENT_' . $this->codeUpperCase . '_STATUS')
            && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->order_status = defined('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_ORDER_STATUS_ID') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_ORDER_STATUS_ID') : 0;
        $this->order_status_error = defined('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_ERROR_ORDER_STATUS_ID') ? constant('MODULE_PAYMENT_' . $this->codeUpperCase
            . '_ERROR_ORDER_STATUS_ID') : 0;

        if(defined('DIR_WS_ADMIN')) {
            $this->description .= '<a class="btn" href="' . DIR_WS_ADMIN . 'admin.php?do=SwixPostfinanceCheckout" style="width: 100%; margin:0;">' . $this->text->get_text('legend_basic_settings') . '</a>';
            $this->description .= '<br>Support: <a href="https://swisswebxperts.ch/postfinance-checkout" target="_blank">www.swisswebXperts.ch</a><br><br>';
        }

        if (is_object($order)) {
            $this->update_status();
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
        return ['id'          => $this->code,
                'module'      => $this->title,
                'description' => $this->info,
                'logo_url'    => DIR_WS_IMAGES . 'icons/payment/' . $this->code . '.png',
        ];
    }

    function pre_confirmation_check()
    {
        return false;
    }

    public function confirmation()
    {
        return false;
    }

    function process_button()
    {
        return false;
    }

    function payment_action()
    {
        $createTransaction = $this->createTransaction();
        $paymentPageUrl = '';

        try {
            $transaction = $this->apiClient->getTransactionService()->create($this->spaceId, $createTransaction);
            if ($transaction === false) {
                $ordersId = (int)$_SESSION['tmp_oID'];
                $this->setOrderStatus($ordersId,
                                      $this->order_status_error,
                                      $this->text->get_text('error_cannot_initialize_transaction'));
                $this->getLogger()->error("Transaction for order {$ordersId} could not be initialized!");
                throw new \Exception('error_cannot_initialize_transaction');
            }
    
            if ($this->paymentMethodId > 0) {
                $result = $this->apiClient->getTransactionService()->fetchPaymentMethods($this->spaceId, $transaction['id'], 'payment_page');
                $allowedPaymentMethodConfigurations = [];
                foreach ($result as $item) {
                    if ($item['paymentMethod'] == $this->paymentMethodId) {
                        $allowedPaymentMethodConfigurations[] = $item['id'];
                    }
                }

                if (count($allowedPaymentMethodConfigurations) > 0) {

                    $transactionPending = [
                        'id' => $transaction['id'],
                        'version' => $transaction['version'] + 1,
                        'allowedPaymentMethodConfigurations' => $allowedPaymentMethodConfigurations,
                        'allowedPaymentMethodBrands' => $this->paymentMethodBrandIds,
                    ];

                    $this->apiClient->getTransactionService()->update($this->spaceId, $transactionPending);
                }
            }

            $paymentPageUrl = $this->apiClient->getTransactionPaymentPageService()->paymentPageUrl($this->spaceId, $transaction['id']);

        } catch(Exception $e) {
            $_SESSION['swixpostfinancecheckout_error'] = $e->getMessage();
            xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_PAYMENT . '?payment_error=' . $this->code);
        }

        xtc_redirect($paymentPageUrl);
    }

    function before_process()
    {
        return false;
    }

    function after_process()
    {
        if ($this->order_status) {
            $this->setOrderStatus($GLOBALS['insert_id'], $this->order_status);
        }
    }

    function setOrderStatus($orders_id, $orders_status, $comment = '')
    {
        $insertId = new IdType((int)$orders_id);
        /** @var OrderWriteServiceInterface $orderWriteService */
        $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
        $orderWriteService->updateOrderStatus($insertId,
            new IntType((int)$orders_status),
            new StringType($comment),
            new BoolType(false));
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
        $this->createBasicStatus();

        $configSQL = "INSERT INTO `gx_configurations`
            (
                `key`,
                `value`,
                `sort_order`,
                `type`,
                `last_modified`
            ) VALUES
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_STATUS',
                'True',
                10,
                'switcher',
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_SORT_ORDER',
                '" . $this->sort_order . "',
                20,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_ALLOWED',
                '" . $this->allowedCountries . "',
                30,
                null,
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_ZONE',
                '0',
                40,
                'geo-zone',
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_ORDER_STATUS_ID',
                '" . $this->configurationStorage->get('basic_success_order_status') . "',
                50,
                'order-status',
                now()
            ),
            ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_ERROR_ORDER_STATUS_ID',
                '" . $this->configurationStorage->get('basic_failed_order_status') . "',
                60,
                'order-status',
                now()
            )
        ";
        xtc_db_query($configSQL);
        
        $aliasCheck = xtc_db_query('SELECT * FROM `gx_configurations` WHERE `key` = "configuration/MODULE_PAYMENT_'
                                   . $this->codeUpperCase . '_ALIAS"');
        if (xtc_db_num_rows($aliasCheck) < 1) {
            $aliasSQL = "INSERT INTO `gx_configurations`
                (
                    `key`,
                    `value`,
                    `sort_order`,
                    `type`,
                    `last_modified`
                ) VALUES
                ('configuration/MODULE_PAYMENT_" . $this->codeUpperCase . "_ALIAS',
                 '" . $this->alias . "',
                 null,
                 null,
                 now()
                )";
            xtc_db_query($aliasSQL);
        }
    }

    protected function createBasicStatus()
    {
        if ($this->configurationStorage->get('basic_success_order_status') == '') {
            $orderStatus = new OrderStatus();
            $orderStatus->setName(new LanguageCode(new StringType('en')), new StringType('Postfinance payed'));
            $orderStatus->setName(new LanguageCode(new StringType('de')), new StringType('Postfinance bezahlt'));
            $orderStatus->setColor(new StringType('2196F3'));
            /** @var OrderStatusService $orderStatusService */
            $orderStatusService = StaticGXCoreLoader::getService('OrderStatus');
            $orders_status_id = $orderStatusService->create($orderStatus);

            $this->configurationStorage->set('basic_success_order_status', $orders_status_id);
        }

        if ($this->configurationStorage->get('basic_failed_order_status')  == '') {

            $orderStatus = new OrderStatus();
            $orderStatus->setName(new LanguageCode(new StringType('en')), new StringType('Postfinance failed'));
            $orderStatus->setName(new LanguageCode(new StringType('de')), new StringType('Postfinance fehlgeschlagen'));
            $orderStatus->setColor(new StringType('E0412C'));

            /** @var OrderStatusService $orderStatusService */
            $orderStatusService = StaticGXCoreLoader::getService('OrderStatus');
            $orders_status_id = $orderStatusService->create($orderStatus);

            $this->configurationStorage->set('basic_failed_order_status', $orders_status_id);
        }
    }

    public function keys()
    {
        $resultSet = xtc_db_query("SELECT * FROM `gx_configurations` where `key` LIKE 'configuration/MODULE_PAYMENT_"
            . strtoupper($this->code) . "%'");

        $keys = [];
        while ($config = xtc_db_fetch_array($resultSet)) {
            if (strpos($config['key'], '_ALIAS') !== false) {
                continue;
            }

            $keys[] = $config['key'];
        }

        return $keys;
    }

    public function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }

    public function get_error()
    {
        if (isset($_SESSION['swixpostfinancecheckout_error'])) {
            $errorText = $this->text->get_text($_SESSION['swixpostfinancecheckout_error']);
            $error = ['error' => $errorText];
            unset($_SESSION['swixpostfinancecheckout_error']);

            return $error;
        }

        return false;
    }
    
    protected function createTransaction()
    {
        global $order;
    
        $lineItems = [];
    
        $uniqueId = 1;

        $amountIncludeTax = $order->info['total'];
        if ((bool)$_SESSION['customers_status']['customers_status_show_price_tax'] === false) {
            $amountIncludeTax += $order->info['tax'];
        }

        $lineItems[] = [
            'uniqueId'           => $uniqueId,
            'name'               => 'Order ' . $_SESSION['tmp_oID'],
            'quantity'           => 1,
            'type'               => 'PRODUCT',
            'amountIncludingTax' => round($amountIncludeTax, 2),
        ];
    
        $gender = $order->billing['gender'] === 'm' ? 'MALE' : 'FEMALE';
    
        $transaction = [
            'billingAddress'          => [
                'organizationName' => $order->billing['company'],
                'gender'           => $gender,
                'givenName'        => $order->billing['firstname'],
                'familyName'       => $order->billing['lastname'],
                'street'           => $order->billing['street_address'],
                'postcode'         => $order->billing['postcode'],
                'city'             => $order->billing['city'],
                'country'          => $order->billing['country']['iso_code_2'],
            ],
            'shippingAddress'         => [
                'organizationName' => $order->delivery['company'],
                'gender'           => $gender,
                'givenName'        => $order->delivery['firstname'],
                'familyName'       => $order->delivery['lastname'],
                'street'           => $order->delivery['street_address'],
                'postcode'         => $order->delivery['postcode'],
                'city'             => $order->delivery['city'],
                'country'          => $order->delivery['country']['iso_code_2'],
            ],
            'customerId'              => $_SESSION['customer_id'],
            'merchantReference'       => $_SESSION['tmp_oID'],
            'customerEmailAddress'    => $order->customer['email_address'],
            'shippingMethod'          => $order->info['shipping_method'],
            'currency'                => $order->info['currency'],
            'lineItems'               => $lineItems,
            'autoConfirmationEnabled' => true,
            'chargeRetryEnabled'      => true,
            'successUrl'              => GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_process.php',
            'failedUrl'               => GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_payment.php',
            'language'                => $_SESSION['language_code'],
            'metaData'                => [
                'payment_class' => $this->code,
            ],
        ];
    
        return $transaction;
    }
    
    public function callback($transaction)
    {
        $state = $transaction['state'];
        $order_id = $transaction['merchantReference'];

        if ($state === 'FULFILL') {
            $this->setOrderStatus($order_id, $this->order_status);
        } else if ($state === 'DECLINE') {
            $this->setOrderStatus($order_id, $this->order_status_error);
        }
    }

    public function getApiClient()
    {
        return $this->apiClient;
    }

    public function getSpaceId()
    {
        return $this->spaceId;
    }
    
    protected function getLogger()
    {
        static $logger;
        if ($logger === null) {
            $loggerBuilder = \LegacyDependencyContainer::getInstance()->get(LoggerBuilder::class);
            $logger  = $loggerBuilder->omitRequestData()->changeNamespace('swixpostfinancecheckout')->build();
        }
        return $logger;
    }
}