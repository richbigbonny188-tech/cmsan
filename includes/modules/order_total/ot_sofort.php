<?php
/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2023-04-28
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2023 SOFORT AG
 *
 * $Id: ot_sofort.php 6141 2013-05-14 10:02:52Z rotsch $
 *
 * ----------
 *
 * Estelco - Ebusiness & more
 * http://www.estelco.de
 * Copyright (C) 2007 Estelco
 *
 * based on:
 * Andreas Zimmermann / IT eSolutions http://www.it-esolutions.de
 * Copyright (C) 2004 IT eSolutions
 *
 * ----------
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]

 */

require_once(DIR_FS_CATALOG.'callback/sofort/helperFunctions.php');

class ot_sofort_ORIGIN
{
    var $title, $output;
    
    public $code;
    public $description;
    public $enabled;
    public $sort_order;
    public $include_shipping;
    public $include_tax;
    public $calculate_tax;
    public $amount;
    public $original_total;
    public $discount;
    public $amounts;
    public $num;
    public $percentage;
    public $payment;

    /**
     * @return int
     */
    public function getNum(): int
    {
        return $this->num;
    }
    public function __construct()
    {
        $this->code = 'ot_sofort';
        $this->title = defined('MODULE_ORDER_TOTAL_SOFORT_TITLE') ? MODULE_ORDER_TOTAL_SOFORT_TITLE : '';
        $this->description = defined('MODULE_ORDER_TOTAL_SOFORT_DESCRIPTION') ? MODULE_ORDER_TOTAL_SOFORT_DESCRIPTION : '';
        $this->enabled = defined('MODULE_ORDER_TOTAL_SOFORT_STATUS') && MODULE_ORDER_TOTAL_SOFORT_STATUS === 'true';
        $this->sort_order = defined('MODULE_ORDER_TOTAL_SOFORT_SORT_ORDER') ? MODULE_ORDER_TOTAL_SOFORT_SORT_ORDER : '0';
        $this->include_shipping = defined('MODULE_ORDER_TOTAL_SOFORT_INC_SHIPPING') ? MODULE_ORDER_TOTAL_SOFORT_INC_SHIPPING : 'false';
        $this->include_tax = defined('MODULE_ORDER_TOTAL_SOFORT_INC_TAX') ? MODULE_ORDER_TOTAL_SOFORT_INC_TAX : 'true';
        $this->calculate_tax = defined('MODULE_ORDER_TOTAL_SOFORT_CALC_TAX') ? MODULE_ORDER_TOTAL_SOFORT_CALC_TAX : 'true';
        $this->output = array();
        $this->amount = 0;
        $this->original_total = 0;
        $this->discount = array();
        $this->amounts = array();
        // Rabattfelder
        $this->num = 0;
        if ($this->enabled) {
        	if(MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SU != '') {
				$this->num++;
				$this->percentage[$this->num] = MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SU;
				$this->payment[$this->num] = 'sofort_sofortueberweisung';
        	}
        	if(MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SL != '') {
				$this->num++;
        		$this->percentage[$this->num] = MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SL;
				$this->payment[$this->num] = 'sofort_sofortlastschrift';
        	}
            if(MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_LS != '') {
				$this->num++;
        		$this->percentage[$this->num] = MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_LS;
				$this->payment[$this->num] = 'sofort_lastschrift';
        	}
        	if(MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SR != '') {
				$this->num++;
        		$this->percentage[$this->num] = MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SR;
				$this->payment[$this->num] = 'sofort_sofortrechnung';
        	}
        	if(MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SV != '') {
				$this->num++;
				$this->percentage[$this->num] = MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SV;
				$this->payment[$this->num] = 'sofort_sofortvorkasse';
        	}
        	if(MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_LS != '') {
				$this->num++;
				$this->percentage[$this->num] = MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_LS;
				$this->payment[$this->num] = 'sofort_lastschrift';
        	}
		}
    }

    function process()
    {
        global $order, $xtPrice;

        $allowed_zones = explode(',', MODULE_ORDER_TOTAL_SOFORT_ALLOWED);

        if ($this->enabled && (in_array($_SESSION['delivery_zone'], $allowed_zones) == true || MODULE_ORDER_TOTAL_SOFORT_ALLOWED == '')) {
            $this->xtc_order_total();
            $this->calculate_credit();
            if ($this->discount['sum']!=0) {
                for ($i=1; $i<=$this->num; $i++) {
                    if ($this->discount['amount' . $i]!=0) {
                        $this->output[] = array('title' =>
                        ($this->discount['pro' . $i] != 0.0 ?
                        number_format(abs($this->discount['pro' . $i]), 2, $xtPrice->currencies[$_SESSION['currency']]['decimal_point'], '') . '% ' .
                        ($this->discount['fee' . $i]!=0? ($this->discount['pro' . $i] != 0.0 ? ' +' : '') . $xtPrice->xtcFormat(abs($this->discount['fee' . $i]), true) . ' ':'') : '') .
                        ($this->discount['amount' . $i]<0?MODULE_ORDER_TOTAL_SOFORT_DISCOUNT:MODULE_ORDER_TOTAL_SOFORT_FEE) . ':',
                        'text' => $this->discount['amount' . $i]<0?$xtPrice->xtcFormat($this->discount['amount' . $i], true):$xtPrice->xtcFormat($this->discount['amount' . $i], true),
                        'value' => $this->discount['amount' . $i]);
                        $order->info['total'] += round($this->discount['amount' . $i],2);
                        $order->info['subtotal'] = $order->info['subtotal'] + $this->discount['amount' . $i];
                    }
                }
            }
        }
    }

