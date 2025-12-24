<?php
/* --------------------------------------------------------------
   get_transfer_charge_text.inc.php 2017-01-04
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function get_transfer_charge_text($shippingModuleName, $language_code, $customer_status_id, $currency_code)
{
	if(!defined('MODULE_ORDER_TOTAL_COD_FEE_TRANSFER_CHARGE'))
	{
		return '';
	}
	
	if(is_numeric(strpos($shippingModuleName, '_')))
	{
		$shippingModuleName = substr($shippingModuleName, strpos($shippingModuleName, '_') + 1);
	}
	
	$unsorted_transfer_charge_string = explode('|', MODULE_ORDER_TOTAL_COD_FEE_TRANSFER_CHARGE);
	$transfer_charge_array           = array();
	$iMax                            = count($unsorted_transfer_charge_string);
	
	for($i = 1; $i < $iMax; $i++)
	{
		$shipping_values = explode(',', $unsorted_transfer_charge_string[$i]);
		
		foreach($shipping_values as $value)
		{
			$value_array                                                                      = explode(':', $value);
			$transfer_charge_array[$unsorted_transfer_charge_string[$i - 1]][$value_array[0]] = $value_array[1];
		}
		
		$i++;
	}
	
	$coo_lang_file_master = MainFactory::create_object('LanguageTextManager', array(), true);
	$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language']
	                                           . '/modules/order_total/ot_cod_fee.lang.inc.php');
	
	$price = 0;
	
	if(array_key_exists($shippingModuleName, $transfer_charge_array))
	{
		if(array_key_exists($language_code, $transfer_charge_array[$shippingModuleName]))
		{
			$price = (float)$transfer_charge_array[$shippingModuleName][$language_code];
		}
		elseif(array_key_exists('00', $transfer_charge_array[$shippingModuleName]))
		{
			$price = (float)$transfer_charge_array[$shippingModuleName]['00'];
		}
	}
	
	if($price > 0)
	{
		$coo_xtcPrice = new xtcPrice($currency_code, $customer_status_id);
		$price        = $coo_xtcPrice->xtcFormat($price, true);

		return sprintf(MODULE_ORDER_TOTAL_COD_FEE_TRANSFER_CHARGE_INFORMATION, $price);
	}
	
	return '';
}