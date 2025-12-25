<?php
/* --------------------------------------------------------------
   ot_tax.php 2023-04-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(ot_tax.php,v 1.14 2003/02/14); www.oscommerce.com  
   (c) 2003	 nextcommerce (ot_tax.php,v 1.11 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_tax.php 1002 2005-07-10 16:11:37Z mz $)

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

/**
 * Class ot_tax
 */
class ot_tax_ORIGIN
{
	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var array
	 */
	public $output;
    
    public $code;
    public $description;
    public $enabled;
    public $sort_order;


	public function __construct()
	{
		$this->code        = 'ot_tax';
		$this->title       = defined('MODULE_ORDER_TOTAL_TAX_TITLE') ? MODULE_ORDER_TOTAL_TAX_TITLE : '';
		$this->description = defined('MODULE_ORDER_TOTAL_TAX_DESCRIPTION') ? MODULE_ORDER_TOTAL_TAX_DESCRIPTION : '';
		$this->enabled     = defined('MODULE_ORDER_TOTAL_TAX_STATUS') && MODULE_ORDER_TOTAL_TAX_STATUS === 'true';
		$this->sort_order  = defined('MODULE_ORDER_TOTAL_TAX_SORT_ORDER') ? MODULE_ORDER_TOTAL_TAX_SORT_ORDER : '0';

		$this->output = array();
	}


	public function process()
	{
		global $order, $xtPrice;
		
		if (!isset($order->info['tax_groups'])) {
			return;
		}
		
		reset($order->info['tax_groups']);
		foreach($order->info['tax_groups'] as $key => $value)
		{
			if($value > 0 || (gm_get_conf('DISPLAY_0_PROCENT_TAX') === '1' && $value >= 0))
			{
				if($_SESSION['customers_status']['customers_status_show_price_tax'] != 0)
				{
					$this->output[] = array(
						'title' => $key . ':',
						'text'  => $xtPrice->xtcFormat($value, true),
						'value' => $xtPrice->xtcFormat($value, false)
					);
				}
				elseif($_SESSION['customers_status']['customers_status_show_price_tax'] == 0
				       && $_SESSION['customers_status']['customers_status_add_tax_ot'] == 1
				)
				{
					$this->output[] = array(
						'title' => $key . ':',
						'text'  => $xtPrice->xtcFormat($value, true),
						'value' => $xtPrice->xtcFormat($value, false)
					);
				}
			}
			
			if($this->isIntracommunityDelivery())
			{
				$this->output[] = array(
					'title' => INTRACOMMUNITY_DELIVERY_TEXT,
					'text'  => ' ', //This is not a normal space. This is a not visible UTF 8 character. Do not change it!
					'value' => $xtPrice->xtcFormat(0, false)
				);
			}
		}
	}


	/**
	 * @return bool|int
	 */
	public function check()
	{
		if(!isset($this->_check))
		{
			$check_query  = xtc_db_query("SELECT `value`
										  FROM `gx_configurations`
										  WHERE `key` = 'configuration/MODULE_ORDER_TOTAL_TAX_STATUS'");
			$this->_check = xtc_db_num_rows($check_query);
		}

		return $this->_check;
	}


	/**
	 * @return bool
	 */
	public function isIntracommunityDelivery()
	{
		if((bool)$_SESSION['customers_status']['customers_status_show_price_tax'] === true)
		{
			return false;
		}

		if(defined('STORE_OWNER_VAT_ID') && strlen(STORE_OWNER_VAT_ID) >= 2)
		{
			$storeCountryIsoCode = strtoupper(substr(trim(STORE_OWNER_VAT_ID), 0, 2));
		}
		else
		{
			return false;
		}

		$customerVatId = trim($_SESSION['customer_vat_id']);

		if(strlen($customerVatId) >= 2)
		{
			$customerCountryIsoCode = strtoupper(substr($customerVatId, 0, 2));
		}
		else
		{
			return false;
		}

		$euCountriesIsoCodes = array(
			'BE',
			'BG',
			'DK',
			'DE',
			'EE',
			'FI',
			'FR',
			'GR',
			'IE',
			'IT',
			'HR',
			'LV',
			'LT',
			'LU',
			'MT',
			'NL',
			'AT',
			'PL',
			'PT',
			'RO',
			'SE',
			'SK',
			'SI',
			'ES',
			'CZ',
			'HU',
			'GB',
			'CY'
		);
		$deliveryZone        = $GLOBALS['order']->delivery['country']['iso_code_2'];

		if(in_array($deliveryZone, $euCountriesIsoCodes) && $deliveryZone !== $storeCountryIsoCode
		   && $storeCountryIsoCode !== $customerCountryIsoCode)
		{
			return true;
		}

		return false;
	}


	/**
	 * @return array
	 */
	public function keys()
	{
		return array('configuration/MODULE_ORDER_TOTAL_TAX_STATUS', 'configuration/MODULE_ORDER_TOTAL_TAX_SORT_ORDER');
	}


	public function install()
	{
		xtc_db_query("INSERT INTO `gx_configurations`
					  (`key`, `value`, `sort_order`, `type`)
		              VALUES ('configuration/MODULE_ORDER_TOTAL_TAX_STATUS', 'true', '1','switcher')");

		xtc_db_query("INSERT INTO `gx_configurations`
					  (`key`, `value`, `sort_order`)
		              VALUES ('configuration/MODULE_ORDER_TOTAL_TAX_SORT_ORDER', '97', '2')");
	}


	public function remove()
	{
		xtc_db_query("DELETE FROM `gx_configurations`
					  WHERE `key`
					  IN ('" . implode("', '", $this->keys()) . "')");
	}
}

MainFactory::load_origin_class('ot_tax');
