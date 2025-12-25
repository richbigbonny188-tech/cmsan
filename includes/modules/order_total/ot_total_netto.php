<?php
/* --------------------------------------------------------------
   ot_total_netto.php 2023-04-28 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_total.php,v 1.7 2003/02/13); www.oscommerce.com 
   (c) 2003	 nextcommerce (ot_total.php,v 1.11 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_total.php 1002 2005-07-10 16:11:37Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/


  class ot_total_netto_ORIGIN {
    var $title, $output;
    
    public $code;
    public $description;
    public $enabled;
    public $sort_order;

    public function __construct() {
      $this->code = 'ot_total_netto';
      $this->title = defined('MODULE_ORDER_TOTAL_TOTAL_NETTO_TITLE') ? MODULE_ORDER_TOTAL_TOTAL_NETTO_TITLE : '';
      $this->description = defined('MODULE_ORDER_TOTAL_TOTAL_NETTO_DESCRIPTION') ? MODULE_ORDER_TOTAL_TOTAL_NETTO_DESCRIPTION : '';
      $this->enabled = defined('MODULE_ORDER_TOTAL_TOTAL_NETTO_STATUS') && MODULE_ORDER_TOTAL_TOTAL_NETTO_STATUS === 'true';
      $this->sort_order = defined('MODULE_ORDER_TOTAL_TOTAL_NETTO_SORT_ORDER') ? MODULE_ORDER_TOTAL_TOTAL_NETTO_SORT_ORDER : '0';

      $this->output = array();
    }


	  function process()
	  {
		  global $order, $xtPrice;

		  $tax_total = 0;

		  reset($order->info['tax_groups']);
		  foreach($order->info['tax_groups'] as $key => $value)
		  {
			  $tax_total += $value;
		  }

		  if($_SESSION['customers_status']['customers_status_show_price_tax'] != 0)
		  {
			  $text      = $xtPrice->xtcFormat($order->info['total'] - $tax_total, true);
			  $textArray = explode(' ', trim($text));
			  $text      = ((float)$textArray[0] < 0) ? '0.00 ' . $textArray[1] : $textArray[0] . ' ' . $textArray[1];

			  $unpreparedValue = $xtPrice->xtcFormat($order->info['total'] - $tax_total, false);
			  $value           =
				  ($unpreparedValue < 0) ? ($unpreparedValue + (0 - $unpreparedValue)) : $unpreparedValue;

			  $this->output[] = array(
				  'title' => $this->title . ':',
				  'text'  => $text,
				  'value' => $value
			  );
		  }
	  }

    function check() {
      if (!isset($this->_check)) {
        $check_query = xtc_db_query("select `value` from `gx_configurations` where `key` = 'configuration/MODULE_ORDER_TOTAL_TOTAL_NETTO_STATUS'");
        $this->_check = xtc_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('configuration/MODULE_ORDER_TOTAL_TOTAL_NETTO_STATUS', 'configuration/MODULE_ORDER_TOTAL_TOTAL_NETTO_SORT_ORDER');
    }

    function install() {
      xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`, `type`) values ('configuration/MODULE_ORDER_TOTAL_TOTAL_NETTO_STATUS', 'true', '1','switcher')");
      xtc_db_query("insert into `gx_configurations` (`key`, `value`, `sort_order`) values ('configuration/MODULE_ORDER_TOTAL_TOTAL_NETTO_SORT_ORDER', '98', '2')");
    }

    function remove() {
      xtc_db_query("delete from `gx_configurations` where `key` in ('" . implode("', '", $this->keys()) . "')");
    }
  }
  
MainFactory::load_origin_class('ot_total_netto');