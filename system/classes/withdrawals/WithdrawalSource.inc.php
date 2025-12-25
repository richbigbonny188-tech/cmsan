<?php
/* --------------------------------------------------------------
   WithdrawalSource.inc.php 2020-01-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

class WithdrawalSource
{
	/**
	 * @param $p_order_hash
	 *
	 * @return bool|Order
	 */
	public function get_order_by_hash($p_order_hash)
    {
        $t_query = 'SELECT
                    	orders_id
                  	FROM
                  		orders
                  	WHERE
                  		orders_hash = "' . xtc_db_input($p_order_hash) .'";';

        $t_result = xtc_db_query($t_query);

		if(xtc_db_num_rows($t_result) === 1)
		{
			$t_row = xtc_db_fetch_array($t_result);

			require_once DIR_FS_CATALOG . 'includes/classes/order.php';
			$coo_order = new order($t_row['orders_id']);

			return $coo_order;
		}

		return false;
	}
}