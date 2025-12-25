<?php
/* --------------------------------------------------------------
   ot_tsexcellence.php 2023-04-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class ot_tsexcellence_ORIGIN {
	var $title, $output;
    
    public $code;
    public $description;
    public $enabled;
    public $sort_order;

	public function __construct() {
		global $xtPrice;
		$this->code = 'ot_tsexcellence';
		$this->title = defined('MODULE_ORDER_TOTAL_TSEXCELLENCE_TITLE') ? MODULE_ORDER_TOTAL_TSEXCELLENCE_TITLE : '';
		$this->description = defined('MODULE_ORDER_TOTAL_TSEXCELLENCE_DESCRIPTION') ? MODULE_ORDER_TOTAL_TSEXCELLENCE_DESCRIPTION : '';
		$this->enabled = defined('MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS') && MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS === 'true';
		$this->sort_order = defined('MODULE_ORDER_TOTAL_TSEXCELLENCE_SORT_ORDER') ? MODULE_ORDER_TOTAL_TSEXCELLENCE_SORT_ORDER : '0';
		$this->output = array();
	}

	function process() {
		global $order, $xtPrice, $shipping;

		if(isset($_SESSION['ts_excellence']) && strpos($_SERVER['REQUEST_URI'], 'checkout_confirmation') !== false) {
			if(!isset($_SESSION['ts_excellence']['from_protection'])) {
				// if checkout_confirmation is revisited after activating buyer protection, previous selection of buyer protection becomes invalid
				unset($_SESSION['ts_excellence']);
			}
		}

		if(isset($_SESSION['ts_excellence'])) {
			$service = new GMTSService();
			$tsid = $service->findExcellenceID($_SESSION['language_code']);
			$trusted_amount = round($order->info['total'], 2);
			$product = $service->findProtectionProduct($tsid, $trusted_amount, $order->info['currency']);
			if($product['tsproductid'] != $_SESSION['ts_excellence']['tsproductid']) {
				unset($_SESSION['ts_excellence']);
			}
		}

		if(isset($_SESSION['ts_excellence'])) {
			$tax = xtc_get_tax_rate(MODULE_ORDER_TOTAL_TSEXCELLENCE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);
			$tax_description = xtc_get_tax_description(MODULE_ORDER_TOTAL_TSEXCELLENCE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);

			$cost_value = $_SESSION['ts_excellence']['protection_grossfee'];
			$tax_value = $cost_value - ($cost_value / (($tax + 100) / 100));
			$order->info['tax'] += $tax_value;
			if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
                $order->info['tax_groups'][TAX_ADD_TAX . $tax_description] = $order->info['tax_groups'][TAX_ADD_TAX . $tax_description] ?? 0;
                
				$order->info['tax_groups'][TAX_ADD_TAX . "$tax_description"] += $tax_value;
				$order->info['total'] += $cost_value;
			}

			if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 0 && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1) {
                $order->info['tax_groups'][TAX_NO_TAX . $tax_description] = $order->info['tax_groups'][TAX_NO_TAX . $tax_description] ?? 0;
                
                $order->info['tax_groups'][TAX_NO_TAX . "$tax_description"] += $tax_value;
				$order->info['total'] += $cost_value - $tax_value;
				$order->info['subtotal'] += $cost_value - $tax_value;
			}

			$title_upto = round($_SESSION['ts_excellence']['protectedamount']) . ' '. $order->info['currency'];
			$cost = $xtPrice->xtcFormat($cost_value, true);
			$this->output[] = array('title' => $this->title . ' ('. MODULE_ORDER_TOTAL_TSEXCELLENCE_UPTO .' '. $title_upto .'):',
									'text' => $cost,
									'value' => $cost_value);
		}
	}

	function check() {
		if(!isset($this->_check)) {
			$check_query = xtc_db_query("select `value` from `gx_configurations` where `key` = 'configuration/MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}
		return $this->_check;
	}

	function keys() {
		return array(
			'configuration/MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS',
			'configuration/MODULE_ORDER_TOTAL_TSEXCELLENCE_SORT_ORDER',
			'configuration/MODULE_ORDER_TOTAL_TSEXCELLENCE_TAX_CLASS',
		);
	}

    function install() {
	  xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_TSEXCELLENCE_STATUS', 'true', '0', 'switcher')");
      xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_TSEXCELLENCE_SORT_ORDER', '35', '0')");
      xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_TSEXCELLENCE_TAX_CLASS', '0', '0', 'tax-class')");
	}

	function remove() {
		xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
	}
}

MainFactory::load_origin_class('ot_tsexcellence');