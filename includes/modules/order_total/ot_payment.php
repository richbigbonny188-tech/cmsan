<?php
/* --------------------------------------------------------------
   ot_payment.php 2023-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

$Id: ot_payment.php,v 1.2.3 (3.0.4) 2005/10/27 13:55:50 Anotherone Exp $

  André Estel / Estelco http://www.estelco.de

  Copyright (C) 2005 Estelco

  based on:
  Andreas Zimmermann / IT eSolutions http://www.it-esolutions.de

  Copyright (C) 2004 IT eSolutions
  -----------------------------------------------------------------------------------------

  (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com

  Released under the GNU General Public License

  ---------------------------------------------------------------------------------------*/

class ot_payment_ORIGIN
{
    /**
     * @var string
     */
    public $code;
    
    /**
     * @var string
     */
    public $title;
    
    /**
     * @var string
     */
    public $description;
    
    /**
     * @var bool
     */
    public $enabled;
    
    /**
     * @var string (string-shaped number)
     */
    public $sort_order;
    
    /**
     * @var string (string-shaped boolean)
     */
    public $include_shipping;
    
    /**
     * @var string (string-shaped boolean)
     */
    public $include_tax;
    
    /**
     * @var string
     */
    public $percentage;
    
    /**
     * @var string
     */
    public $percentage2;
    
    /**
     * @var string (string-shaped boolean)
     */
    public $calculate_tax;
    
    /**
     * @var string
     */
    public $amount1_desc;
    
    /**
     * @var string
     */
    public $amount2_desc;
    
    /**
     * @var array
     */
    public $output;
    
    /**
     * @var mixed
     */
    public $deduction;
    
