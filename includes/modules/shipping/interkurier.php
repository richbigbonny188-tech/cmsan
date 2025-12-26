<?php
/*----------------------------------
Web-work24.de
Jahnstraße 18 
94249 Bodenmais
Telefon: 09924903768
email:info@web-work24.de
-------------------------------------*/

class interkurier_ORIGIN
{
    var $code, $title, $description, $icon, $enabled, $num_interkurier;
    
    
    public function __construct()
    {
        $this->code        = 'interkurier';
        $this->title       = defined('MODULE_SHIPPING_INTERKURIER_TEXT_TITLE') ? MODULE_SHIPPING_INTERKURIER_TEXT_TITLE : '';
        $this->description = defined('MODULE_SHIPPING_INTERKURIER_TEXT_DESCRIPTION') ? MODULE_SHIPPING_INTERKURIER_TEXT_DESCRIPTION : '';
        $this->sort_order  = defined('MODULE_SHIPPING_INTERKURIER_SORT_ORDER') ? MODULE_SHIPPING_INTERKURIER_SORT_ORDER : '0';
        $this->icon        = DIR_WS_ICONS . 'shipping/interkurier.png';
        $this->tax_class   = defined('MODULE_SHIPPING_INTERKURIER_TAX_CLASS') ? MODULE_SHIPPING_INTERKURIER_TAX_CLASS : '0';
        $this->enabled     = defined('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        
        if (isset($GLOBALS['order']) && ($this->enabled == true) && ((int)MODULE_SHIPPING_INTERKURIER_ZONE > 0)) {
            $check_flag  = false;
            $check_query = xtc_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '"
                                        . MODULE_SHIPPING_INTERKURIER_ZONE . "' and zone_country_id = '"
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
        
        //Zonen
        $this->num_interkurier = 6;
    }
    
    
    function quote($method = '')
    {
        global $order, $shipping_weight, $shipping_num_boxes;
        
        $dest_country = $order->delivery['country']['iso_code_2'];
        $dest_zone    = 0;
        $error        = false;
        
        $shipping_method = '';
        $shipping_cost   = 0.0;
        
        for ($i = 1; $i <= $this->num_interkurier; $i++) {
            $countries_table = constant('MODULE_SHIPPING_INTERKURIER_COUNTRIES_' . $i);
            $country_zones   = explode(',', $countries_table);
            if (in_array($dest_country, $country_zones)) {
                $dest_zone = $i;
                break;
            }
        }
        
        if ($dest_zone == 0) {
            $error = true;
        } else {
            $shipping         = -1;
            $interkurier_cost = constant('MODULE_SHIPPING_INTERKURIER_COST_' . $i);
            
            $interkurier_table = preg_split('/[:,]/', $interkurier_cost);
            for ($i = 0; $i < sizeof($interkurier_table); $i += 2) {
                if ($shipping_weight <= $interkurier_table[$i]) {
                    $shipping        = $interkurier_table[$i + 1];
                    $shipping_method = MODULE_SHIPPING_INTERKURIER_TEXT_WAY . ': ';
                    break;
                }
            }
            
            if ($shipping == -1) {
                $shipping_cost   = 0;
                $shipping_method = MODULE_SHIPPING_INTERKURIER_UNDEFINED_RATE;
            } else {
                $shipping_cost = ($shipping + MODULE_SHIPPING_INTERKURIER_HANDLING);
            }
        }
        
        $this->quotes = [
            'id'      => $this->code,
            'module'  => MODULE_SHIPPING_INTERKURIER_TEXT_TITLE,
            'methods' => [
                [
                    'id'    => $this->code,
                    'title' => $shipping_method, //. ' (' . MODULE_SHIPPING_INTERKURIER_TEXT_UNITS .')'
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
            $this->quotes['error'] = MODULE_SHIPPING_INTERKURIER_INVALID_ZONE;
        }
        
        return $this->quotes;
    }
    
    
    function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_SHIPPING_INTERKURIER_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_STATUS', 'True', '0', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_HANDLING', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_TAX_CLASS', '0', '0', 'tax-class', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_ZONE', '0', '0', 'geo-zone', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_SORT_ORDER', '0', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_ALLOWED', '', '0', now())");
        
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COUNTRIES_1', 'DE', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COST_1', '
0.00:15,0.5:16,1:17,2:18,3:19,4:20,5:21,6:22,7:23,8:24,9:25,10:26,11:27,12:28,13:29,14:30,15:31,16:32,17:33,18:34,19:35,20:36,21:37,22:38,23:39,24:40,25:41,26:42,27:43,28:44,29:45,30:46', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COUNTRIES_2', 'BE,DK,FR,LU,NL,AT,PL,CH,CZ', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COST_2',
'0.00:29.9,0.5:41.9,1:47.1,2:50.8,3:54.5,4:58.2,5:58.7,6:61.9,7:65.6,8:69.3,9:73,10:76.7,11:80.4,12:84.1,13:86.7,14:89.2,15:91.8,16:94.3,17:96.9,18:99.4,19:102,20:104.5,21:107.1,22:109.6,23:112.2,24:114.7,25:117.2,26:119.8,27:122.3,28:124.9,29:127.4,30:130', '0', now())");
        
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COUNTRIES_3', '
AL,AD,BA,BG,XC,EE,GI,GR,GL,GB,IE,IS,IT,XK,HR,LV,LI,LT,MT,MK,XL,MD,MC,ME,NO,PT,RO,SM,SE,RS,SK,SI,ES,UA,HU,VA,CY,FI', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COST_3', '0.00:24.6,0.5:52.4,1:60.5,2:68.3,3:76,4:83.8,5:91.5,6:99.3,7:107.1,8:114.8,9:122.6,10:130.3,11:138.1,12:142.4,13:146.7,14:150.9,15:155.2,16:159.5,17:163.8,18:168.1,19:172.4,20:176.6,21:180.9,22:185.2,23:189.5,24:193.8,25:198.1,26:202.4,27:206.6,28:210.9,29:215.2,30:219.5
', '0', now())");
        
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COUNTRIES_4', 'CA,US', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COST_4', '0.00:33.5,0.5:54.1,1:60.9,2:70.1,3:79.4,4:88.6,5:97.9,6:107.2,7:116.4,8:125.7,9:135,10:144.2,11:153.5,12:158.5,13:163.4,14:168.4,15:173.4,16:178.4,17:183.4,18:188.3,19:193.3,20:198.3,21:203.3,22:208.3,23:213.2,24:218.2,25:223.2,26:228.2,27:233.2,28:238.1,29:243.1,30:248.1', '0', now())");
        
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COUNTRIES_5', '
AZ,BH,CN,GE,IR,JP,KZ,QA,KG,KW,LB,LY,MO,OM,RU,SA,SG,TW,TH,TR,UZ,AE,BY,AM,HK,IN,IL,KR', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COST_5', '0.00:42,0.5:67,1:75.3,2:88.4,3:101.5,4:114.6,5:127.7,6:140.8,7:153.8,8:166.9,9:180,10:193.1,11:206.2,12:214.4,13:222.6,14:230.8,15:239.1,16:247.3,17:255.5,18:263.7,19:271.9,20:280.2,21:288.4,22:296.6,23:304.8,24:313,25:321.3,26:329.5,27:337.7,28:345.9,29:354.2,30:62.4
', '0', now())");
        
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COUNTRIES_6', '
EG,GQ,ET,AF,DZ,AS,VI,AO,AI,AQ,AG,AR,AW,AU,BS,BD,BB,BZ,BJ,BM,BT,BO,BW,BV,BR,VG,BN,BF,BI,CL,CK,CR,CI,DM,DO,DJ,EC,SV,ER,FO,FK,FJ,GF,PF,GA,GM,GH,GD,GP,GU,GT,GN,GW,GY,HT,HM,HN,ID,IQ,JM,YE,JO,KY,KH,CM,CV,KE,KI,CC,CO,KM,CD,CG,KP,CU,LA,LS,LR,MG,MW,MY,MV,ML,MA,MH', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_COST_6', '0.00:48.8,0.5:82.7,1:102.2,2:123,3:143.9,4:164.7,5:185.6,6:206.4,7:227.2,8:248.1,9:268.9,10:289.8,11:310.6,12:319.1,13:327.5,14:336,15:344.4,16:352.9,17:361.3,18:369.8,19:378.2,20:386.7,21:395.1,22:403.6,23:412,24:420.5,25:429,26:437.4,27:445.9,28:454.3,29:462.8,30:471.2
', '0', now())");
        
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_NOTIFICATION', '0', '0', 'switcher', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_FETCH_FIRM', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_FETCH_NAME', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_FETCH_PHONE', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_FETCH_EMAIL', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_FETCH_STREET', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_FETCH_POSTCODE', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_FETCH_CITY', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_FETCH_COUNTRY', 'Deutschland', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_FIRM', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_NAME', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_PHONE', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_EMAIL', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_STREET', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_POSTCODE', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_CITY', '', '0', now())");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `last_modified`) values ('configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_COUNTRY', 'Deutschland', '0', now())");
        //Insert status
        $lastNumber     = xtc_db_query("select * from `orders_status` order by `orders_status_id` desc");
        $last           = mysqli_fetch_object($lastNumber);
        $newNumber      = $last->orders_status_id + 1;
        $ifexists_query = xtc_db_query("select * from orders_status where orders_status_name like 'Expressversand freigegeben'");
        $ifexists       = xtc_db_num_rows($ifexists_query);
        if ($ifexists < 1) {
            xtc_db_query("insert into orders_status (orders_status_id, language_id,	orders_status_name) values ('"
                         . $newNumber . "','2','Expressversand freigegeben')");
        }
    }
    
    
    function remove()
    {
        xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
        //xtc_db_query("delete from orders_status where orders_status_name like 'Expressversand freigegeben'");
    }
    
    
    function keys()
    {
        $keys = [
            'configuration/MODULE_SHIPPING_INTERKURIER_STATUS',
            'configuration/MODULE_SHIPPING_INTERKURIER_HANDLING',
            'configuration/MODULE_SHIPPING_INTERKURIER_ALLOWED',
            'configuration/MODULE_SHIPPING_INTERKURIER_TAX_CLASS',
            'configuration/MODULE_SHIPPING_INTERKURIER_ZONE',
            'configuration/MODULE_SHIPPING_INTERKURIER_SORT_ORDER',
            'configuration/MODULE_SHIPPING_INTERKURIER_NOTIFICATION',
            'configuration/MODULE_SHIPPING_INTERKURIER_FETCH_FIRM',
            'configuration/MODULE_SHIPPING_INTERKURIER_FETCH_NAME',
            'configuration/MODULE_SHIPPING_INTERKURIER_FETCH_PHONE',
            'configuration/MODULE_SHIPPING_INTERKURIER_FETCH_EMAIL',
            'configuration/MODULE_SHIPPING_INTERKURIER_FETCH_STREET',
            'configuration/MODULE_SHIPPING_INTERKURIER_FETCH_POSTCODE',
            'configuration/MODULE_SHIPPING_INTERKURIER_FETCH_CITY',
            'configuration/MODULE_SHIPPING_INTERKURIER_FETCH_COUNTRY',
            'configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_FIRM',
            'configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_NAME',
            'configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_PHONE',
            'configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_EMAIL',
            'configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_STREET',
            'configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_POSTCODE',
            'configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_CITY',
            'configuration/MODULE_SHIPPING_INTERKURIER_INVOICE_COUNTRY'
        ];
        
        for ($i = 1; $i <= $this->num_interkurier; $i++) {
            $keys[count($keys)] = 'configuration/MODULE_SHIPPING_INTERKURIER_COUNTRIES_' . $i;
            $keys[count($keys)] = 'configuration/MODULE_SHIPPING_INTERKURIER_COST_' . $i;
        }
        
        return $keys;
    }
}

MainFactory::load_origin_class('interkurier');
