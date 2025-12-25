<?php
/* --------------------------------------------------------------
   item.php 2022-04-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(table.php,v 1.27 2003/02/05); www.oscommerce.com
   (c) 2003	 nextcommerce (table.php,v 1.8 2003/08/24); www.nextcommerce.org
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: item.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class item_ORIGIN
{
    var $code, $title, $description, $icon, $enabled;
    
    
    public function __construct()
    {
        $this->code        = 'item';
        $this->title       = defined('MODULE_SHIPPING_ITEM_TEXT_TITLE') ? MODULE_SHIPPING_ITEM_TEXT_TITLE : '';
        $this->description = defined('MODULE_SHIPPING_ITEM_TEXT_DESCRIPTION') ? MODULE_SHIPPING_ITEM_TEXT_DESCRIPTION : '';
        $this->sort_order  = defined('MODULE_SHIPPING_ITEM_SORT_ORDER') ? MODULE_SHIPPING_ITEM_SORT_ORDER : '0';
        $this->icon        = '';
        $this->tax_class   = defined('MODULE_SHIPPING_ITEM_TAX_CLASS') ? MODULE_SHIPPING_ITEM_TAX_CLASS : '0';
        $this->enabled     = defined('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        
        if (isset($GLOBALS['order']) && ($this->enabled == true) && ((int)MODULE_SHIPPING_ITEM_ZONE > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . MODULE_SHIPPING_ITEM_ZONE . "' and zone_country_id = '"
                                        . $GLOBALS['order']->delivery['country']['id'] . "' order by zone_id");
            while ($check = xtc_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $GLOBALS['order']->delivery['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }
            
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }
    
    
    function quote($method = '')
    {
        global $order, $total_count;
        
        $this->quotes = [
            'id'      => $this->code,
            'module'  => MODULE_SHIPPING_ITEM_TEXT_TITLE,
            'methods' => [
                [
                    'id'    => $this->code,
                    'title' => MODULE_SHIPPING_ITEM_TEXT_WAY,
                    'cost'  => ((double)MODULE_SHIPPING_ITEM_COST * $total_count) + (double)MODULE_SHIPPING_ITEM_HANDLING
                ]
            ]
        ];
        
        if ($this->tax_class > 0) {
            $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class,
                                                    $order->delivery['country']['id'],
                                                    $order->delivery['zone_id']);
        }
        
        if (xtc_not_null($this->icon)) {
            $this->quotes['icon'] = xtc_image($this->icon, $this->title);
        }
        
        return $this->quotes;
    }
    
    
    function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_SHIPPING_ITEM_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_ITEM_STATUS', 'True', '0', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_ITEM_ALLOWED', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_ITEM_COST', '0.00', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_ITEM_HANDLING', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_ITEM_TAX_CLASS', '0', '0', 'tax-class', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_ITEM_ZONE', '0', '0', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_ITEM_SORT_ORDER', '0', '0', now())");
    }
    
    
    function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    function keys()
    {
        return [
            'configuration/MODULE_SHIPPING_ITEM_STATUS',
            'configuration/MODULE_SHIPPING_ITEM_COST',
            'configuration/MODULE_SHIPPING_ITEM_HANDLING',
            'configuration/MODULE_SHIPPING_ITEM_ALLOWED',
            'configuration/MODULE_SHIPPING_ITEM_TAX_CLASS',
            'configuration/MODULE_SHIPPING_ITEM_ZONE',
            'configuration/MODULE_SHIPPING_ITEM_SORT_ORDER'
        ];
    }
}

MainFactory::load_origin_class('item');