    /**
     * @var ?bool
     */
    public $check;
    
    
    public function __construct()
    {
        $this->code             = 'ot_payment';
        $this->title            = defined('MODULE_ORDER_TOTAL_PAYMENT_TITLE') ? MODULE_ORDER_TOTAL_PAYMENT_TITLE : '';
        $this->description      = defined('MODULE_ORDER_TOTAL_PAYMENT_DESCRIPTION') ? MODULE_ORDER_TOTAL_PAYMENT_DESCRIPTION : '';
        $this->enabled          = defined('MODULE_ORDER_TOTAL_PAYMENT_STATUS')
                                  && MODULE_ORDER_TOTAL_PAYMENT_STATUS === 'true';
        $this->check            = null;
        $this->sort_order       = defined('MODULE_ORDER_TOTAL_PAYMENT_SORT_ORDER') ? MODULE_ORDER_TOTAL_PAYMENT_SORT_ORDER : '0';
        $this->include_shipping = defined('MODULE_ORDER_TOTAL_PAYMENT_INC_SHIPPING') ? MODULE_ORDER_TOTAL_PAYMENT_INC_SHIPPING : 'false';
        $this->include_tax      = defined('MODULE_ORDER_TOTAL_PAYMENT_INC_TAX') ? MODULE_ORDER_TOTAL_PAYMENT_INC_TAX : 'true';
        $this->percentage       = defined('MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE') ? MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE : '100:4';
        $this->percentage2      = defined('MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE2') ? MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE2 : '100:2'; //neu
        $this->calculate_tax    = defined('MODULE_ORDER_TOTAL_PAYMENT_CALC_TAX') ? MODULE_ORDER_TOTAL_PAYMENT_CALC_TAX : 'true';
        $this->amount1_desc     = defined('MODULE_ORDER_TOTAL_PAYMENT_AMOUNT1') ? MODULE_ORDER_TOTAL_PAYMENT_AMOUNT1 : '';
        $this->amount2_desc     = defined('MODULE_ORDER_TOTAL_PAYMENT_AMOUNT2') ? MODULE_ORDER_TOTAL_PAYMENT_AMOUNT2 : '';
        $this->output           = [];
    }
    
    
    public function process()
    {
        $order   = $GLOBALS['order'];
        $xtPrice = $GLOBALS['xtPrice'];
        
        $allowed_zones = explode(',', MODULE_ORDER_TOTAL_PAYMENT_ALLOWED);
        
        if ($this->enabled
            && (in_array($_SESSION['delivery_zone'] ?? null, $allowed_zones, true)
                || MODULE_ORDER_TOTAL_PAYMENT_ALLOWED === '')) {
            $discount = $this->calculate_credit($this->xtc_order_total());
            if ((float)$discount['sum'] !== 0.0) {
                $this->deduction = $discount['sum'];
                if ($discount['amount1'] !== 0) {
                    $this->output[] = [
                        'title' => abs($discount['pro1']) . "% " . $this->amount1_desc . ':',
                        'text'  => $xtPrice->xtcFormat($discount['sum'], true),
                        'value' => $discount['sum'],
                    ];
                } elseif ($discount['amount2'] !== 0) {
                    $this->output[] = [
                        'title' => abs($discount['pro2']) . "% " . $this->amount2_desc . ':',
                        'text'  => $xtPrice->xtcFormat($discount['sum'], true),
                        'value' => $discount['sum'],
                    ];
                }
                $order->info['subtotal'] += $discount['sum'];
                $order->info['total']    += $discount['sum'];
            }
        }
    }
    
    
    public function calculate_credit($amount)
    {
        $order      = $GLOBALS['order'];
        $od_amount  = 0;
        $od_amount2 = 0;
        $discount   = [];
        
        $discount_table1 = (preg_split('/[:,]/', $this->percentage));
        
        $discountPercentage1 = 0;
        $minimum1            = 0;
        for ($i = 0, $sizeOfTable = count($discount_table1); $i < $sizeOfTable; $i += 2) {
            if ($amount >= $discount_table1[$i]) {
                $discountPercentage1 = $discount_table1[$i + 1];
                $minimum1            = $discount_table1[$i];
            } else {
                break;
            }
        }
        
        $discount_table2 = (preg_split('/[:,]/', $this->percentage2));
        
        $discountPercentage2 = 0;
        $minimum2            = 0;
        for ($i = 0, $sizeOfTable = count($discount_table2); $i < $sizeOfTable; $i += 2) {
            if ($amount >= $discount_table2[$i]) {
                $discountPercentage2 = $discount_table2[$i + 1];
                $minimum2            = $discount_table2[$i];
            } else {
                break;
            }
        }
    
        $paymentTypes1 = explode(',', MODULE_ORDER_TOTAL_PAYMENT_TYPE);
        $discount['amount1'] = 0;
        $discount['pro1']    = 0;
        if ($amount >= $minimum1 && in_array($_SESSION['payment'], $paymentTypes1, true)) {
            // Calculate tax reduction if necessary
            if ($this->calculate_tax === 'true') {
                // Calculate main tax reduction
                $order->info['tax'] = $order->info['tax'] ?? 0.0;
                $tod_amount         = round($order->info['tax'] * 100) / 100 * $discountPercentage1 / 100;
                $order->info['tax'] -= $tod_amount;
                // Calculate tax group deductions
                reset($order->info['tax_groups']);
                foreach ($order->info['tax_groups'] as $key => $value) {
                    $god_amount                      = round($value * 100) / 100 * $discountPercentage1 / 100;
                    $order->info['tax_groups'][$key] -= $god_amount;
                }
            }
            $od_amount           = round($amount * 100) / 100 * $discountPercentage1 / 100;
            $discount['amount1'] = $od_amount;
            $discount['pro1']    = $discountPercentage1;
        }
        
        $amount2 = $amount - $od_amount;

        $paymentTypes2 = explode(',', MODULE_ORDER_TOTAL_PAYMENT_TYPE2);
        $discount['amount2'] = 0;
        $discount['pro2']    = 0;
        if ($amount2 >= $minimum2 && in_array($_SESSION['payment'], $paymentTypes2, true)) {
            // Calculate tax reduction if necessary
            if ($this->calculate_tax === 'true') {
                // Calculate main tax reduction
                $order->info['tax'] = $order->info['tax'] ?? 0.0;
                $tod_amount2        = round($order->info['tax'] * 100) / 100 * $discountPercentage2 / 100;
                $order->info['tax'] -= $tod_amount2;
                // Calculate tax group deductions
                foreach ($order->info['tax_groups'] as $key => $value) {
                    $god_amount2                     = round($value * 100) / 100 * $discountPercentage2 / 100;
                    $order->info['tax_groups'][$key] -= $god_amount2;
                }
            }
            $od_amount2          = round($amount2 * 100) / 100 * $discountPercentage2 / 100;
            $discount['amount2'] = $od_amount2;
            $discount['pro2']    = $discountPercentage2;
        }
        $discount['sum'] = -($od_amount + $od_amount2);
        
        return $discount;
    }
    
    
    public function xtc_order_total()
    {
        $order       = $GLOBALS['order'];
        $cart        = $GLOBALS['cart'] ?? $_SESSION['cart'];
        $order_total = $order->info['total'];
        // Check if gift voucher is in cart and adjust total
        $products = $_SESSION['cart']->get_products();
        foreach ($products as $product) {
            $t_prid    = xtc_get_prid($product['id']);
            $gv_query  = xtc_db_query("select products_price, products_tax_class_id, products_model from "
                                      . TABLE_PRODUCTS . " where products_id = '" . $t_prid . "'");
            $gv_result = xtc_db_fetch_array($gv_query);
            if (0 === strpos(addslashes($gv_result['products_model']), "GIFT")) {
                $qty          = $cart->get_quantity($t_prid);
                $products_tax = xtc_get_tax_rate($gv_result['products_tax_class_id']);
                if ($this->include_tax === 'false') {
                    $gv_amount = $gv_result['products_price'] * $qty;
                } else {
                    $gv_amount = ($gv_result['products_price'] + xtc_calculate_tax($gv_result['products_price'],
                                                                                   $products_tax)) * $qty;
                }
                $order_total -= $gv_amount;
            }
        }
        if ($this->include_shipping === 'false') {
            $order_total -= $order->info['shipping_cost'];
        }
        if ($this->include_tax === 'false') {
            if ($_SESSION['customers_status']['customers_status_add_tax_ot'] === '1') {
                $order_total -= ($order->info['tax'] ?? 0.0);
            }
        }
        
        return $order_total;
    }
    
    
    public function check()
    {
        if (!isset($this->check)) {
            $check_query = xtc_db_query("select `value` from `gx_configurations` where `key` = 'configuration/MODULE_ORDER_TOTAL_PAYMENT_STATUS'");
            $this->check = xtc_db_num_rows($check_query);
        }
        
        return $this->check;
    }
    
    
    public function keys()
    {
        return [
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_STATUS',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_SORT_ORDER',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE2',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_TYPE',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_TYPE2',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_INC_SHIPPING',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_INC_TAX',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_CALC_TAX',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_ALLOWED',
            'configuration/MODULE_ORDER_TOTAL_PAYMENT_TAX_CLASS',
        ];
    }
    
    
    public function install()
    {
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_STATUS', 'true', '1','switcher')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_SORT_ORDER', '49', '2')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_INC_SHIPPING', 'false', '5', 'switcher')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_INC_TAX', 'true', '6','switcher')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE', '100:4', '4')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_PERCENTAGE2', '100:2', '4')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_CALC_TAX', 'true', '5','switcher')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_TYPE', 'moneyorder', '3')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_TYPE2', 'cod', '3')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_ALLOWED', '', '2')");
        xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_PAYMENT_TAX_CLASS', '0', '7', 'tax-class')");
    }
    
    
    public function remove()
    {
        $keys       = '';
        $keys_array = $this->keys();
        foreach ($keys_array as $key) {
            $keys .= "'" . $key . "',";
        }
        $keys = substr($keys, 0, -1);
        
        xtc_db_query("delete from `gx_configurations` where `key` in (" . $keys . ")");
    }
    
}

MainFactory::load_origin_class('ot_payment');
