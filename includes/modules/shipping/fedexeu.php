<?php
/* --------------------------------------------------------------
   fedexeu.php 2023-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(fedexeu.php,v 1.01 2003/02/18); www.oscommerce.com 
   (c) 2003	 nextcommerce (fedexeu.php,v 1.12 2003/08/24); www.nextcommerce.org
   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: fedexeu.php 899 2005-04-29 02:40:57Z hhgag $)


   Released under the GNU General Public License 
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   fedex_europe_1.02         	Autor:	Copyright (C) 2002 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers | http://www.themedia.at & http://www.oscommerce.at

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class fedexeu_ORIGIN
{
    var $code, $title, $description, $icon, $enabled, $num_fedexeu, $types;
    
    
    public function __construct()
    {
        $this->code        = 'fedexeu';
        $this->title       = defined('MODULE_SHIPPING_FEDEXEU_TEXT_TITLE') ? MODULE_SHIPPING_FEDEXEU_TEXT_TITLE : '';
        $this->description = defined('MODULE_SHIPPING_FEDEXEU_TEXT_DESCRIPTION') ? MODULE_SHIPPING_FEDEXEU_TEXT_DESCRIPTION : '';
        $this->sort_order  = defined('MODULE_SHIPPING_FEDEXEU_SORT_ORDER') ? MODULE_SHIPPING_FEDEXEU_SORT_ORDER : '0';
        $this->icon        = DIR_WS_ICONS . 'shipping/fedexeu.png';
        $this->tax_class   = defined('MODULE_SHIPPING_FEDEXEU_TAX_CLASS') ? MODULE_SHIPPING_FEDEXEU_TAX_CLASS : '0';
        $this->enabled     = defined('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        
        if (isset($GLOBALS['order']) && ($this->enabled == true) && ((int)MODULE_SHIPPING_FEDEXEU_ZONE > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . MODULE_SHIPPING_FEDEXEU_ZONE . "' and zone_country_id = '"
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
        
        $this->types = [
            'ENV' => 'FedEx Envelope',
            'PAK' => 'FedEx Pak',
            'BOX' => 'FedEx Box'
        ];
        
        /**
         * CUSTOMIZE THIS SETTING FOR THE NUMBER OF ZONES NEEDED
         */
        $this->num_fedexeu = 8;
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
        
        for ($j = 1; $j <= $this->num_fedexeu; $j++) {
            $countries_table = constant('MODULE_SHIPPING_FEDEXEU_COUNTRIES_' . $j);
            $country_zones   = explode(',', $countries_table);
            if (in_array($dest_country, $country_zones)) {
                $dest_zone = $j;
                break;
            }
        }
        
        $methods = [];
        
        if ($dest_zone == 0) {
            $error = true;
        } else {
            $shipping         = -1;
            $fedexeu_cost_env = defined('MODULE_SHIPPING_FEDEXEU_COST_ENV_' . $j) ? constant('MODULE_SHIPPING_FEDEXEU_COST_ENV_' . $j) : '';
            $fedexeu_cost_pak = defined('MODULE_SHIPPING_FEDEXEU_COST_PAK_' . $j) ? constant('MODULE_SHIPPING_FEDEXEU_COST_PAK_' . $j) : '';
            $fedexeu_cost_box = defined('MODULE_SHIPPING_FEDEXEU_COST_BOX_' . $j) ? constant('MODULE_SHIPPING_FEDEXEU_COST_BOX_' . $j) : '';
            
            if ($fedexeu_cost_pak != '') {
                $fedexeu_table_pak = preg_split('/[:,]/', $fedexeu_cost_pak);
                
                for ($i = 0; $i < sizeof($fedexeu_table_pak); $i += 2) {
                    if ($shipping_weight <= $fedexeu_table_pak[$i]) {
                        $shipping_pak = $fedexeu_table_pak[$i + 1];
                        break;
                    }
                }
                
                if (($shipping_pak ?? null) == -1) {
                    $shipping_cost   = 0;
                    $shipping_method = MODULE_SHIPPING_FEDEXEU_UNDEFINED_RATE;
                } else {
                    $shipping_cost_1 = (($shipping_pak ?? null) + (double)MODULE_SHIPPING_FEDEXEU_HANDLING);
                }
                
                if (($shipping_pak ?? null) != 0) {
                    $methods[] = [
                        'id'    => 'PAK',
                        'title' => 'FedEx Pak',
                        'cost'  => ((double)MODULE_SHIPPING_FEDEXEU_HANDLING + $shipping_cost_1) * $shipping_num_boxes
                    ];
                }
            }
            
            if ($fedexeu_cost_env != '') {
                $fedexeu_table_env = preg_split('/[:,]/', $fedexeu_cost_env);
                
                $shipping_env = 0.0;
                for ($i = 0; $i < sizeof($fedexeu_table_env); $i += 2) {
                    if ($shipping_weight <= $fedexeu_table_env[$i]) {
                        $shipping_env = $fedexeu_table_env[$i + 1];
                        break;
                    }
                }
                
                $shipping_cost_1 = 0;
                if ($shipping_env == -1) {
                    $shipping_cost   = 0;
                    $shipping_method = MODULE_SHIPPING_FEDEXEU_UNDEFINED_RATE;
                } else {
                    $shipping_cost_1 = ($shipping_env + (double)MODULE_SHIPPING_FEDEXEU_HANDLING);
                }
                
                if ($shipping_env != 0) {
                    $methods[] = [
                        'id'    => 'ENV',
                        'title' => 'FedEx Envelope',
                        'cost'  => ((double)MODULE_SHIPPING_FEDEXEU_HANDLING + $shipping_cost_1) * $shipping_num_boxes
                    ];
                }
            }
            
            $shipping_box = 0;
            
            if ($fedexeu_cost_box != '') {
                $fedexeu_table_box = preg_split('/[:,]/', $fedexeu_cost_box);
                if (($shipping_weight > 10) and ($shipping_weight <= 20)) {
                    $shipping_box = number_format((($shipping_weight - 10) * 2 + 0.5), 0)
                                    * constant('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_' . $j)
                                    + $fedexeu_table_box[count($fedexeu_table_box) - 1];
                } elseif (($shipping_weight > 20) and ($shipping_weight <= 40)) {
                    $shipping_box = number_format((($shipping_weight - 20) * 2 + 0.5), 0)
                                    * constant('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_' . $j) + 20
                                                                                              * constant('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_'
                                                                                                         . $j)
                                    + $fedexeu_table_box[count($fedexeu_table_box) - 1];
                } elseif (($shipping_weight > 40) and ($shipping_weight <= 70)) {
                    $shipping_box = number_format((($shipping_weight - 40) * 2 + 0.5), 0)
                                    * constant('MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_' . $j) + 20
                                                                                              * constant('MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_'
                                                                                                         . $j) + 40
                                                                                                                 * constant('MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_'
                                                                                                                            . $j)
                                    + $fedexeu_table_box[count($fedexeu_table_box) - 1];
                } else {
                    
                    for ($i = 0; $i < sizeof($fedexeu_table_box); $i += 2) {
                        if ($shipping_weight <= $fedexeu_table_box[$i]) {
                            $shipping_box = $fedexeu_table_box[$i + 1];
                            break;
                        }
                    }
                }
                
                if ($shipping_box == -1) {
                    $shipping_cost   = 0;
                    $shipping_method = MODULE_SHIPPING_FEDEXEU_UNDEFINED_RATE;
                } else {
                    $shipping_cost_2 = ($shipping_box + (double)MODULE_SHIPPING_FEDEXEU_HANDLING);
                }
                
                if ($shipping_box != 0) {
                    $methods[] = [
                        'id'    => 'BOX',
                        'title' => 'FedEx Box',
                        'cost'  => ((double)MODULE_SHIPPING_FEDEXEU_HANDLING + $shipping_cost_2) * $shipping_num_boxes
                    ];
                }
            }
        }
        
        $this->quotes = [
            'id'     => $this->code,
            'module' => $this->title . ' (' . $shipping_num_boxes . ' x ' . $shipping_weight . ' '
                        . MODULE_SHIPPING_FEDEXEU_TEXT_UNITS . ')'
        ];
        
        $this->quotes['methods'] = $methods;
        
        if ($this->tax_class > 0) {
            $this->quotes['tax'] = xtc_get_tax_rate($this->tax_class,
                                                    $order->delivery['country']['id'],
                                                    $order->delivery['zone_id']);
        }
        
        if (xtc_not_null($this->icon)) {
            $this->quotes['icon'] = xtc_image($this->icon, $this->title);
        }
        
        if ($error == true) {
            $this->quotes['error'] = MODULE_SHIPPING_FEDEXEU_INVALID_ZONE;
        }
        
        if ((xtc_not_null($method)) && (isset($this->types[$method]))) {
            $methodsc   = [];
            for ($i = 0; $i < sizeof($methods); $i++) {
                if ($method == $methods[$i]['id']) {
                    $methodsc[] = [
                        'id'    => $methods[$i]['id'],
                        'title' => $methods[$i]['title'],
                        'cost'  => $methods[$i]['cost']
                    ];
                    break;
                }
            }
            $this->quotes['methods'] = $methodsc;
        }
        
        return $this->quotes;
    }
    
    
    function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_SHIPPING_FEDEXEU_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STATUS', 'True', '0', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_HANDLING', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_TAX_CLASS', '0', '0', 'tax-class', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_ZONE', '0', '0', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_SORT_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_ALLOWED', '', '0', now())");
        
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_1', 'AT,AD,BE,DK,DE,FI,FO,FR,GR,GL,GB,IE,IT,LU,MC,NL,PT,SE,SM,ES,VA', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_1', '0.5:41.40,1:48.20,1.5:51.30,2:54.40,2.5:57.50', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_1', '0.5:41.40,1:48.20,1.5:51.30,2:54.40,2.5:57.50,3:60.30,3.5:63.00,4:65.70,4.5:68.50,5:71.20,5.5:75.20,6:77.80,6.5:80.30,7:82.90,7.5:85.50,8:88.10,8.5:90.60,9:93.20,9.5:95.80,10:98.40', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_1', '1.70', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_1', '1.30', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_1', '1.10', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_2', 'GI,IS,LI,NO,CH', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_2', '0.5:51.90,1:58.20,1.5:64.40,2:70.70,2.5:77.00', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_2', '0.5:71.50,1:77.80,1.5:84.20,2:90.40,2.5:96.70,3:103.10,3.5:108.50,4:113.90,4.5:119.40,5:124.80,5.5:129.50,6:134.30,6.5:139.10,7:143.80,7.5:148.50,8:153.30,8.5:158.00,9:162.80,9.5:167.60,10:172.40', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_2', '1.50', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_2', '1.50', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_2', '1.60', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_3', 'AL,BA,BG,EE,HR,LV,LT,MK,MD,PL,RO,RU,SK,SI,CZ,TR,UA,HU,YU,BY', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_3', '0.5:51.10,1:57.60,1.5:64.20,2:70.70,2.5:77.30', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_3', '0.5:70.70,1:79.80,1.5:86.20,2:92.70,2.5:99.10,3:104.50,3.5:109.90,4:115.20,4.5:120.60,5:126.00,5.5:130.70,6:135.30,6.5:140.00,7:144.60,7.5:149.20,8:153.90,8.5:158.50,9:163.20,9.5:167.90,10:172.40', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_3', '2.10', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_3', '1.40', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_3', '1.70', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_4', 'CA,MX,PR,US', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_4', '0.5:50.30,1:58.30,1.5:66.10,2:74.10,2.5:81.90', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_4', '0.5:70.90,1:78.10,1.5:86.00,2:93.80,2.5:101.70,3:109.50,3.5:117.30,4:125.20,4.5:133.10,5:141.00,5.5:148.80,6:156.70,6.5:164.50,7:172.40,7.5:180.20,8:187.10,8.5:194.90,9:202.80,9.5:210.60,10:218.40', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_4', '4.10', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_4', '3.90', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_4', '3.80', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_5', 'AU,CN,HK,ID,JP,KR,MO,MY,NZ,PH,SG,TW,TH,VN', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_5', '0.5:55.80,1:74.60,1.5:93.20,2:111.90,2.5:130.40', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_5', '0.5:72.90,1:91.50,1.5:110.10,2:128.80,2.5:147.40,3:164.60,3.5:181.70,4:198.80,4.5:216.00,5:233.10,5.5:242.40,6:251.80,6.5:261.10,7:270.40,7.5:279.80,8:289.10,8.5:298.40,9:307.60,9.5:317.00,10:326.40', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_5', '4.30', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_5', '4.30', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_5', '3.70', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_6', 'BH,BD,BT,BN,KH,CY,EG,IN,IL,YE,JO,QA,KW,LA,LB,MT,MM,NP,OM,PK,SA,LK,SY,AE', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_6', '0.5:59.60,1:79.00,1.5:96.70,2:114.40,2.5:132.20', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_6', '0.5:81.20,1:100.50,1.5:118.30,2:136.10,2.5:153.90,3:171.70,3.5:189.50,4:207.30,4.5:225.10,5:242.70,5.5:251.70,6:260.70,6.5:269.70,7:278.50,7.5:287.40,8:296.30,8.5:305.10,9:314.10,9.5:322.90,10:331.90', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_6', '4.50', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_6', '4.30', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_6', '3.80', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_7', 'AI,AG,AR,AW,BS,BB,BZ,BM,BO,BR,KY,CL,CR,CO,DM,DO,EC,SV,GF,GD,GP,GT,GY,HT,HN,JM,VG,VI,MQ,MS,NI,AN,PA,PY,PE,KN,LC,VC,ZA,SR,TT,TC,UY,VE', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_7', '0.5:67.00,1:85.60,1.5:104.30,2:122.90,2.5:114.50', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_7', '0.5:84.20,1:102.80,1.5:121.40,2:140.00,2.5:158.70,3:175.70,3.5:192.90,4:210.10,4.5:227.20,5:244.30,5.5:254.40,6:264.50,6.5:274.50,7:284.60,7.5:294.60,8:304.60,8.5:314.70,9:324.80,9.5:334.90,10:344.90', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_7', '4.50', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_7', '4.30', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_7', '4.30', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_8', 'DZ,AS,AO,AM,AZ,BJ,BW,BF,BI,CM,CV,TD,CK,CG,DJ,GQ,ET,ER,FJ,FM,PF,GA,GM,GN,GW,GE,GH,GU,KZ,KE,KG,LS,LR,MG,MW,MV,ML,MA,MR,MU,MN,MZ,NA,NC,NE,NG,PW,PG,RE,RW,ZM,ZW,SN,SC,SL,SD,SZ,TZ,TG,TN,TM,UG,UZ,VU,WF', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_8', '0.5:68.50,1:86.90,1.5:105.50,2:124.00,2.5:142.40', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_8', '0.5:88.60,1:107.10,1.5:125.60,2:144.10,2.5:162.40,3:179.50,3.5:196.50,4:213.40,4.5:230.50,5:247.40,5.5:257.30,6:267.30,6.5:277.30,7:287.20,7.5:297.20,8:307.20,8.5:317.20,9:327.20,9.5:337.20,10:347.20', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_8', '5.50', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_8', '4.70', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_8', '4.70', '0', now())");
    }
    
    
    function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
    
    
    function keys()
    {
        $keys = [
            'configuration/MODULE_SHIPPING_FEDEXEU_STATUS',
            'configuration/MODULE_SHIPPING_FEDEXEU_HANDLING',
            'configuration/MODULE_SHIPPING_FEDEXEU_ALLOWED',
            'configuration/MODULE_SHIPPING_FEDEXEU_TAX_CLASS',
            'configuration/MODULE_SHIPPING_FEDEXEU_ZONE',
            'configuration/MODULE_SHIPPING_FEDEXEU_SORT_ORDER'
        ];
        
        for ($i = 1; $i <= $this->num_fedexeu; $i++) {
            $keys[] = 'configuration/MODULE_SHIPPING_FEDEXEU_COUNTRIES_' . $i;
            $keys[] = 'configuration/MODULE_SHIPPING_FEDEXEU_COST_PAK_' . $i;
            $keys[] = 'configuration/MODULE_SHIPPING_FEDEXEU_COST_ENV_' . $i;
            $keys[] = 'configuration/MODULE_SHIPPING_FEDEXEU_COST_BOX_' . $i;
            $keys[] = 'configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_20_' . $i;
            $keys[] = 'configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_40_' . $i;
            $keys[] = 'configuration/MODULE_SHIPPING_FEDEXEU_STEP_BOX_70_' . $i;
        }
        
        return $keys;
    }
}

MainFactory::load_origin_class('fedexeu');
