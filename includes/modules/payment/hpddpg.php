<?php
/*
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
*/

$languageTextManager = MainFactory::create_object('LanguageTextManager', [], true);
$languageTextManager->init_from_lang_file('hgwConf', $_SESSION['languages_id']);
$languageTextManager->init_from_lang_file('hpddpg', $_SESSION['languages_id']);

if (file_exists(DIR_WS_CLASSES . 'class.heidelpaygw.php')) {
    include_once(DIR_WS_CLASSES . 'class.heidelpaygw.php');
} else {
    require_once(DIR_FS_CATALOG . DIR_WS_CLASSES . 'class.heidelpaygw.php');
}

class hpddpg_ORIGIN
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
        $this->pm                   = 'ddpg';
        $this->code                 = 'hp' . $this->pm;
        $this->title_txt            = defined('MODULE_PAYMENT_HPDDPG_TEXT_TITLE') ? MODULE_PAYMENT_HPDDPG_TEXT_TITLE : '';
        $this->title                = sprintf(HGW_LOGO, DIR_WS_CATALOG) . $this->title_txt;
        $this->description          = MODULE_PAYMENT_HPDDPG_TEXT_DESC . '<br/><i>[' . $this->hgw->version . ']</i>'
                                      . $this->hgw->modulConfButton;
        $this->sort_order           = defined('MODULE_PAYMENT_HPDDPG_SORT_ORDER') ? MODULE_PAYMENT_HPDDPG_SORT_ORDER : '0';
        $this->enabled              = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                                      && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->info                 = defined('MODULE_PAYMENT_HPDDPG_TEXT_INFO') ? MODULE_PAYMENT_HPDDPG_TEXT_INFO : '';
        $this->tmpOrders            = true;
        $this->hgw->actualPaymethod = strtoupper($this->pm);
        $this->prefix               = 'configuration/MODULE_PAYMENT_HPDDPG_';
        $this->getConf              = $this->hgw->getConf;
        
        if (is_object($order)) {
            $this->update_status();
        }
    }
    
    
    function update_status()
    {
        GLOBAL $order;
        
        if (($this->enabled == true) && ((int)MODULE_PAYMENT_HPDDPG_ZONE > 0)) {
            $check_flag = false;
            $sql        = xtc_db_query("SELECT zone_id FROM " . TABLE_ZONES_TO_GEO_ZONES . " WHERE geo_zone_id = '"
                                       . MODULE_PAYMENT_HPDDPG_ZONE . "' AND zone_country_id = '"
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
        
        // save supplimential fields in session if fields are set
        
        if (isset($_POST['DDPG_NAME_SALUTATION'])) {
            $_SESSION['HP']['NAME.SALUTATION'] = htmlspecialchars($_POST['DDPG_NAME_SALUTATION']);
            
            if (empty($_POST['DDPG_NAME_SALUTATION'])) {
                $this->pmAv       = false;
                $this->pmAv_error = HGW_ERROR_SALUTATION;
                
                $content = [
                    [
                        'title' => '',
                        'field' => $this->pmAv_error,
                    ],
                ];
            }
        }
        
        if (isset($_POST['DDPG_NAME_BIRTHDAY'])) {
            $_SESSION['HP']['NAME.BIRTHDATE'] = htmlspecialchars($_POST['DDPG_NAME_BIRTHYEAR'] . '-'
                                                                 . $_POST['DDPG_NAME_BIRTHMONTH'] . '-'
                                                                 . $_POST['DDPG_NAME_BIRTHDAY']);
            
            if (empty($_POST['DDPG_NAME_BIRTHDAY'])
                || (time() - strtotime($_SESSION['HP']['NAME.BIRTHDATE'])) < (18 * 31536000)) {
                $this->pmAv       = false;
                $this->pmAv_error = HGW_ERROR_BIRTHDAY;
                
                $content = [
                    [
                        'title' => '',
                        'field' => $this->pmAv_error,
                    ],
                ];
            }
        }
        
        if (isset($_POST['DDPG_ACCOUNT_HOLDER'])) {
            $_SESSION['HP']['ACCOUNT.HOLDER'] = htmlspecialchars($_POST['DDPG_ACCOUNT_HOLDER']);
            
            if (empty($_POST['DDPG_ACCOUNT_HOLDER'])) {
                $this->pmAv       = false;
                $this->pmAv_error = HGW_ERROR_ACCOUNT_HOLDER;
                
                $content = [
                    [
                        'title' => '',
                        'field' => $this->pmAv_error,
                    ],
                ];
            }
        }
        
        if (isset($_POST['DDPG_ACCOUNT_IBAN'])) {
            $_SESSION['HP']['ACCOUNT.IBAN'] = str_replace(' ', '', htmlspecialchars($_POST['DDPG_ACCOUNT_IBAN']));
            
            if (preg_match('/[A-Z]{2}[0-9]{18,30}/', $_SESSION['HP']['ACCOUNT.IBAN']) == false) {
                $this->pmAv       = false;
                $this->pmAv_error = HGW_ERROR_IBAN;
                
                $content = [
                    [
                        'title' => '',
                        'field' => $this->pmAv_error,
                    ],
                ];
            }
        }
        
        // message if no testing account is set
        if ($getConf['transactionMode'] == '1' && isset($order)
            && strpos(strtolower(MODULE_PAYMENT_HPDDPG_TEST_ACCOUNT), strtolower($order->customer['email_address']))
               === false) {
            $this->pmAv       = false;
            $this->pmAv_error = HGW_DEBUGTEXT;
            
            $content = [
                [
                    'title' => ' ',
                    'field' => $this->pmAv_error,
                ],
            ];
        }
        
        // check if shipping and delivery adress match
        if (!$this->hgw->checkAddressMatch($order)) {
            $this->pmAv       = false;
            $this->pmAv_error = HGW_ADDRESSCHECK;
            
            $content = [
                [
                    'title' => '',
                    'field' => $this->pmAv_error,
                ],
            ];
        }
        
        // check if customer is company
        if ($this->hgw->checkIfCompany($order)) {
            $this->pmAv       = false;
            $this->pmAv_error = HGW_COMPANYCHECK;
            
            $content = [
                [
                    'title' => '',
                    'field' => $this->pmAv_error,
                ],
            ];
        }
        
        /*
         * form fields for supplimental data
         */
        
        $salutationfield = '';
        $salutationfield .= '<select name="DDPG.NAME.SALUTATION" class="form-control input-sm">';
        
        switch ($order->customer['gender']) {
            case 'f':
                $salutationfield .= '<option value="MRS" selected>' . MODULE_PAYMENT_HPDDPG_FEMALE . '</option>';
                $salutationfield .= '<option value="MR">' . MODULE_PAYMENT_HPDDPG_MALE . '</option>';
                break;
            case 'm':
                $salutationfield .= '<option value="MRS">' . MODULE_PAYMENT_HPDDPG_FEMALE . '</option>';
                $salutationfield .= '<option value="MR" selected>' . MODULE_PAYMENT_HPDDPG_MALE . '</option>';
                break;
            default:
                $salutationfield .= '<option value="MRS" selected>' . MODULE_PAYMENT_HPDDPG_FEMALE . '</option>';
                $salutationfield .= '<option value="MR">' . MODULE_PAYMENT_HPDDPG_MALE . '</option>';
                break;
        }
        
        $salutationfield .= '</select>';
        
        $content[] = [
            'title' => MODULE_PAYMENT_HPDDPG_SALUTATION,
            'field' => $salutationfield
        ];
        
        //birthdatefield with selects, defaults on empty
        $birthdatefield = '';
        $birthdatefield .= '<div class="row"><div class="col-sm-4"><select name="DDPG.NAME.BIRTHDAY" class="form-control input-sm"><option value="">'
                           . MODULE_PAYMENT_HPDDPG_BIRTHDAY . '</option>';
        for ($i = 1; $i <= 31; $i++) {
            $birthdatefield .= '<option value="' . sprintf('%02d', $i) . '">' . sprintf('%02d', $i) . '</option>';
        }
        $birthdatefield .= '</select></div><div class="col-sm-4"><select name="DDPG.NAME.BIRTHMONTH" class="form-control input-sm"><option value="">'
                           . MODULE_PAYMENT_HPDDPG_BIRTHMONTH . '</option>';
        for ($i = 1; $i <= 12; $i++) {
            $birthdatefield .= '<option value="' . sprintf('%02d', $i) . '">' . sprintf('%02d', $i) . '</option>';
        }
        $birthdatefield .= '</select></div><div class="col-sm-4"><select name="DDPG.NAME.BIRTHYEAR" class="form-control input-sm"><option value="">'
                           . MODULE_PAYMENT_HPDDPG_BIRTHYEAR . '</option>';
        for ($i = date('Y') - 18; $i >= 1900; $i--) {
            $birthdatefield .= '<option value="' . $i . '">' . $i . '</option>';
        }
        $birthdatefield .= '</select></div></div>';
        
        $content[] = [
            'title' => MODULE_PAYMENT_HPDDPG_BIRTHDATE,
            'field' => $birthdatefield
        ];
        
        $content[] = [
            'title' => HGW_TXT_ACC_HOLDER,
            'field' => '<input name="DDPG.ACCOUNT.HOLDER" class="form-control input-sm" placeholder="'
                       . HGW_TXT_ACC_HOLDER . '" value="' . $order->customer['firstname'] . ' '
                       . $order->customer['lastname'] . '">',
        ];
        
        $content[] = [
            'title' => HGW_TXT_ACC_IBAN,
            'field' => '<input name="DDPG.ACCOUNT.IBAN" class="form-control input-sm" placeholder="' . HGW_TXT_ACC_IBAN
                       . '" >',
        ];
        
        return [
            'id'          => $this->code,
            'module'      => $this->title_txt,
            'fields'      => $content,
            'description' => $this->info
        ];
    }
    
    
    function pre_confirmation_check()
    {
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
            'title' => MODULE_PAYMENT_HPDDPG_TEXT_ERROR,
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
        $sqlBase = 'INSERT INTO `gx_configurations` SET ';
        
        $inst   = [];
        $inst[] = [
            'key'   => $this->prefix . 'STATUS',
            'value' => 'True',
            'type'  => 'switcher',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'TEST_ACCOUNT',
            'value' => '',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'PROCESSED_STATUS_ID',
            'value' => '333',
            'type'  => 'order-status'
        ];
        $inst[] = [
            'key'   => $this->prefix . 'PENDING_STATUS_ID',
            'value' => '2',
            'type'  => 'order-status'
        ];
        $inst[] = [
            'key'   => $this->prefix . 'CANCELED_STATUS_ID',
            'value' => '330',
            'type'  => 'order-status'
        ];
        $inst[] = [
            'key'   => $this->prefix . 'SORT_ORDER',
            'value' => '1.30',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'ALLOWED',
            'value' => '',
        ];
        $inst[] = [
            'key'   => $this->prefix . 'ZONE',
            'value' => '',
            'type'  => 'geo-zone'
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
            'configuration/MODULE_PAYMENT_HPDDPG_STATUS',
            'configuration/MODULE_PAYMENT_HPDDPG_TEST_ACCOUNT',
            'configuration/MODULE_PAYMENT_HPDDPG_PROCESSED_STATUS_ID',
            'configuration/MODULE_PAYMENT_HPDDPG_PENDING_STATUS_ID',
            'configuration/MODULE_PAYMENT_HPDDPG_CANCELED_STATUS_ID',
            'configuration/MODULE_PAYMENT_HPDDPG_SORT_ORDER',
            'configuration/MODULE_PAYMENT_HPDDPG_ALLOWED',
            'configuration/MODULE_PAYMENT_HPDDPG_ZONE',
        ];
    }
}

MainFactory::load_origin_class('hpddpg');