    function calculate_credit($payment = '')
    {
        global $order;
        $discount = array();
        $values = array();

        if ($payment == '') {
            $payment = $_SESSION['payment'];
        }

        if ($this->include_shipping == 'false') {
            $module = substr($_SESSION['shipping']['id'], 0, strpos($_SESSION['shipping']['id'], '_'));
            $shipping_tax = xtc_get_tax_rate($GLOBALS[$module]->tax_class, $order->delivery['country']['id'], $order->delivery['zone_id']);
            if ($_SESSION['customers_status']['customers_status_show_price_tax'] && !$_SESSION['customers_status']['customers_status_add_tax_ot']) {
                $tod_shipping = $order->info['shipping_cost'] / (100 + $shipping_tax) * $shipping_tax;
            } else {
                $tod_shipping = $order->info['shipping_cost'] / 100 * $shipping_tax;
            }
        } else {
            $tod_shipping = 0;
        }

        for ($j=1; $j<=$this->num; $j++) {
            $do = false;
            if (strpos($this->percentage[$j], "|") !== false) {
                $strings = explode('|', $this->percentage[$j]);
                $allowed_zones = explode(',', $strings[0]);
                if (!in_array($_SESSION['delivery_zone'], $allowed_zones) == true && $strings[0] != '00') {
                    continue;
                }
                $string = $strings[1];
            } else {
                $string = $this->percentage[$j];
            }
            $discount_table = (preg_split("/[:,]/", $string));
            //print_r($discount_table);
            for ($i=0; $i<sizeof($discount_table); $i+=2) {
                if ($this->amount >= $discount_table[$i]) {
                    $values[$j]['minimum'] = $discount_table[$i];
                    $fees = explode('&', $discount_table[$i+1]);
                    $values[$j]['percent'] = $fees[0];
                    $values[$j]['fee'] = $fees[1]!=''?$fees[1]:0;
                } else {
                    break;
                }
            }

            if ($this->amount >= $values[$j]['minimum']) {
                $od_amount = 0;
                $tod_amount = 0;
                $table = explode(',' , $this->payment[$j]);
                for ($i = 0; $i < count($table); $i++) {
                    if ($payment == $table[$i]) $do = true;
                }
                if ($do) {
                    // Calculate tax reduction if necessary
                    if($this->calculate_tax == 'true') {
                    	if (isset($order->info['tax_groups']) && is_array($order->info['tax_groups'])) {
	                        // Calculate tax group deductions
	                        reset($order->info['tax_groups']);
	                        foreach($order->info['tax_groups'] as $key => $value) {
	                            if (strpos($key, $shipping_tax . '%') && $this->include_shipping =='true') {
	                                $god_amount = $this->get_discount(($value - $tod_shipping), $values[$j]['percent']);
	                            } else {
	                                $god_amount = $this->get_discount($value, $values[$j]['percent']);
	                            }
	                            if ($values[$j]['fee'] != 0 && count($this->amounts) > 0) {
	                                foreach($this->amounts as $key2=>$value2) {
	                                    if (strpos($key, $key2 . '%')) {
	                                        $god_amount += $values[$j]['fee'] * $value2 / $this->amounts['total'] * $key2 / 100 / (100 + $key2) * 100;
	                                    }
	                                }
	                            }
	                            $order->info['tax_groups'][$key] -= $god_amount;
	                        }
                    	}
                        // Calculate main tax reduction
                        if($this->include_shipping =='true')
                            $tod_amount = $this->get_discount(($order->info['tax'] - $tod_shipping), $values[$j]['percent']);
                        else
                            $tod_amount = $god_amount;
                        $order->info['tax'] -= $tod_amount;
                    }
                    $values[$j]['discount'] = $this->get_discount($this->amount, $values[$j]['percent']) + $values[$j]['fee'];
                }
            }
            $this->discount['sum'] -= $values[$j]['discount'];
            $this->discount['amount' . $j] = -$values[$j]['discount'];
            $this->discount['pro' . $j] = $values[$j]['percent'];
            $this->discount['fee' . $j] = $values[$j]['fee'];
            if ($do && MODULE_ORDER_TOTAL_SOFORT_BREAK != 'true') break;
        }
    }

