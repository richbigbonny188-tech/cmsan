<?php
/* --------------------------------------------------------------
   flat.php 2020-07-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(flat.php,v 1.40 2003/02/05); www.oscommerce.com 
   (c) 2003	 nextcommerce (flat.php,v 1.7 2003/08/24); www.nextcommerce.org
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: flat.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class flat_ORIGIN
{
    var $code, $title, $description, $icon, $enabled;
    
    
    public function __construct()
    {
        $this->code        = 'flat';
        $this->title       = defined('MODULE_SHIPPING_FLAT_TEXT_TITLE') ? MODULE_SHIPPING_FLAT_TEXT_TITLE : '';
        $this->description = defined('MODULE_SHIPPING_FLAT_TEXT_DESCRIPTION') ? MODULE_SHIPPING_FLAT_TEXT_DESCRIPTION : '';
        $this->sort_order  = defined('MODULE_SHIPPING_FLAT_SORT_ORDER') ? MODULE_SHIPPING_FLAT_SORT_ORDER : '0';
        $this->icon        = '';
        $this->tax_class   = defined('MODULE_SHIPPING_FLAT_TAX_CLASS') ? MODULE_SHIPPING_FLAT_TAX_CLASS : '0';
        $this->enabled     = defined('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        
        if (isset($GLOBALS['order']) && ($this->enabled == true) && ((int)MODULE_SHIPPING_FLAT_ZONE > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . MODULE_SHIPPING_FLAT_ZONE . "' and zone_country_id = '"
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
        global $order;
        $this->quotes = [
            'id'       => $this->code,
            'module'   => MODULE_SHIPPING_FLAT_TEXT_TITLE,
            'logo_url' => $this->getLogoUrl(),
            'logo_alt' => MODULE_SHIPPING_FLAT_TEXT_TITLE,
            'methods'  => [
                [
                    'id'    => $this->code,
                    'title' => MODULE_SHIPPING_FLAT_TEXT_WAY,
                    'cost'  => MODULE_SHIPPING_FLAT_COST
                ],
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
    
    
    public function getLogoUrl()
    {
        $logoUrl = xtc_href_link('images/icons/shipping/flat.png', '', 'SSL', false, false, false, true, true);
        
        return $logoUrl;
    }
    
    
    function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_SHIPPING_FLAT_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FLAT_STATUS', 'True', '0', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FLAT_ALLOWED', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FLAT_COST', '0.00', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FLAT_TAX_CLASS', '0', '0', 'tax-class', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FLAT_ZONE', '0', '0', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FLAT_SORT_ORDER', '0', '0', now())");
    }
    
    
    function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    function keys()
    {
        return [
            'configuration/MODULE_SHIPPING_FLAT_STATUS',
            'configuration/MODULE_SHIPPING_FLAT_COST',
            'configuration/MODULE_SHIPPING_FLAT_ALLOWED',
            'configuration/MODULE_SHIPPING_FLAT_TAX_CLASS',
            'configuration/MODULE_SHIPPING_FLAT_ZONE',
            'configuration/MODULE_SHIPPING_FLAT_SORT_ORDER'
        ];
    }
}

MainFactory::load_origin_class('flat');
