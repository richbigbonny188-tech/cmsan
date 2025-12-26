<?php
/*
 Released under the GNU General Public License (Version 2)
[http://www.gnu.org/licenses/gpl-2.0.html]
*/

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('hgwConf', $_SESSION['languages_id']);
$languageTextManager->init_from_lang_file('hppf', $_SESSION['languages_id']);

if (file_exists(DIR_WS_CLASSES . 'class.heidelpaygw.php')) {
    include_once(DIR_WS_CLASSES . 'class.heidelpaygw.php');
} else {
    require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.heidelpaygw.php');
}

class hppf_ORIGIN
{
    var $code, $title, $description, $enabled, $hgw, $pm, $tmpOrders;
    
    public $title_txt;
    public $sort_order;
    public $info;
    public $prefix;
    public $getConf;
    public $pmAv;
    public $pmAv_error;
    
    
    // class constructor
    function __construct()
    {
        GLOBAL $order, $language;
        
        $this->hgw                  = new heidelpayGW();
        $this->pm                   = 'pf';
        $this->code                 = 'hp' . $this->pm;
        $this->title_txt            = defined('MODULE_PAYMENT_HPPF_TEXT_TITLE') ? MODULE_PAYMENT_HPPF_TEXT_TITLE : '';
        $this->title                = sprintf(HGW_LOGO, DIR_WS_CATALOG) . $this->title_txt;
        $this->description          = MODULE_PAYMENT_HPPF_TEXT_DESC . '<br/><i>[' . $this->hgw->version . ']</i>'
                                      . $this->hgw->modulConfButton;
        $this->sort_order           = defined('MODULE_PAYMENT_HPPF_SORT_ORDER') ? MODULE_PAYMENT_HPPF_SORT_ORDER : '0';
        $this->enabled              = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                                      && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->info                 = defined('MODULE_PAYMENT_HPPF_TEXT_INFO') ? MODULE_PAYMENT_HPPF_TEXT_INFO : '';
        $this->tmpOrders            = true;
        $this->hgw->actualPaymethod = strtoupper($this->pm);
        $this->prefix               = 'configuration/MODULE_PAYMENT_HPPF_';
        $this->getConf              = $this->hgw->getConf;
        
        if (is_object($order)) {
            $this->update_status();
        }
    }
    
    
    function update_status()
    {
        GLOBAL $order;
        
        if (($this->enabled == true) && ((int)MODULE_PAYMENT_HPPF_ZONE > 0)) {
            $check_flag = false;
            $sql        = xtc_db_query("SELECT zone_id FROM " . TABLE_ZONES_TO_GEO_ZONES . " WHERE geo_zone_id = '"
                                       . MODULE_PAYMENT_HPPF_ZONE . "' AND zone_country_id = '"
                                       . $order->billing['country']['id'] . "' ORDER BY zone_id");
            
            while ($check = xtc_db_fetch_array($sql)) {
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
        GLOBAL $order, $formUrl;
        $getConf          = $this->getConf;
        $content          = [];
        $this->pmAv       = true;
        $this->pmAv_error = '';
        
        $getFormUrl = $this->hgw->getFormUrl($this->pm,
                                             $bookingMode,
                                             $_SESSION['customer_id'],
                                             $uid = null,
                                             (array)$order,
                                             $ppd_crit = null);
        
        //getting template Verion
        $templateVersion = gm_get_env_info('TEMPLATE_VERSION');
        
        // message if no testing account is set
        if ($getConf['transactionMode'] == '1' && isset($order)
            && strpos(strtolower(MODULE_PAYMENT_HPPF_TEST_ACCOUNT),
                      strtolower($order->customer['email_address'])) === false) {
            $this->pmAv       = false;
            $this->pmAv_error = HGW_DEBUGTEXT;
            
            if ($templateVersion >= 3.0) {
                // honeygrid
                $content = [
                    [
                        'title' => ' ',
                        'field' => $this->pmAv_error,
                    ],
                ];
            } else {
                // eyecandy
                $content = [
                    [
                        'title' => '',
                        'field' => $this->pmAv_error,
                    ],
                ];
            }
        } else {
            //check if billing address is CH and currency is CHF
            if ($order->billing['country']['iso_code_2'] != 'CH') {
                $this->pmAv       = false;
                $this->pmAv_error = HGW_WRONG_COUNTRY;
                
                if ($templateVersion >= 3.0) {
                    $content = [
                        [
                            'title' => ' ',
                            'field' => $this->pmAv_error,
                        ],
                    ];
                } else {
                    $content = [
                        [
                            'title' => '',
                            'field' => $this->pmAv_error,
                        ],
                    ];
                }
            } elseif ($order->info['currency'] != 'CHF') {
                $this->pmAv       = false;
                $this->pmAv_error = HGW_WRONG_CURRENCY;
                
                if ($templateVersion >= 3.0) {
                    $content = [
                        [
                            'title' => ' ',
                            'field' => $this->pmAv_error,
                        ],
                    ];
                } else {
                    $content = [
                        [
                            'title' => '',
                            'field' => $this->pmAv_error,
                        ],
                    ];
                }
                
                $content = [
                    [
                        'title' => '',
                        'field' => $this->pmAv_error,
                    ],
                ];
            } else {
                
                $bankBrands = json_decode(stripslashes($getFormUrl['CONFIG.BRANDS']), true);
                foreach ($bankBrands as $brand => $brandname) {
                    $optBrand .= '<option value="' . $brand . '">' . $brandname . '</option>';
                }
                if ($templateVersion >= 3.0) {
                    $content = [
                        [
                            'title' => ' ',
                            'field' => '
								<table>
									<tr><td><select id="accBrand" name="ACCOUNT.BRAND">' . $optBrand . '</select></td></tr>
								</table>'
                        ]
                    ];
                } else {
                    $content = [
                        [
                            'title' => '',
                            'field' => '
								<table>
									<tr><td><select id="accBrand" name="ACCOUNT.BRAND">' . $optBrand . '</select></td></tr>
								</table>'
                        ]
                    ];
                }
            }
        }
        
        // Workaround for Honeygrid template file
        if ($templateVersion >= 3.0) {
            // Honeygrid
            $content[0]['title'] = ' ';
        } else {
            // EyeCandy
            $content[0]['title'] = '';
        }
        
        return [
            'id'          => $this->code,
            'module'      => $this->title_txt,
            'fields'      => $content,
            'description' => $this->info
        ];
    }
    
    
    function pre_confirmation_check()
    {
        $_SESSION['HP']['ADDRESS.COUNTRY'] = 'CH';
        $_SESSION['HP']['ACCOUNT.BRAND']   = trim($_POST['ACCOUNT_BRAND']);
        
        if ($this->pmAv === false) {
            $_SESSION['redirect_error'] = $this->pmAv_error;
            $url                        = xtc_href_link(FILENAME_CHECKOUT_PAYMENT,
                                                        'payment_error=' . $this->code,
                                                        'SSL');
            
            xtc_redirect($url);
        }
        
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
    
    
    function before_process()
    {
        return false;
    }
    
    
    function payment_action()
    {
        $_SESSION['hp_tmp_oID']           = $_SESSION['tmp_oID'];
        $_SESSION['hp_tmp_glob']['order'] = json_encode($GLOBALS['order']);
        
        foreach ($GLOBALS as $key => $value) {
            if (is_int(strpos($key, 'ot_'))) {
                $_SESSION['hp_tmp_glob'][$key] = $value;
                $_SESSION['hp_tmp_otmod'][]    = $key . '.php';
            }
        }
        
        $url = xtc_href_link('ext/heidelpay/heidelpayGW_gateway.php', '', 'SSL');
        xtc_redirect($url);
        
        return true;
    }
    
    
    function after_process()
    {
        unset($_SESSION['hp_tmp_oID']);
        unset($_SESSION['hp_tmp_glob']);
        unset($_SESSION['hp_tmp_otmod']);
        
        return true;
    }
    
    
    function get_error()
    {
        $error = [
            'title' => MODULE_PAYMENT_HPPF_TEXT_ERROR,
            'error' => $_SESSION['redirect_error']
        ];
        unset($_SESSION['redirect_error']);
        
        return $error;
    }
    
    
    function check()
    {
        if (!isset ($this->_check)) {
            $sql          = xtc_db_query("SELECT `value` FROM `gx_configurations` where `key` = 'configuration/MODULE_PAYMENT_"
                                         . strtoupper($this->code) . "_STATUS'");
            $this->_check = xtc_db_num_rows($sql);
        }
        
        return $this->_check;
    }
    
    
    function install()
    {
        $this->hgw->checkRegTable();
        $this->hgw->checkOrderStatus();
        $this->hgw->checkTransactTable();
        
        $this->remove(true);
        
        $groupId = 6;
        $sqlBase = 'INSERT INTO `' . TABLE_CONFIGURATION . '` SET ';
        
        $inst   = [];
        $inst[] = [
            'key'          => $this->prefix . 'STATUS',
            'value'        => 'True',
            'type' => 'switcher',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'TEST_ACCOUNT',
            'value' => '',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'PROCESSED_STATUS_ID',
            'value' => '333',
            'type'  => 'order-status',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'PENDING_STATUS_ID',
            'value' => '2',
            'type'  => 'order-status',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'CANCELED_STATUS_ID',
            'value' => '330',
            'type'  => 'order-status',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'SORT_ORDER',
            'value' => '1.55',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'ALLOWED',
            'value' => '',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'ZONE',
            'value' => '',
            'type'  => 'geo-zone',
        ];
        
        foreach ($inst as $sort => $conf) {
            $sql = $sqlBase . ' ';
            foreach ($conf as $key => $val) {
                $sql .= '`' . addslashes($key) . '` = "' . $val . '", ';
            }
            $sql .= '`sort_order` = "' . $sort . '", ';
            $sql .= '`last_modified` = NOW() ';
            xtc_db_query($sql);
        }
    }
    
    
    function remove()
    {
        xtc_db_query("DELETE FROM `gx_configurations` where `key` IN ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    function keys()
    {
        return [
            'configuration/MODULE_PAYMENT_HPPF_STATUS',
            'configuration/MODULE_PAYMENT_HPPF_TEST_ACCOUNT',
            'configuration/MODULE_PAYMENT_HPPF_PROCESSED_STATUS_ID',
            'configuration/MODULE_PAYMENT_HPPF_PENDING_STATUS_ID',
            'configuration/MODULE_PAYMENT_HPPF_CANCELED_STATUS_ID',
            'configuration/MODULE_PAYMENT_HPPF_SORT_ORDER',
            'configuration/MODULE_PAYMENT_HPPF_ALLOWED',
            'configuration/MODULE_PAYMENT_HPPF_ZONE',
        ];
    }
}

MainFactory::load_origin_class('hppf');
