<?php
/* --------------------------------------------------------------
   cash.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/* -----------------------------------------------------------------------------------------
   $Id: cash.php 1102 2005-07-24 15:05:38Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(moneyorder.php,v 1.10 2003/01/29); www.oscommerce.com
   (c) 2003	 nextcommerce (moneyorder.php,v 1.7 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class cash_ORIGIN
{
    public $code, $title, $description, $enabled;
    /**
     * @var string
     */
    public $sort_order;
    /**
     * @var string
     */
    public $info;
    /**
     * @var string
     */
    public $email_footer;
    public $order_status;
    
    
    public function __construct()
    {
        global $order;
        
        $this->code        = 'cash';
        $this->title       = defined('MODULE_PAYMENT_CASH_TEXT_TITLE') ? MODULE_PAYMENT_CASH_TEXT_TITLE : '';
        $this->description = defined('MODULE_PAYMENT_CASH_TEXT_DESCRIPTION') ? MODULE_PAYMENT_CASH_TEXT_DESCRIPTION : '';
        $this->sort_order  = defined('MODULE_PAYMENT_CASH_SORT_ORDER') ? MODULE_PAYMENT_CASH_SORT_ORDER : '0';
        $this->enabled     = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->info        = defined('MODULE_PAYMENT_CASH_TEXT_INFO') ? MODULE_PAYMENT_CASH_TEXT_INFO : '';
        if (defined('MODULE_PAYMENT_CASH_ORDER_STATUS_ID') && (int)MODULE_PAYMENT_CASH_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_CASH_ORDER_STATUS_ID;
        }
        
        if (is_object($order)) {
            $this->update_status();
        }
        
        $this->email_footer = defined('MODULE_PAYMENT_CASH_TEXT_EMAIL_FOOTER') ? MODULE_PAYMENT_CASH_TEXT_EMAIL_FOOTER : '';
    }
    
    
    public function update_status()
    {
        global $order;
        
        if (array_key_exists('shipping', $_SESSION) && is_array($_SESSION['shipping'])
            && array_key_exists('id',
                                $_SESSION['shipping'])
            && $_SESSION['shipping']['id'] !== 'selfpickup_selfpickup') {
                $this->enabled = false;
            }
        
        if (($this->enabled == true) && ((int)MODULE_PAYMENT_CASH_ZONE > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . MODULE_PAYMENT_CASH_ZONE . "' and zone_country_id = '"
                                        . $order->billing['country']['id'] . "' order by zone_id");
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
        return ['id' => $this->code, 'module' => $this->title, 'description' => $this->info];
    }
    
    
    public function pre_confirmation_check()
    {
        return false;
    }
    
    
    public function confirmation()
    {
        return ['title' => MODULE_PAYMENT_CASH_TEXT_DESCRIPTION];
    }
    
    
    public function process_button()
    {
        return false;
    }
    
    
    public function before_process()
    {
        return false;
    }
    
    
    public function after_process()
    {
        if ($this->order_status) {
            $insertId = new IdType((int)$GLOBALS['insert_id']);
            /** @var OrderWriteServiceInterface $orderWriteService */
            $orderWriteService = StaticGXCoreLoader::getService('OrderWrite');
            $orderWriteService->updateOrderStatus($insertId,
                                                  new IntType((int)$this->order_status),
                                                  new StringType(''),
                                                  new BoolType(false));
        }
    }
    
    
    public function get_error()
    {
        return false;
    }
    
    
    public function check()
    {
        if (!isset ($this->_check)) {
            $check_query  = xtc_db_query("select `value` from `gx_configurations` where`key` = 'configuration/MODULE_PAYMENT_CASH_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    public function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_CASH_STATUS', 'True', '1', 'switcher', now());");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_CASH_ALLOWED', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_CASH_SORT_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_CASH_ZONE', '0', '2', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_CASH_ORDER_STATUS_ID', '0', '0', 'order-status', now())");
    }
    
    
    public function remove()
    {
        xtc_db_query("delete from `gx_configurations` where`key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    public function keys()
    {
        return [
            'configuration/MODULE_PAYMENT_CASH_STATUS',
            'configuration/MODULE_PAYMENT_CASH_ALLOWED',
            'configuration/MODULE_PAYMENT_CASH_ZONE',
            'configuration/MODULE_PAYMENT_CASH_ORDER_STATUS_ID',
            'configuration/MODULE_PAYMENT_CASH_SORT_ORDER'
        ];
    }
}

MainFactory::load_origin_class('cash');
