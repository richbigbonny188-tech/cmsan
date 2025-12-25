<?php
/* --------------------------------------------------------------
  sepa.php 2023-04-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class sepa_ORIGIN
{
    public $code;
    public $title;
    public $description;
    public $enabled;
    public $gm_check_blz;
    /**
     * @var string
     */
    public $sort_order;
    /**
     * @var string
     */
    public $min_order;
    /**
     * @var string
     */
    public $info;
    public $order_status;
    
    public $sepa_owner;
    public $sepa_iban;
    public $sepa_bic;
    public $sepa_bankname;
    public $sepa_prz;
    public $sepa_status;
    public $sepa_fax;
    
    public function __construct()
    {
        global $order;
        
        $this->code        = 'sepa';
        $this->title       = defined('MODULE_PAYMENT_SEPA_TEXT_TITLE') ? MODULE_PAYMENT_SEPA_TEXT_TITLE : '';
        $this->description = defined('MODULE_PAYMENT_SEPA_TEXT_DESCRIPTION') ? MODULE_PAYMENT_SEPA_TEXT_DESCRIPTION : '';
        $this->sort_order  = defined('MODULE_PAYMENT_SEPA_SORT_ORDER') ? MODULE_PAYMENT_SEPA_SORT_ORDER : '0';
        $this->min_order   = defined('MODULE_PAYMENT_SEPA_MIN_ORDER') ? MODULE_PAYMENT_SEPA_MIN_ORDER : '0';
        $this->enabled     = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->info        = defined('MODULE_PAYMENT_SEPA_TEXT_INFO') ? MODULE_PAYMENT_SEPA_TEXT_INFO : '';
        if (defined('MODULE_PAYMENT_SEPA_ORDER_STATUS_ID') && (int)MODULE_PAYMENT_SEPA_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_SEPA_ORDER_STATUS_ID;
        }
        if (is_object($order)) {
            $this->update_status();
        }
    }
    
    
    public function update_status()
    {
        global $order;
        
        $check_order_query = xtc_db_query("select count(*) as count from " . TABLE_ORDERS . " where customers_id = '"
                                          . (int)$_SESSION['customer_id'] . "'");
        $order_check       = xtc_db_fetch_array($check_order_query);
        
        if ($order_check['count'] < MODULE_PAYMENT_SEPA_MIN_ORDER) {
            $check_flag    = false;
            $this->enabled = false;
        } else {
            $check_flag = true;
            
            if (($this->enabled == true) && ((int)MODULE_PAYMENT_SEPA_ZONE > 0)) {
                $check_flag  = false;
                $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                            . MODULE_PAYMENT_SEPA_ZONE . "' and zone_country_id = '"
                                            . (int)$order->billing['country']['id'] . "' order by zone_id");
                while ($check = xtc_db_fetch_array($check_query)) {
                    if ($check['zone_id'] < 1) {
                        $check_flag = true;
                        break;
                    } elseif ($check['zone_id'] == $order->billing['zone_id']) {
                        $check_flag = true;
                        break;
                    }
                }
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }
    
    
    public function javascript_validation()
    {
        $js = '';
        
        return $js;
    }
    
    
    public function selection()
    {
        global $order;
        
        $t_sepa_owner = ($order->billing['firstname'] ?? null) . ' ' . ($order->billing['lastname'] ?? null);
        if (trim($_SESSION['sepa_owner'] ?? '') != '') {
            $t_sepa_owner = $_SESSION['sepa_owner'];
        }
        
        $t_sepa_iban = '';
        if (trim($_SESSION['sepa_iban'] ?? '') != '') {
            $t_sepa_iban = $_SESSION['sepa_iban'];
        }
        
        $t_sepa_bic = '';
        if (trim($_SESSION['sepa_bic'] ?? '') != '') {
            $t_sepa_bic = $_SESSION['sepa_bic'];
        }
        
        $t_sepa_bankname = '';
        if (trim($_SESSION['sepa_bankname'] ?? '') != '') {
            $t_sepa_bankname = $_SESSION['sepa_bankname'];
        }
        
        $selection = [
            'id'          => $this->code,
            'module'      => $this->title,
            'description' => $this->info,
            'fields'      => [
                [
                    'title' => MODULE_PAYMENT_SEPA_TEXT_BANK_OWNER,
                    'field' => xtc_draw_input_field('sepa_owner',
                                                    htmlentities_wrapper($t_sepa_owner),
                                                    'style="width:200px"')
                ],
                [
                    'title' => MODULE_PAYMENT_SEPA_TEXT_BANK_IBAN,
                    'field' => xtc_draw_input_field('sepa_iban',
                                                    htmlentities_wrapper($t_sepa_iban),
                                                    'maxlength="32" style="width:200px" id="sepa_iban"')
                               . xtc_draw_hidden_field('recheckok', htmlentities_wrapper($_GET['recheckok'] ?? null))
                               . $this->getIbanCheckJavaScriptCode()
                ],
            ]
        ];
        
        $selection['fields'][] = [
            'title' => MODULE_PAYMENT_SEPA_TEXT_BANK_BIC,
            'field' => xtc_draw_input_field('sepa_bic',
                                            htmlentities_wrapper($t_sepa_bic),
                                            'maxlength="11" style="width:200px"')
        ];
        $selection['fields'][] = [
            'title' => MODULE_PAYMENT_SEPA_TEXT_BANK_NAME,
            'field' => xtc_draw_input_field('sepa_bankname',
                                            htmlentities_wrapper($t_sepa_bankname),
                                            'maxlength="32" style="width:200px"')
        ];
        
        if (MODULE_PAYMENT_SEPA_FAX_CONFIRMATION == 'true') {
            $selection['fields'][] = [
                'title' => MODULE_PAYMENT_SEPA_TEXT_NOTE,
                'field' => '<div>' . MODULE_PAYMENT_SEPA_TEXT_NOTE2 . '</div>'
            ];
            $selection['fields'][] = [
                'title' => MODULE_PAYMENT_SEPA_TEXT_BANK_FAX,
                'field' => xtc_draw_checkbox_field('sepa_fax', 'on')
            ];
        }
        
        $selection['logo_url'] = $this->getLogoUrl();
        
        return $selection;
    }
    
    
    public function getLogoUrl(): string
    {
        return xtc_href_link('images/icons/payment/sepa.png', '', 'SSL', false, false, false, true, true);
    }
    
    
    public function pre_confirmation_check()
    {
        if (($_POST['sepa_fax'] ?? null) == false) {
            $_SESSION['sepa_owner']    = $_POST['sepa_owner'];
            $_SESSION['sepa_bic']      = $_POST['sepa_bic'];
            $_SESSION['sepa_iban']     = $_POST['sepa_iban'];
            $_SESSION['sepa_bankname'] = $_POST['sepa_bankname'];
            
            $sepa_validation = MainFactory::create_object('SepaAccountCheck');
            $sepa_result     = $sepa_validation->CheckAccount($_POST['sepa_owner'],
                                                              $_POST['sepa_iban'],
                                                              $_POST['sepa_bic'],
                                                              $_POST['sepa_bankname']);
            
            switch ($sepa_result) {
                case 0: // payment o.k.
                    $error     = 'O.K.';
                    $recheckok = 'false';
                    break;
                case 1: // number & blz not ok (BLZValidation)
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_1;
                    $recheckok = 'false';
                    break;
                case 2: // account number has no calculation method (BLZValidation)
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_2;
                    $recheckok = 'true';
                    break;
                case 3: // No calculation method implemented (BLZValidation)
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_3;
                    $recheckok = 'true';
                    break;
                case 4: // Number cannot be checked (BLZValidation)
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_4;
                    $recheckok = 'true';
                    break;
                case 5: // BLZ not found (BLZValidation)
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_5;
                    $recheckok = 'false'; // Set "true" if you have not the latest BLZ table!
                    break;
                // CUSTOM ERRORS
                case 10: // no account holder
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_10;
                    $recheckok = 'false';
                    break;
                case 11: // no iban
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_11;
                    $recheckok = 'false';
                    break;
                case 12: // no iban check digits
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_12;
                    $recheckok = 'false';
                    break;
                case 13: // incorrect iban
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_13;
                    $recheckok = 'false';
                    break;
                case 14: // no bic
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_14;
                    $recheckok = 'false';
                    break;
                case 15: // incorrect bic
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_15;
                    $recheckok = 'false';
                    break;
                case 16: // no bankname
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_16;
                    $recheckok = 'false';
                    break;
                case 128: // Internal error
                    $error     = 'Internal error, please check again to process your payment';
                    $recheckok = 'true';
                    break;
                default:
                    $error     = MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR_4;
                    $recheckok = 'true';
                    break;
            }
            
            if ($sepa_result > 0 && $_POST['recheckok'] != 'true') {
                $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&recheckok='
                                        . $recheckok;
                xtc_redirect(xtc_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
            }
            
            $this->sepa_owner    = $sepa_validation->owner;
            $this->sepa_iban     = $sepa_validation->iban;
            $this->sepa_bic      = $sepa_validation->bic;
            $this->sepa_bankname = $sepa_validation->bankname;
            $this->sepa_prz      = $sepa_validation->prz;
            $this->sepa_status   = $sepa_result;
            
            $_SESSION['sepa_owner']    = $sepa_validation->owner;
            $_SESSION['sepa_bic']      = $sepa_validation->bic;
            $_SESSION['sepa_iban']     = $sepa_validation->iban;
            $_SESSION['sepa_bankname'] = $sepa_validation->bankname;
        }
    }
    
    
    public function confirmation()
    {
        if (!$_POST['sepa_owner'] == '') {
            $confirmation = [
                'title'  => $this->title,
                'fields' => [
                    [
                        'title' => '<span style="display:inline-block;margin-left:13px;line-height:18px;">'
                                   . MODULE_PAYMENT_SEPA_TEXT_BANK_OWNER . '<br />' . MODULE_PAYMENT_SEPA_TEXT_BANK_IBAN
                                   . '<br />' . MODULE_PAYMENT_SEPA_TEXT_BANK_BIC . '<br />'
                                   . MODULE_PAYMENT_SEPA_TEXT_BANK_NAME . '</span>',
                        'field' => '<span style="display:inline-block;margin-left:13px;line-height:18px;">'
                                   . $this->sepa_owner . '<br />' . $this->sepa_iban . '<br />' . $this->sepa_bic
                                   . '<br />' . $this->sepa_bankname . '</span>'
                    ]
                ]
            ];
        }
        
        if (($_POST['sepa_fax'] ?? null) == "on") {
            $confirmation   = ['fields' => [['title' => MODULE_PAYMENT_SEPA_TEXT_BANK_FAX]]];
            $this->sepa_fax = "on";
        }
        
        return $confirmation;
    }
    
    
    public function process_button()
    {
        global $_POST;
        
        $process_button_string = xtc_draw_hidden_field('sepa_bic', $this->sepa_bic ?? null)
                                 . xtc_draw_hidden_field('sepa_bankname', $this->sepa_bankname ?? null)
                                 . xtc_draw_hidden_field('sepa_iban', $this->sepa_iban ?? null)
                                 . xtc_draw_hidden_field('sepa_owner', $this->sepa_owner ?? null)
                                 . xtc_draw_hidden_field('sepa_status', $this->sepa_status ?? null)
                                 . xtc_draw_hidden_field('sepa_prz', $this->sepa_prz ?? null)
                                 . xtc_draw_hidden_field('sepa_fax', $this->sepa_fax ?? null);
        
        return $process_button_string;
    }
    
    
    public function before_process()
    {
        return false;
    }
    
    
    public function after_process()
    {
        global $insert_id, $_POST;
        
        xtc_db_query("
	      	INSERT INTO sepa (
	      		orders_id, 
	      		sepa_bic, 
	      		sepa_bankname, 
	      		sepa_iban, 
	      		sepa_owner, 
	      		sepa_status, 
	      		sepa_prz
	      	)
	      	VALUES (
	      		'" . $insert_id . "', 
	      		'" . xtc_db_input($_POST['sepa_bic']) . "', 
	      		'" . xtc_db_input($_POST['sepa_bankname']) . "', 
	      		'" . xtc_db_input($_POST['sepa_iban']) . "', 
	      		'" . xtc_db_input($_POST['sepa_owner']) . "', 
	      		'" . xtc_db_input($_POST['sepa_status']) . "', 
	      		'" . xtc_db_input($_POST['sepa_prz']) . "'
      		)");
        
        if ($_POST['sepa_fax']) {
            xtc_db_query("update sepa set sepa_fax = '" . xtc_db_input($_POST['sepa_fax']) . "' where orders_id = '"
                         . $insert_id . "'");
        }
        
        if ($this->order_status) {
            $insertId = new IdType((int)$GLOBALS['insert_id']);
            /** @var OrderWriteServiceInterface $orderWriteService */
            $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
            $orderWriteService->updateOrderStatus($insertId,
                                                  new IntType((int)$this->order_status),
                                                  new StringType(''),
                                                  new BoolType(false));
        }
        
        unset($_SESSION['sepa_owner']);
        unset($_SESSION['sepa_bic']);
        unset($_SESSION['sepa_iban']);
        unset($_SESSION['sepa_bankname']);
    }
    
    
    public function get_error()
    {
        
        $error = [
            'title' => MODULE_PAYMENT_SEPA_TEXT_BANK_ERROR,
            'error' => stripslashes(urldecode($_GET['error']))
        ];
        
        return $error;
    }
    
    
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_PAYMENT_SEPA_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    public function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_STATUS', 'True', '1', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_ZONE', '0', '2', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_ALLOWED', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_SORT_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_ORDER_STATUS_ID', '0', '0', 'order-status', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_FAX_CONFIRMATION', 'false', '2', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_DATABASE_BLZ', 'true', '0', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_CREDITOR_ID', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_SEND_MANDATE', 'false', '0', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY', 'false', '0', 'switcher', now())");
        xtc_db_query("CREATE TABLE IF NOT EXISTS `sepa` (`orders_id` int(11) NOT NULL, `sepa_owner` varchar(64), `sepa_iban` varchar(35), `sepa_bic` varchar(15), `sepa_bankname` varchar(255), `sepa_status` int(11), `sepa_prz` char(2), `sepa_fax` char(2),PRIMARY KEY (`orders_id`)) ENGINE=InnoDB DEFAULT CHARSET=`utf8`");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_SEPA_MIN_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`, `type`) VALUES('configuration/MODULE_PAYMENT_SEPA_DATACHECK', 'true', 3, '2011-05-19 08:19:02', 'switcher')");
    }
    
    
    public function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    public function keys()
    {
        $t_return   = [];
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_STATUS';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_CREDITOR_ID';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_SEND_MANDATE';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_COMMUNICATE_SEPARATELY';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_ALLOWED';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_ZONE';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_ORDER_STATUS_ID';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_SORT_ORDER';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_DATACHECK';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_DATABASE_BLZ';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_FAX_CONFIRMATION';
        $t_return[] = 'configuration/MODULE_PAYMENT_SEPA_MIN_ORDER';
        
        return $t_return;
    }
    
    
    /**
     * @return string
     */
    protected function getIbanCheckJavaScriptCode(): string
    {
        $js = '';
        if (MODULE_PAYMENT_SEPA_DATACHECK === 'true' && empty($_GET['payment_error'])) {
            $js = <<<'JS'
<script type="text/javascript">
const sepaIban = document.getElementById('sepa_iban');

sepaIban.addEventListener('change', (event) => {
	// hide BIC and bank name for german IBAN, because it will be ignored and overwritten
	if (event.target.value.trim().substring(0, 2) === 'DE') {
		document.querySelector('.sepa .checkout-payment-form .form-group:nth-child(3)').style.display = 'none';
		document.querySelector('.sepa .checkout-payment-form .form-group:nth-child(4)').style.display = 'none';
	} else {
		document.querySelector('.sepa .checkout-payment-form .form-group:nth-child(3)').style.display = 'block';
		document.querySelector('.sepa .checkout-payment-form .form-group:nth-child(4)').style.display = 'block';
	}
});
</script>
JS;
        }
        
        return $js;
    }
}

MainFactory::load_origin_class('sepa');
