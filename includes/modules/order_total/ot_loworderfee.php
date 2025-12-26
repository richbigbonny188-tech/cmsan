<?php
/* --------------------------------------------------------------
   ot_loworderfee.php 2022-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/* -----------------------------------------------------------------------------------------
   $Id: ot_loworderfee.php 1002 2005-07-10 16:11:37Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_loworderfee.php,v 1.11 2003/02/14); www.oscommerce.com
   (c) 2003	 nextcommerce (ot_loworderfee.php,v 1.7 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class ot_loworderfee_ORIGIN
{
    public $code;
    public $title;
    public $output;
    public $description;
    public $enabled;
    public $sort_order;
    
    
    public function __construct()
    {
        $this->code        = 'ot_loworderfee';
        $this->title       = defined(
            'MODULE_ORDER_TOTAL_LOWORDERFEE_TITLE'
        ) ? MODULE_ORDER_TOTAL_LOWORDERFEE_TITLE : '';
        $this->description = defined(
            'MODULE_ORDER_TOTAL_LOWORDERFEE_DESCRIPTION'
        ) ? MODULE_ORDER_TOTAL_LOWORDERFEE_DESCRIPTION : '';
        $this->enabled     = defined('MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS')
                             && MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS === 'true';
        $this->sort_order  = defined(
            'MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER'
        ) ? MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER : '0';
        
        $this->output = [];
    }
    
    
    public function process()
    {
        require_once DIR_FS_INC . 'xtc_calculate_tax.inc.php';
        
        $order   = $GLOBALS['order'];
        $xtPrice = $GLOBALS['xtPrice'];
        
        if (MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE === 'true') {
            $pass              = false;
            $deliveryCountryId = $order->delivery['country_id'];
            $deliveryZoneId    = $order->delivery['zone_id'];
            if (null === $deliveryCountryId) {
                if (!empty($_SESSION['delivery_zone'])) {
                    /** @var \CountryService $countryService */
                    $countryService    = StaticGXCoreLoader::getService('Country');
                    $deliveryCountry   = $countryService->getCountryByIso2($_SESSION['delivery_zone']);
                    $deliveryCountryId = $deliveryCountry->getId();
                    $deliveryZoneId    = $countryService->getUnknownCountryZoneByName('unknown zone')->getId();
                } else {
                    $this->output = [];
                    return;
                }
            }
            
            switch (MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION) {
                case 'national':
                    if ((int)$deliveryCountryId === (int)STORE_COUNTRY) {
                        $pass = true;
                    }
                    break;
                case 'international':
                    if ((int)$deliveryCountryId !== (int)STORE_COUNTRY) {
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
            
            if (($pass === true)
                && (($order->info['total'] - $order->info['shipping_cost'])
                    < MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER)) {
                $low_order_fee   = 0.0;
                $tax             = xtc_get_tax_rate(
                    MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS,
                    $deliveryCountryId,
                    $deliveryZoneId
                );
                $tax_description = xtc_get_tax_description(
                    MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS,
                    $deliveryCountryId,
                    $deliveryZoneId
                );
                
                if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 1) {
                    $order->info['tax_groups'][TAX_ADD_TAX . $tax_description] = $order->info['tax_groups'][TAX_ADD_TAX
                                                                                                            . $tax_description]
                                                                                 ?? 0;
                    
                    $order->info['tax']                                        += xtc_calculate_tax(
                        MODULE_ORDER_TOTAL_LOWORDERFEE_FEE,
                        $tax
                    );
                    $order->info['tax_groups'][TAX_ADD_TAX . $tax_description] += xtc_calculate_tax(
                        MODULE_ORDER_TOTAL_LOWORDERFEE_FEE,
                        $tax
                    );
                    $order->info['total']                                      += MODULE_ORDER_TOTAL_LOWORDERFEE_FEE
                                                                                  + xtc_calculate_tax(
                                                                                      MODULE_ORDER_TOTAL_LOWORDERFEE_FEE,
                                                                                      $tax
                                                                                  );
                    $low_order_fee                                             = xtc_add_tax(
                        MODULE_ORDER_TOTAL_LOWORDERFEE_FEE,
                        $tax
                    );
                }
                
                if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 0
                    && (int)$_SESSION['customers_status']['customers_status_add_tax_ot'] === 1) {
                    $order->info['tax_groups'][TAX_NO_TAX . $tax_description] = $order->info['tax_groups'][TAX_NO_TAX
                                                                                                           . $tax_description]
                                                                                ?? 0;
                    
                    $low_order_fee                                            = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE;
                    $order->info['tax']                                       += xtc_calculate_tax(
                        MODULE_ORDER_TOTAL_LOWORDERFEE_FEE,
                        $tax
                    );
                    $order->info['tax_groups'][TAX_NO_TAX . $tax_description] += xtc_calculate_tax(
                        MODULE_ORDER_TOTAL_LOWORDERFEE_FEE,
                        $tax
                    );
                    $order->info['subtotal']                                  += $low_order_fee;
                    $order->info['total']                                     += $low_order_fee;
                }
                
                if ((int)$_SESSION['customers_status']['customers_status_show_price_tax'] === 0
                    && (int)$_SESSION['customers_status']['customers_status_add_tax_ot'] !== 1) {
                    $low_order_fee           = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE;
                    $order->info['subtotal'] += $low_order_fee;
                    $order->info['total']    += $low_order_fee;
                }
                
                $this->output[] = [
                    'title' => $this->title . ':',
                    'text'  => $xtPrice->xtcFormat($low_order_fee, true),
                    'value' => $low_order_fee,
                ];
            }
        }
    }
    
    
    public function check()
    {
        if (!isset($this->_check)) {
            $check_query  = xtc_db_query(
                sprintf(
                    "select `value` from `%s` where `key` = 'configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS'",
                    'gx_configurations'
                )
            );
            $this->_check = xtc_db_num_rows($check_query);
        }
        
        return $this->_check;
    }
    
    
    public function keys()
    {
        return [
            'configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS',
            'configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER',
            'configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE',
            'configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER',
            'configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_FEE',
            'configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION',
            'configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS',
        ];
    }
    
    
    public function install()
    {
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS', 'true', '1','switcher')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_SORT_ORDER', '11', '2')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_LOW_ORDER_FEE', 'false', '3', 'switcher')"
        );
        // Todo: $currencies->format use_function validation
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER', '50', '4')"
        );
        // Todo: $currencies->format use_function validation
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_FEE', '5', '5')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION', 'both','6', 'shipping-destination')"
        );
        xtc_db_query(
            "insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS', '0', '7', 'tax-class')"
        );
    }
    
    
    public function remove()
    {
        xtc_db_query(
            'delete from ' . TABLE_CONFIGURATION . " where `key` in ('" . implode("', '", $this->keys())
            . "')"
        );
    }
}

MainFactory::load_origin_class('ot_loworderfee');
