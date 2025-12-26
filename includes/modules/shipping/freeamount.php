<?php
/* --------------------------------------------------------------
   freeamount.php 2020-04-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(freeamount.php,v 1.01 2002/01/24); www.oscommerce.com 
   (c) 2003	 nextcommerce (freeamount.php,v 1.12 2003/08/24); www.nextcommerce.org
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: freeamount.php 1306 2005-10-14 10:32:31Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class freeamount_ORIGIN
{
    var $code, $title, $description, $icon, $enabled;
    
    
    public function __construct()
    {
        $this->code        = 'freeamount';
        $this->title       = defined('MODULE_SHIPPING_FREEAMOUNT_TEXT_TITLE') ? MODULE_SHIPPING_FREEAMOUNT_TEXT_TITLE : '';
        $this->description = defined('MODULE_SHIPPING_FREEAMOUNT_TEXT_DESCRIPTION') ? MODULE_SHIPPING_FREEAMOUNT_TEXT_DESCRIPTION : '';
        $this->icon        = '';   // change $this->icon =  DIR_WS_ICONS . 'shipping_ups.gif'; to some freeshipping icon
        $this->sort_order  = defined('MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER') ? MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER : '0';
        $this->tax_class   = defined('MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS') ? MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS : '0';
        $this->enabled     = defined('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
    }
    
    
    function quote($method = '')
    {
        global $xtPrice, $order;
        
        $t_freeamount = (double)MODULE_SHIPPING_FREEAMOUNT_AMOUNT;
        if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0
            && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 0
            && (int)MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS > 0) {
            $t_freeamount = $t_freeamount / (1 + $xtPrice->TAX[MODULE_ORDER_TOTAL_SHIPPING_TAX_CLASS] / 100);
        } elseif ($this->tax_class > 0 && $_SESSION['customers_status']['customers_status_show_price_tax'] == 0) {
            $tax_rate     = xtc_get_tax_rate($this->tax_class,
                                             $order->delivery['country']['id'],
                                             $order->delivery['zone_id']);
            $t_freeamount = $t_freeamount / (1 + $tax_rate / 100);
        }
        
        if ((round($xtPrice->xtcRemoveCurr($_SESSION['cart']->show_total()), 2) < round($t_freeamount, 2))
            && MODULE_SHIPPING_FREEAMOUNT_DISPLAY == 'False') {
            return;
        }
        
        $this->quotes = [
            'id'      => $this->code,
            'module'  => MODULE_SHIPPING_FREEAMOUNT_TEXT_TITLE,
            'methods' => [],
        ];
        
        if (round($xtPrice->xtcRemoveCurr($_SESSION['cart']->show_total()), 2) < round($t_freeamount, 2)) {
            $this->quotes['error'] = sprintf(MODULE_SHIPPING_FREEAMOUNT_TEXT_WAY,
                                             $xtPrice->xtcFormat($t_freeamount, true, 0, true));
        } else {
            $this->quotes['methods'] = [
                [
                    'id'    => $this->code,
                    'title' => sprintf(MODULE_SHIPPING_FREEAMOUNT_TEXT_WAY,
                                       $xtPrice->xtcFormat($t_freeamount, true, 0, true)),
                    'cost'  => 0
                ]
            ];
        }
        
        if (xtc_not_null($this->icon)) {
            $this->quotes['icon'] = xtc_image($this->icon, $this->title);
        }
        
        return $this->quotes;
    }
    
    
    function check()
    {
        $check = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_SHIPPING_FREEAMOUNT_STATUS'");
        $check = xtc_db_num_rows($check);
        
        return $check;
    }
    
    
    function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FREEAMOUNT_STATUS', 'True', '7', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FREEAMOUNT_ALLOWED', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FREEAMOUNT_DISPLAY', 'True', '7', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FREEAMOUNT_AMOUNT', '50.00', '8', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER', '0', '4', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS', '0', '0', 'tax-class', now())");
    }
    
    
    function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    function keys()
    {
        return [
            'configuration/MODULE_SHIPPING_FREEAMOUNT_STATUS',
            'configuration/MODULE_SHIPPING_FREEAMOUNT_ALLOWED',
            'configuration/MODULE_SHIPPING_FREEAMOUNT_DISPLAY',
            'configuration/MODULE_SHIPPING_FREEAMOUNT_AMOUNT',
            'configuration/MODULE_SHIPPING_FREEAMOUNT_SORT_ORDER',
            'configuration/MODULE_SHIPPING_FREEAMOUNT_TAX_CLASS'
        ];
    }
}

MainFactory::load_origin_class('freeamount');
