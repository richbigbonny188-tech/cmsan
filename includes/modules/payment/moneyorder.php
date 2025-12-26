<?php
/* --------------------------------------------------------------
   moneyorder.php 2022-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(moneyorder.php,v 1.10 2003/01/29); www.oscommerce.com 
   (c) 2003	 nextcommerce (moneyorder.php,v 1.7 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: moneyorder.php 998 2005-07-07 14:18:20Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class moneyorder_ORIGIN
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
    public $order_status;
    /**
     * @var string
     */
    public $email_footer;
    
    
    public function __construct()
    {
        $this->code        = 'moneyorder';
        $this->title       = defined('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE') ? MODULE_PAYMENT_MONEYORDER_TEXT_TITLE : '';
        $this->description = defined('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION')
                             && defined('MODULE_PAYMENT_MONEYORDER_PAYTO') ? sprintf(MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION,
                                                                                     MODULE_PAYMENT_MONEYORDER_PAYTO,
                                                                                     nl2br(STORE_NAME_ADDRESS)) : '';
        $this->sort_order  = defined('MODULE_PAYMENT_MONEYORDER_SORT_ORDER') ? MODULE_PAYMENT_MONEYORDER_SORT_ORDER : '0';
        $this->enabled     = defined('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_PAYMENT_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->info        = defined('MODULE_PAYMENT_MONEYORDER_TEXT_INFO') ? MODULE_PAYMENT_MONEYORDER_TEXT_INFO : '';
        if (defined('MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID')
            && (int)MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID;
        }
        
        if (key_exists('order', $GLOBALS) && $GLOBALS['order']) {
            $this->update_status();
        }
        
        $this->email_footer = '';
        if (defined('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER') && defined('MODULE_PAYMENT_MONEYORDER_PAYTO')) {
            $this->email_footer = sprintf(MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER,
                                          MODULE_PAYMENT_MONEYORDER_PAYTO,
                                          STORE_NAME_ADDRESS);
        }
    }
    
    
    public function update_status(): void
    {
        $order = $GLOBALS['order'];
        
        if (($this->enabled === true) && ((int)MODULE_PAYMENT_MONEYORDER_ZONE > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . MODULE_PAYMENT_MONEYORDER_ZONE . "' and zone_country_id = '"
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
            
            if ($check_flag === false) {
                $this->enabled = false;
            }
        }
    }
    
    
    public function javascript_validation(): bool
    {
        return false;
    }
    
    
    public function selection(): array
    {
        return ['id' => $this->code, 'module' => $this->title, 'description' => $this->info];
    }
    
    
    public function pre_confirmation_check(): bool
    {
        return false;
    }
    
    
    public function confirmation(): array
    {
        return [
            'title' => sprintf(MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION,
                               MODULE_PAYMENT_MONEYORDER_PAYTO,
                               nl2br(STORE_NAME_ADDRESS))
        ];
    }
    
    
    public function process_button(): bool
    {
        return false;
    }
    
    
    public function before_process(): bool
    {
        return false;
    }
    
    
    public function after_process(): void
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
    
    
    public function get_error(): bool
    {
        return false;
    }
    
    
    public function check(): bool
    {
        if (!isset ($this->_check)) {
            $check_query  = xtc_db_query("select `value` from `gx_configurations` where `key` = 'configuration/MODULE_PAYMENT_MONEYORDER_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    public function install()
    {
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_MONEYORDER_STATUS', 'False', '1', 'switcher', now());");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_MONEYORDER_ALLOWED', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `last_modified`, `type`) VALUES ('configuration/MODULE_PAYMENT_MONEYORDER_PAYTO', '', '1', now(), 'textarea');");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_MONEYORDER_SORT_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_MONEYORDER_ZONE', '0', '2', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` ( `key`, `value`, `sort_order`, `type`, `last_modified`) VALUES ('configuration/MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID', '0', '0', 'order-status', now())");
    }
    
    
    public function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    public function keys()
    {
        return [
            'configuration/MODULE_PAYMENT_MONEYORDER_STATUS',
            'configuration/MODULE_PAYMENT_MONEYORDER_ALLOWED',
            'configuration/MODULE_PAYMENT_MONEYORDER_ZONE',
            'configuration/MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID',
            'configuration/MODULE_PAYMENT_MONEYORDER_SORT_ORDER',
            'configuration/MODULE_PAYMENT_MONEYORDER_PAYTO'
        ];
    }
}

MainFactory::load_origin_class('moneyorder');
