<?php
/* --------------------------------------------------------------
	ipayment.php 2020-02-04
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);

class ipayment_ORIGIN
{
    public $code      = 'ipayment';
    public $title, $description, $enabled;
    public $tmpOrders = true;
    public $tmpStatus = 0;
    public $sort_order;
    public $info;
    public $order_status;
    
    
    public function __construct()
    {
        global $order;
        $coo_lang_file_master = MainFactory::create_object('LanguageTextManager', [], true);
        $coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language'] . '/modules/payment/' . $this->code
                                                   . '.php');
    
        $this->title       = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                     . '_TEXT_TITLE') ? constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                 . '_TEXT_TITLE') : '';
        $this->description = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                     . '_TEXT_DESCRIPTION') ? (constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                        . '_TEXT_DESCRIPTION') . '<br><br>'
                                                               . constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                          . '_TEXT_DESCRIPTION_LINK') . '<br><br>'
                                                               . $this->_checkRequirements()) : '';
        $this->sort_order  = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                     . '_SORT_ORDER') ? constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                 . '_SORT_ORDER') : 0;
        $this->enabled     = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->info        = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                     . '_TEXT_INFO') ? constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                . '_TEXT_INFO') : '';
        if (defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_ORDER_STATUS_ID')
            && constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ORDER_STATUS_ID') > 0) {
            $this->order_status = constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ORDER_STATUS_ID');
        }
        $this->tmpStatus = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                                   . '_TMPORDER_STATUS_ID') ? (int)constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                                             . '_TMPORDER_STATUS_ID') : 0;
        
        if (is_object($order)) {
            $this->update_status();
        }
    }
    
    
    public function _checkRequirements()
    {
        $out = defined('MODULE_PAYMENT_' . strtoupper($this->code)
                       . '_SYSTEM_REQUIREMENTS') ? (constant('MODULE_PAYMENT_' . strtoupper($this->code)
                                                             . '_SYSTEM_REQUIREMENTS') . ':<br>') : '';
        if (defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], DIR_WS_ADMIN) !== false
            && defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_OK')
            && defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_MISSING')) {
            $has_curl = in_array('curl', get_loaded_extensions());
            $out      .= "cURL: " . ($has_curl ? '<span style="color:green">' . constant('MODULE_PAYMENT_'
                                                                                         . strtoupper($this->code)
                                                                                         . '_OK')
                                                 . '</span>' : '<span style="color:red">' . constant('MODULE_PAYMENT_'
                                                                                                     . strtoupper($this->code)
                                                                                                     . '_MISSING')
                                                               . '</span>') . '<br>';
        }
    
        return $out;
    }
    
    
    public function update_status()
    {
        global $order;
        
        if (($this->enabled == true) && ((int)constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_ZONE') > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . constant("MODULE_PAYMENT_" . strtoupper($this->code) . "_ZONE")
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
            'fields'      => [],
        ];
        
        return $selection;
    }
    
    
    public function pre_confirmation_check()
    {
        return false;
    }
    
    
    public function confirmation()
    {
        $confirmation = [
            'title' => constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_TEXT_DESCRIPTION'),
        ];
        
        return $confirmation;
    }
    
    
    public function refresh()
    {
    }
    
    
    public function process_button()
    {
        return '';
    }
    
    
    public function payment_action()
    {
        xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_ipayment.php');
    }
    
    
    public function before_process()
    {
        return false;
    }
    
    
    public function after_process()
    {
        $insert_id = $GLOBALS['insert_id'];
        
        if (isset($_SESSION['ipayment_response'][$insert_id])) {
            $request  = $_SESSION['ipayment_response'][$insert_id];
            $order    = new order($insert_id);
            $ipayment = new GMIPayment($order->info['payment_method']);
            $ipayment->logResponse($insert_id, $request);
            
            if ($request['ret_status'] == 'SUCCESS') {
                /** @var OrderWriteServiceInterface $orderWriteService */
                $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
                $orderWriteService->updateOrderStatus(new IdType((int)$insert_id),
                                                      new IntType((int)$this->order_status),
                                                      new StringType(''),
                                                      new BoolType(false));
            } elseif ($request['ret_status'] == 'REDIRECT') {
                die('this should never happen'); // because we're using normal/silent mode, which handles necessary redirections on its own
            } else { // ERROR
                $_SESSION['ipayment_error'] = $request['ret_errormsg'];
                xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . 'checkout_payment.php?error=' . $this->code);
            }
            unset($_SESSION['ipayment_response'][$insert_id]);
        } else {
            die('payment failed');
        }
    }
    
    
    public function get_error()
    {
        if (isset($_SESSION['ipayment_error'])) {
            $error = ['error' => $_SESSION['ipayment_error']];
            unset($_SESSION['ipayment_error']);
            
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
        $defaultOrderStatus = [
            'TMPORDER_STATUS_ID'   => [
                'names' => ['en' => 'ipayment temporary', 'de' => 'ipayment temporaer'],
                'color' => '2196F3',
            ],
            'ORDER_STATUS_ID'      => [
                'names' => ['en' => 'ipayment paid', 'de' => 'ipayment bezahlt'],
                'color' => '45a845',
            ],
            'ERRORORDER_STATUS_ID' => [
                'names' => ['en' => 'ipayment error', 'de' => 'ipayment Fehler'],
                'color' => 'e0412c',
            ]
        ];
        foreach ($defaultOrderStatus as $configKey => $orderStatusDefaults) {
            $this->updateConfiguration($configKey,
                                       $this->getOrdersStatus($orderStatusDefaults['names'],
                                                              $orderStatusDefaults['color']));
        }
    }
    
    
    public function _configuration()
    {
        $config = [
            'STATUS'               => [
                'value' => 'True',
                'type'  => 'switcher ',
            ],
            'ALLOWED'              => [
                'value' => '',
            ],
            'ACCOUNT_ID'           => [
                'value' => '99999',
            ],
            'APPLICATION_ID'       => [
                'value' => '99998',
            ],
            'APPLICATION_PASSWORD' => [
                'value' => '0',
            ],
            'ADMINACTION_PASSWORD' => [
                'value' => '5cfgRT34xsdedtFLdfHxj7tfwx24fe',
            ],
            'SECURITY_KEY'         => [
                'value' => 'testtest',
            ],
            'AUTH_MODE'            => [
                'value' => 'auth',
                'type'  => 'auth-mode',
            ],
            'ZONE'                 => [
                'value' => '0',
                'type'  => 'geo-zone',
            ],
            'TMPORDER_STATUS_ID'   => [
                'value' => '',
                'type'  => 'order-status',
            ],
            'ORDER_STATUS_ID'      => [
                'value' => '',
                'type'  => 'order-status',
            ],
            'ERRORORDER_STATUS_ID' => [
                'value' => '',
                'type'  => 'order-status',
            ],
            'SORT_ORDER'           => [
                'value' => '0',
            ],
        ];
        
        return $config;
    }
    
    
    protected function updateConfiguration($configurationKey, $configurationValue)
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->where('key', 'configuration/MODULE_PAYMENT_' . strtoupper($this->code) . '_' . $configurationKey);
        $db->update('gx_configurations', ['value' => $configurationValue]);
    }
    
    
    protected function getOrdersStatus($names, $color)
    {
        $orderStatusId      = null;
        $orderStatusService = StaticGXCoreLoader::getService('OrderStatus');
        /** @var \OrderStatusInterface $orderStatus */
        foreach ($orderStatusService->findAll() as $orderStatus) {
            foreach ($names as $languageCode => $statusName) {
                if ($orderStatus->getName(MainFactory::create('LanguageCode', new StringType($languageCode)))
                    === $statusName) {
                    $orderStatusId = $orderStatus->getId();
                    break 2;
                }
            }
        }
        if ($orderStatusId === null) {
            $newOrderStatus = MainFactory::create('OrderStatus');
            foreach ($names as $languageCode => $statusName) {
                $newOrderStatus->setName(MainFactory::create('LanguageCode', new StringType($languageCode)),
                                         new StringType($statusName));
            }
            $newOrderStatus->setColor(new StringType($color));
            $orderStatusId = $orderStatusService->create($newOrderStatus);
        }
        
        return $orderStatusId;
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
    
}

MainFactory::load_origin_class('ipayment');
