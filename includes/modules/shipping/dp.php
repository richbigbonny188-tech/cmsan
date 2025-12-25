<?php
/* --------------------------------------------------------------
   dp.php 2023-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(dp.php,v 1.36 2003/03/09 02:14:35); www.oscommerce.com
   (c) 2003	 nextcommerce (dp.php,v 1.12 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: dp.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   German Post (Deutsche Post WorldNet)         	Autor:	Copyright (C) 2002 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers | http://www.themedia.at & http://www.oscommerce.at

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class dp_ORIGIN
{
    var $code, $title, $description, $icon, $enabled, $num_dp;
    
    
    public function __construct()
    {
        $this->code        = 'dp';
        $this->title       = defined('MODULE_SHIPPING_DP_TEXT_TITLE') ? MODULE_SHIPPING_DP_TEXT_TITLE : '';
        $this->description = defined('MODULE_SHIPPING_DP_TEXT_DESCRIPTION') ? MODULE_SHIPPING_DP_TEXT_DESCRIPTION : '';
        $this->sort_order  = defined('MODULE_SHIPPING_DP_SORT_ORDER') ? MODULE_SHIPPING_DP_SORT_ORDER : '0';
        $this->icon        = '';
        $this->tax_class   = defined('MODULE_SHIPPING_DP_TAX_CLASS') ? MODULE_SHIPPING_DP_TAX_CLASS : '0';
        $this->enabled     = defined('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        
        if (isset($GLOBALS['order']) && ($this->enabled === true) && ((int)MODULE_SHIPPING_DP_ZONE > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . MODULE_SHIPPING_DP_ZONE . "' and zone_country_id = '"
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
        
        /**
         * CUSTOMIZE THIS SETTING FOR THE NUMBER OF ZONES NEEDED
         */
        $this->num_dp = 6;
    }
    
    
    /**
     * class methods
     */
    function quote($method = '')
    {
        global $order, $shipping_weight, $shipping_num_boxes;
        
        $dest_country = $order->delivery['country']['iso_code_2'];
        $dest_zone    = 0;
        $error        = false;
        
        $shipping_method = '';
        $shipping_cost   = 0.0;
        
        for ($i = 1; $i <= $this->num_dp; $i++) {
            $countries_table = constant('MODULE_SHIPPING_DP_COUNTRIES_' . $i);
            $country_zones   = explode(',', $countries_table);
            if (in_array($dest_country, $country_zones)) {
                $dest_zone = $i;
                break;
            }
        }
        
        if ($dest_zone == 0) {
            $error = true;
        } else {
            $shipping = -1;
            $dp_cost  = constant('MODULE_SHIPPING_DP_COST_' . $i);
            
            $dp_table = preg_split("/[:,]/", $dp_cost);
            for ($i = 0; $i < sizeof($dp_table); $i += 2) {
                if ($shipping_weight <= $dp_table[$i]) {
                    $shipping        = $dp_table[$i + 1];
                    $shipping_method = MODULE_SHIPPING_DP_TEXT_WAY . ' ' . $dest_country . ': ';
                    break;
                }
            }
            
            if ($shipping == -1) {
                $error           = true;
                $shipping_cost   = 0;
                $shipping_method = MODULE_SHIPPING_DP_UNDEFINED_RATE;
            } else {
                $shipping_cost = ($shipping + (double)MODULE_SHIPPING_DP_HANDLING);
            }
        }
        
        $this->quotes = [
            'id'      => $this->code,
            'module'  => MODULE_SHIPPING_DP_TEXT_TITLE,
            'methods' => [
                [
                    'id'    => $this->code,
                    'title' => $shipping_method . ' (' . $shipping_num_boxes . ' x ' . $shipping_weight . ' '
                               . MODULE_SHIPPING_DP_TEXT_UNITS . ')',
                    'cost'  => $shipping_cost * $shipping_num_boxes
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
        
        if ($error == true) {
            $this->quotes['error'] = MODULE_SHIPPING_DP_UNDEFINED_RATE;
        }
        
        return $this->quotes;
    }
    
    
    function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_SHIPPING_DP_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_STATUS', 'True', '0', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_HANDLING', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_TAX_CLASS', '0', '0', 'tax-class', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_ZONE', '0', '0', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_SORT_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_ALLOWED', '', '0', now())");
        
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COUNTRIES_1', 'AD,AT,BE,CZ,DK,FO,FI,FR,GR,GL,IE,IT,LI,LU,MC,NL,PL,PT,SM,SK,SE,CH,VA,GB,SP', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COST_1', '5:16.50,10:20.50,20:28.50', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COUNTRIES_2', 'AL,AM,AZ,BY,BA,BG,HR,CY,GE,GI,HU,IS,KZ,LT,MK,MT,MD,NO,SI,UA,TR,YU,RU,RO,LV,EE', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COST_2', '5:25.00,10:35.00,20:45.00', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COUNTRIES_3', 'DZ,BH,CA,EG,IR,IQ,IL,JO,KW,LB,LY,OM,SA,SY,US,AE,YE,MA,QA,TN,PM', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COST_3', '5:29.00,10:39.00,20:59.00', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COUNTRIES_4', 'AF,AS,AO,AI,AG,AR,AW,AU,BS,BD,BB,BZ,BJ,BM,BT,BO,BW,BR,IO,BN,BF,BI,KH,CM,CV,KY,CF,TD,CL,CN,CC,CO,KM,CG,CR,CI,CU,DM,DO,EC,SV,ER,ET,FK,FJ,GF,PF,GA,GM,GH,GD,GP,GT,GN,GW,GY,HT,HN,HK,IN,ID,JM,JP,KE,KI,KG,KP,KR,LA,LS', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COST_4', '5:35.00,10:50.00,20:80.00', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COUNTRIES_5', 'MO,MG,MW,MY,MV,ML,MQ,MR,MU,MX,MN,MS,MZ,MM,NA,NR,NP,AN,NC,NZ,NI,NE,NG,PK,PA,PG,PY,PE,PH,PN,RE,KN,LC,VC,SN,SC,SL,SO,LK,SR,SZ,ZA,SG,TG,TH,TZ,TT,TO,TM,TV,VN,WF,VE,UG,UZ,UY,ST,SH,SD,TW,GQ,LR,DJ,CG,RW,ZM,ZW', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COST_5', '5:35.00,10:50.00,20:80.00', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COUNTRIES_6', 'DE', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_DP_COST_6', '5:6.70,10:9.70,20:13.00', '0', now())");
    }
    
    
    function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    function keys()
    {
        $keys = [
            'configuration/MODULE_SHIPPING_DP_STATUS',
            'configuration/MODULE_SHIPPING_DP_HANDLING',
            'configuration/MODULE_SHIPPING_DP_ALLOWED',
            'configuration/MODULE_SHIPPING_DP_TAX_CLASS',
            'configuration/MODULE_SHIPPING_DP_ZONE',
            'configuration/MODULE_SHIPPING_DP_SORT_ORDER'
        ];
        
        for ($i = 1; $i <= $this->num_dp; $i++) {
            $keys[count($keys)] = 'configuration/MODULE_SHIPPING_DP_COUNTRIES_' . $i;
            $keys[count($keys)] = 'configuration/MODULE_SHIPPING_DP_COST_' . $i;
        }
        
        return $keys;
    }
}

MainFactory::load_origin_class('dp');
