<?php
/* --------------------------------------------------------------
   ShippingAndPaymentMatrixAssistentSource.inc.php 2023-01-25 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShippingAndPaymentMatrixAssistentSource
{
	public function save_shipping_and_payment_matrix(array $p_shipping_info_array, array $p_payment_info_array, array $p_shipping_time_array)
	{
		foreach($p_shipping_info_array as $t_language_id => $t_country_array)
		{
			foreach($t_country_array as $t_country_code => $t_shipping_info)
			{
				$t_sql = 'REPLACE INTO `shipping_and_payment_matrix` (country_code, language_id, shipping_info, payment_info, shipping_time) VALUES("' . xtc_db_input(xtc_db_prepare_input($t_country_code)) . '", "' . (int)$t_language_id . '", "' . xtc_db_input(xtc_db_prepare_input($t_shipping_info)) . '", "' . xtc_db_input(xtc_db_prepare_input($p_payment_info_array[$t_language_id][$t_country_code])) . '", "' . xtc_db_input(xtc_db_prepare_input($p_shipping_time_array[$t_language_id][$t_country_code])) . '")';
				xtc_db_query($t_sql);
			}
		}
	}
	
	public function delete_matrix()
	{
		$t_sql = 'TRUNCATE shipping_and_payment_matrix';
		xtc_db_query($t_sql);
	}
}