    function xtc_order_total()
    {
        global $order;
        $order_total = $order->info['total'];
        // Check if gift voucher is in cart and adjust total
        $products = $_SESSION['cart']->get_products();
        for ($i=0; $i<sizeof($products); $i++) {
            $t_prid = xtc_get_prid($products[$i]['id']);
            $gv_query = xtc_db_query("SELECT products_price, products_tax_class_id, products_model FROM " . HelperFunctions::escapeSql(TABLE_PRODUCTS) . " WHERE products_id = '" . HelperFunctions::escapeSql($t_prid) . "'");
            $gv_result = xtc_db_fetch_array($gv_query);
            $qty = $_SESSION['cart']->get_quantity($products[$i]['id']);
            $products_tax = xtc_get_tax_rate($gv_result['products_tax_class_id']);
            if (preg_match('/^GIFT/', $gv_result['products_model']))
            {
                if ($this->include_tax =='false') {
                    $gv_amount = $gv_result['products_price'] * $qty;
                } else {
                    $gv_amount = ($gv_result['products_price'] + xtc_calculate_tax($gv_result['products_price'],$products_tax)) * $qty;
                }
                $order_total -= $gv_amount;
            } else {
                $this->amounts[(string)$products_tax] += $gv_result['products_price'] * (int)$qty;
                $this->amounts['total'] += $gv_result['products_price'] * $qty;
            }
        }
        if ($this->include_shipping == 'false') $order_total -= $order->info['shipping_cost'];
        if ($this->include_tax == 'false') $order_total -= $order->info['tax'];
        $this->amount = $order_total;
    }

    function get_percent($payment, $type = 'percent')
    {
        global $order, $xtPrice;
        $string = '';
        $allowed_zones = explode(',', MODULE_ORDER_TOTAL_SOFORT_ALLOWED);

        if ($this->enabled && (in_array($_SESSION['delivery_zone'], $allowed_zones) == true || MODULE_ORDER_TOTAL_SOFORT_ALLOWED == '')) {
            $this->calculate_credit($payment);
            if ($this->discount['sum']!=0) {
                for ($i=1; $i<=$this->num; $i++) {
                    if ($this->discount['amount' . $i]!=0) {
                        if ($type == 'price') {
                            $string .= $xtPrice->xtcFormat(abs($this->discount['amount' . $i]), true) . ' ' . ($this->discount['amount' . $i]<0?MODULE_ORDER_TOTAL_SOFORT_DISCOUNT:MODULE_ORDER_TOTAL_SOFORT_FEE);
                        } else {
                            $string .= ($this->discount['pro' . $i] != 0.0 ?
                            number_format(abs($this->discount['pro' . $i]), 2, $xtPrice->currencies[$_SESSION['currency']]['decimal_point'], '') . '% ' : '') .
                            ($this->discount['fee' . $i]!=0? ($this->discount['pro' . $i] != 0.0 ? ' +' : '') . $xtPrice->xtcFormat(abs($this->discount['fee' . $i]), true) . ' ' : '') .
                            ($this->discount['amount' . $i]<0?MODULE_ORDER_TOTAL_SOFORT_DISCOUNT:MODULE_ORDER_TOTAL_SOFORT_FEE);
                        }
                        if (MODULE_ORDER_TOTAL_SOFORT_BREAK != 'true') break;
                    }
                }
            }
        }
        return $string;
    }

    function get_discount($value, $percent)
    {
        return round($value * 100) / 100 * $percent / 100;
    }

    function check()
    {
        if (!isset($this->check)) {
            $check_query = xtc_db_query("SELECT `value` FROM `gx_configurations` WHERE `key` = 'configuration/MODULE_ORDER_TOTAL_SOFORT_STATUS'");
            $this->check = xtc_db_num_rows($check_query);
        }
        return $this->check;
    }

    function install()
    {
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_STATUS', 'true', '1','switcher')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_SORT_ORDER', '49', '2')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_INC_SHIPPING', 'false', '100005', 'switcher')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_INC_TAX', 'true', '100006','switcher')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_CALC_TAX', 'true', '100005','switcher')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_ALLOWED', '', '2')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_TAX_CLASS', '0', '100007', 'tax-class')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`, `type`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_BREAK', 'false', '3','switcher')");

        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SU', '100:4', '10')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SL', '100:4', '11')");
        xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SV', '100:4', '12')");
		xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_SR', '100:4', '13')");
		xtc_db_query("INSERT INTO `gx_configurations` (`key`, `value`, `sort_order`) VALUES ('configuration/MODULE_ORDER_TOTAL_SOFORT_PERCENTAGE_LS', '100:4', '13')");
    }

    function keys()
    {
        $keys = array();
        $check_query = xtc_db_query("SELECT `key` FROM `gx_configurations` WHERE `key` LIKE 'configuration/MODULE_ORDER_TOTAL_SOFORT_%' ORDER BY sort_order");
        while($key = xtc_db_fetch_array($check_query)) {
            $keys[] = $key['key'];
        }
        return $keys;
    }

    function remove()
    {
        xtc_db_query("DELETE FROM `gx_configurations` WHERE `key` LIKE 'configuration/MODULE_ORDER_TOTAL_SOFORT_%'");
    }
}
MainFactory::load_origin_class('ot_sofort');