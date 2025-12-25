<?php
/* --------------------------------------------------------------
   invoice.php 2020-02-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(cod.php,v 1.28 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (invoice.php,v 1.6 2003/08/24); www.nextcommerce.org
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: invoice.php 1122 2005-07-26 10:16:27Z mz $   )

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class invoice_ORIGIN
{
    public $code, $title, $description, $enabled;
    /**
     * @var string
     */
    public $min_order;
    /**
     * @var string
     */
    public $sort_order;
    /**
     * @var string
     */
    public $info;
    public $order_status;
    
    
    public function __construct()
    {
        global $order;
        
        $this->code        = 'invoice';
        $this->title       = defined('MODULE_PAYMENT_INVOICE_TEXT_TITLE') ? MODULE_PAYMENT_INVOICE_TEXT_TITLE : '';
        $this->description = defined('MODULE_PAYMENT_INVOICE_TEXT_DESCRIPTION') ? MODULE_PAYMENT_INVOICE_TEXT_DESCRIPTION : '';
        $this->min_order   = defined('MODULE_PAYMENT_INVOICE_MIN_ORDER') ? MODULE_PAYMENT_INVOICE_MIN_ORDER : '0';
        $this->sort_order  = defined('MODULE_PAYMENT_INVOICE_SORT_ORDER') ? MODULE_PAYMENT_INVOICE_SORT_ORDER : '0';
        $this->enabled     = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->info        = defined('MODULE_PAYMENT_INVOICE_TEXT_INFO') ? MODULE_PAYMENT_INVOICE_TEXT_INFO : '';
        if (defined('MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID') && (int)MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID;
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
        
        if ($order_check['count'] < MODULE_PAYMENT_INVOICE_MIN_ORDER) {
            $check_flag    = false;
            $this->enabled = false;
        } else {
            $check_flag = true;
            
            if (($this->enabled == true) && ((int)MODULE_PAYMENT_INVOICE_ZONE > 0)) {
                $check_flag  = false;
                $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                            . MODULE_PAYMENT_INVOICE_ZONE . "' and zone_country_id = '"
                                            . $order->delivery['country']['id'] . "' order by zone_id");
                
                while ($check = xtc_db_fetch_array($check_query)) {
                    if ($check['zone_id'] < 1) {
                        $check_flag = true;
                        break;
                    } elseif ($check['zone_id'] == $order->delivery['zone_id']) {
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
        return false;
    }
    
    
    public function confirmation()
    {
        return false;
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
            $check_query  = xtc_db_query("select `value` from `gx_configurations` where `key` = 'configuration/MODULE_PAYMENT_INVOICE_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    public function install()
    {
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_INVOICE_STATUS', 'True', '1', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_INVOICE_ALLOWED', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_INVOICE_ZONE', '0', '2', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_INVOICE_SORT_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_PAYMENT_INVOICE_MIN_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID', '0', '0', 'order-status', now())");
    }
    
    
    public function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    public function keys()
    {
        return [
            'configuration/MODULE_PAYMENT_INVOICE_STATUS',
            'configuration/MODULE_PAYMENT_INVOICE_ALLOWED',
            'configuration/MODULE_PAYMENT_INVOICE_ZONE',
            'configuration/MODULE_PAYMENT_INVOICE_ORDER_STATUS_ID',
            'configuration/MODULE_PAYMENT_INVOICE_MIN_ORDER',
            'configuration/MODULE_PAYMENT_INVOICE_SORT_ORDER'
        ];
    }
}

MainFactory::load_origin_class('invoice');
