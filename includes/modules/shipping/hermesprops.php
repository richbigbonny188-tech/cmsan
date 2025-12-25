<?php
/* --------------------------------------------------------------
   hermesprops.php 2022-05-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class hermesprops
{
    public $code;
    public $title;
    public $description;
    public $icon;
    public $enabled;
    public $sort_order;
    public $tax_class;
    
    
    public function __construct()
    {
        $order = $GLOBALS['order'] ?? null;
        
        $this->code        = 'hermesprops';
        $this->title       = defined('MODULE_SHIPPING_HERMESPROPS_TEXT_TITLE') ? MODULE_SHIPPING_HERMESPROPS_TEXT_TITLE : '';
        $this->description = defined('MODULE_SHIPPING_HERMESPROPS_TEXT_DESCRIPTION') ? MODULE_SHIPPING_HERMESPROPS_TEXT_DESCRIPTION : '';
        $this->sort_order  = defined('MODULE_SHIPPING_HERMESPROPS_SORT_ORDER') ? MODULE_SHIPPING_HERMESPROPS_SORT_ORDER : '0';
        $this->tax_class   = defined('MODULE_SHIPPING_HERMESPROPS_TAX_CLASS') ? MODULE_SHIPPING_HERMESPROPS_TAX_CLASS : '0';
        $this->enabled     = defined('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS')
                             && filter_var(constant('MODULE_SHIPPING_' . strtoupper($this->code) . '_STATUS'),
                FILTER_VALIDATE_BOOLEAN);
        $this->icon        = DIR_WS_ICONS . 'shipping/hermesprops.png';
        
        if (isset($order) && ($this->enabled === true) && ((int)MODULE_SHIPPING_HERMESPROPS_ZONE > 0)) {
            $check_flag = false;
            $db         = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $zoneQuery  = $db->select('zone_id')
                ->where('geo_zone_id', MODULE_SHIPPING_HERMESPROPS_ZONE)
                ->where('zone_country_id', $order->delivery['country']['id'])
                ->get('zones_to_geo_zones');
            foreach ($zoneQuery->result_array() as $zoneRow) {
                $allStatesInZone     = $zoneRow['zone_id'] <= 0;
                $deliveryStateInZone = (int)$zoneRow['zone_id'] === (int)$order->delivery['zone_id'];
                if ($allStatesInZone || $deliveryStateInZone) {
                    $check_flag = true;
                    break;
                }
            }
            
            if ($check_flag === false) {
                $this->enabled = false;
            }
        }
    }
    
    
    public function quote($method = '')
    {
        $order = $GLOBALS['order'];
        
        $packet_class = $this->determinePacketClass($order->products);
        
        $this->quotes = [
            'id'      => $this->code,
            'module'  => MODULE_SHIPPING_HERMESPROPS_TEXT_TITLE,
            'methods' => [
                [
                    'id'    => $this->code,
                    'title' => '',
                    'cost'  => (double)MODULE_SHIPPING_HERMESPROPS_HANDLING
                               + constant('MODULE_SHIPPING_HERMESPROPS_COST_' . $packet_class)
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
    
    
    public function determinePacketClass($products)
    {
        require_once DIR_FS_INC . '/xtc_get_prid.inc.php';
        $classes  = ['XS' => 0, 'S' => 1, 'M' => 2, 'L' => 3, 'XL' => 4, 'XXL' => 5];
        $fclasses = array_flip($classes);
        $minclass = 0;
        foreach ($products as $p) {
            $prid       = xtc_get_prid($p['id']);
            $classquery = xtc_db_query("SELECT min_pclass FROM products_hermesoptions WHERE products_id = " . $prid);
            if (xtc_db_num_rows($classquery) == 0) {
                $min_pclass = 'XS';
            } else {
                $classrow   = xtc_db_fetch_array($classquery);
                $min_pclass = $classrow['min_pclass'];
            }
            if ($classes[$min_pclass] > $minclass) {
                $minclass = $classes[$min_pclass];
            }
        }
        
        return $fclasses[$minclass];
    }
    
    
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query("SELECT `value` from `gx_configurations` where `key` = 'configuration/MODULE_SHIPPING_HERMESPROPS_STATUS'");
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    public function install()
    {
        $db       = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $defaults = [
            'last_modified'   => date('Y-m-d H:i:s'),
            'type'            => null,
        ];
        foreach ($this->getConfiguration() as $confKey => $confData) {
            $row        = array_merge($defaults, $confData);
            $row['key'] = $confKey;
            $db->insert('gx_configurations', $row);
        }
    }
    
    
    protected function getConfiguration()
    {
        $configuration = [
            'configuration/MODULE_SHIPPING_HERMESPROPS_STATUS'     => [
                'value' => 'True',
                'type'  => 'switcher',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_ALLOWED'    => [
                'value' => 'DE,BE,DK,EE,FI,FR,GB,IE,IT,LV,LT,LU,MC,NL,AT,PL,PT,SE,SK,SI,ES,CZ,HU',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_COST_XS'    => [
                'value' => '0.00',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_COST_S'     => [
                'value' => '0.00',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_COST_M'     => [
                'value' => '0.00',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_COST_L'     => [
                'value' => '0.00',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_COST_XL'    => [
                'value' => '0.00',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_COST_XXL'   => [
                'value' => '0.00',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_HANDLING'   => [
                'value' => '0.00',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_TAX_CLASS'  => [
                'value' => '0',
                'type'  => 'tax-class',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_ZONE'       => [
                'value' => '0',
                'type'  => 'geo-zone',
            ],
            'configuration/MODULE_SHIPPING_HERMESPROPS_SORT_ORDER' => [
                'value' => '0',
            ],
        ];
        
        return $configuration;
    }
    
    
    public function remove()
    {
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $db->where('`key` IN (\'' . implode("', '", $this->keys()) . '\')')->delete('gx_configurations');
    }
    
    
    public function keys()
    {
        return array_keys($this->getConfiguration());
    }
}

MainFactory::load_origin_class('hermesprops');
