<?php
/* --------------------------------------------------------------
   ot_gambioultra.php 2023-04-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_loworderfee.php,v 1.11 2003/02/14); www.oscommerce.com 
   (c) 2003	 nextcommerce (ot_loworderfee.php,v 1.7 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class ot_gambioultra_ORIGIN
{
    public $code;
    public $title;
    public $output;
    public $description;
    public $enabled;
    public $sort_order;
    
    
    public function __construct()
    {
        global $xtPrice;
        $this->code        = 'ot_gambioultra';
        $this->title       = defined(
            'MODULE_ORDER_TOTAL_GAMBIOULTRA_TITLE'
        ) ? MODULE_ORDER_TOTAL_GAMBIOULTRA_TITLE : '';
        $this->description = defined(
            'MODULE_ORDER_TOTAL_GAMBIOULTRA_DESCRIPTION'
        ) ? MODULE_ORDER_TOTAL_GAMBIOULTRA_DESCRIPTION : '';
        $this->enabled     = defined('MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS')
                             && MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS === 'true';
        $this->sort_order  = defined(
            'MODULE_ORDER_TOTAL_GAMBIOULTRA_SORT_ORDER'
        ) ? MODULE_ORDER_TOTAL_GAMBIOULTRA_SORT_ORDER : '0';
        
        $this->output = [];
    }
    
    
    function nc_get_product_shipping_costs()
    {
        $xtPrice  = $GLOBALS['xtPrice'];
        $products = $_SESSION['cart']->get_products();
        $costs    = 0;
        $infos    = [];
        
        for ($i = 0, $iMax = count($products); $i < $iMax; $i++) {
            $result = mysqli_query(
                $GLOBALS["___mysqli_ston"],
                '
					SELECT 
						p.nc_ultra_shipping_costs AS costs,
						pd.products_name					AS products_name
					FROM 	
						products p
					LEFT JOIN 
						products_description AS pd USING (products_id)
					WHERE
						p.nc_ultra_shipping_costs	 != 0																	AND
						p.products_id 							= "' . $products[$i]['id'] . '"	AND
						pd.language_id 							= "' . $_SESSION['languages_id'] . '"
				'
            );
            
            if (((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno(
                    $GLOBALS["___mysqli_ston"]
                ) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false)) == 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $costs += $xtPrice->xtcFormat($row['costs'] * $products[$i]['quantity'], false, 0, true);
                    
                    $infos[] = [
                        'title'       => gm_prepare_number(
                                             $products[$i]['quantity'],
                                             $xtPrice->currencies[$xtPrice->actualCurr]['decimal_point']
                                         ) . 'x ' . $row['products_name'],
                        'price'       => $xtPrice->xtcFormat(
                            $row['costs'] * $products[$i]['quantity'],
                            true,
                            MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS,
                            true
                        ),
                        'price_plain' => $xtPrice->xtcFormat($row['costs'] * $products[$i]['quantity'], false, 0, true),
                    ];
                }
            }
        }
        $output = [
            'costs' => $costs,
            'infos' => $infos,
        ];
        
        return $output;
    }
    
    
    public function process()
    {
        $order   = $GLOBALS['order'];
        $xtPrice = $GLOBALS['xtPrice'];
        
        if ($order->info['shipping_class'] == 'selfpickup_selfpickup') {
            $this->output = [];
            
            return;
        }
        
        //include needed functions
        require_once DIR_FS_INC . 'xtc_calculate_tax.inc.php';
        
        if (MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS === 'true'
            && $order->info['shipping_class'] !== 'selfpickup_selfpickup') {
            switch (MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION) {
                case 'national':
                    if ((int)$order->delivery['country_id'] === (int)STORE_COUNTRY) {
                        $pass = true;
                    }
                    break;
                case 'international':
                    if ((int)$order->delivery['country_id'] !== (int)STORE_COUNTRY) {
                        $pass = true;
                    }
                    break;
                case 'both':
                    $pass = true;
                    break;
                default:
                    $pass = false;
                    break;
            }
            
            if ($pass === true) {
                $coo_text_mgr = MainFactory::create_object(
                    'LanguageTextManager',
                    ['gambioultra', $_SESSION['languages_id']],
                    false
                );
                
                $nc_ultra_data = $this->nc_get_product_shipping_costs();
                
                $tax             = xtc_get_tax_rate(
                    MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS,
                    $order->delivery['country']['id'],
                    $order->delivery['zone_id']
                );
                $tax_description = xtc_get_tax_description(
                    MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS,
                    $order->delivery['country']['id'],
                    $order->delivery['zone_id']
                );
                
                if (MODULE_ORDER_TOTAL_GAMBIOULTRA_DETAILS === 'false') // without details...
                {
                    if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 1) {
                        $order->info['tax_groups'][TAX_ADD_TAX
                                                   . $tax_description] = $order->info['tax_groups'][TAX_ADD_TAX
                                                                                                    . $tax_description]
                                                                         ?? 0;
                        
                        $order->info['tax']                                          += xtc_calculate_tax(
                            $nc_ultra_data['costs'],
                            $tax
                        );
                        $order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += xtc_calculate_tax(
                            $nc_ultra_data['costs'],
                            $tax
                        );
                        $order->info['total']                                        += $nc_ultra_data['costs']
                                                                                        + xtc_calculate_tax(
                                                                                            $nc_ultra_data['costs'],
                                                                                            $tax
                                                                                        );
                        $gambioultra_fee                                             = xtc_add_tax(
                            $nc_ultra_data['costs'],
                            $tax
                        );
                    }
                    if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 0
                        && (int)$_SESSION['customers_status']['customers_status_add_tax_ot'] === 1) {
                        $order->info['tax_groups'][TAX_NO_TAX
                                                   . $tax_description] = $order->info['tax_groups'][TAX_NO_TAX
                                                                                                    . $tax_description]
                                                                         ?? 0;
                        
                        $gambioultra_fee                                          = $nc_ultra_data['costs'];
                        $order->info['tax']                                       += xtc_calculate_tax(
                            $nc_ultra_data['costs'],
                            $tax
                        );
                        $order->info['tax_groups'][TAX_NO_TAX . $tax_description] += xtc_calculate_tax(
                            $nc_ultra_data['costs'],
                            $tax
                        );
                        $order->info['subtotal']                                  += $gambioultra_fee;
                        $order->info['total']                                     += $gambioultra_fee;
                    }
                    if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 0
                        && (int)$_SESSION['customers_status']['customers_status_add_tax_ot'] !== 1) {
                        $gambioultra_fee         = $nc_ultra_data['costs'];
                        $order->info['subtotal'] += $gambioultra_fee;
                        $order->info['total']    += $gambioultra_fee;
                    }
                    $output_title   = $coo_text_mgr->get_text('name') . ':';
                    $this->output[] = [
                        'title' => $output_title,
                        'text'  => $xtPrice->xtcFormat($gambioultra_fee, true),
                        'value' => $gambioultra_fee,
                    ];
                } else {
                    //show details...
                    foreach ($nc_ultra_data['infos'] as $info) {
                        if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 1) {
                            $order->info['tax_groups'][TAX_ADD_TAX
                                                       . $tax_description] = $order->info['tax_groups'][TAX_ADD_TAX
                                                                                                        . $tax_description]
                                                                             ?? 0;
                            
                            $order->info['tax']                                          += xtc_calculate_tax(
                                $info['price_plain'],
                                $tax
                            );
                            $order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += xtc_calculate_tax(
                                $info['price_plain'],
                                $tax
                            );
                            $order->info['total']                                        += $info['price_plain']
                                                                                            + xtc_calculate_tax(
                                                                                                $info['price_plain'],
                                                                                                $tax
                                                                                            );
                            $gambioultra_fee                                             = xtc_add_tax(
                                $info['price_plain'],
                                $tax
                            );
                        }
                        if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 0
                            && (int)$_SESSION['customers_status']['customers_status_add_tax_ot'] === 1) {
                            $order->info['tax_groups'][TAX_NO_TAX
                                                       . $tax_description] = $order->info['tax_groups'][TAX_NO_TAX
                                                                                                        . $tax_description]
                                                                             ?? 0;
                            
                            $gambioultra_fee                                            = $info['price_plain'];
                            $order->info['tax']                                         += xtc_calculate_tax(
                                $info['price_plain'],
                                $tax
                            );
                            $order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += xtc_calculate_tax(
                                $info['price_plain'],
                                $tax
                            );
                            $order->info['subtotal']                                    += $gambioultra_fee;
                            $order->info['total']                                       += $gambioultra_fee;
                        }
                        if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 0
                            && (int)$_SESSION['customers_status']['customers_status_add_tax_ot'] !== 1) {
                            $gambioultra_fee         = $info['price_plain'];
                            $order->info['subtotal'] += $gambioultra_fee;
                            $order->info['total']    += $gambioultra_fee;
                        }
                        $output_title   = $coo_text_mgr->get_text('name') . ': ';
                        $this->output[] = [
                            'title' => $output_title . ' ' . $info['title'] . ': ',
                            'text'  => $xtPrice->xtcFormat($gambioultra_fee, true),
                            'value' => $gambioultra_fee,
                        ];
                    }//end foreach
                }
            }
        }
    }
    
    
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query(
                'select `value` from `gx_configurations` where `key` = "configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS"'
            );
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    function keys()
    {
        return [
            'configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS',
            'configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_SORT_ORDER',
            'configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_OUTPUT_NAME',
            'configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_DETAILS',
            'configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION',
            'configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS',
        ];
    }
    
    
    public function install()
    {
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_STATUS', 'true', '1','switcher')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_SORT_ORDER', '31', '2')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_OUTPUT_NAME', 'Sperrgutzuschlag', '2')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_DETAILS', 'true', '1','switcher')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_DESTINATION', 'both','6', 'shipping-destination')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_GAMBIOULTRA_TAX_CLASS', '0', '7', 'tax-class')"
        );
    }
    
    
    public function remove()
    {
        xtc_db_query(
            "delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys())
            . "')"
        );
    }
}

MainFactory::load_origin_class('ot_gambioultra');
