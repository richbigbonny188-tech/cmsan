<?php
/* --------------------------------------------------------------
   GambioHubInvoicePackingslipExtender.inc.php 2018-03-02
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubInvoicePackingslipExtender
 */
class GambioHubInvoicePackingslipExtender extends GambioHubInvoicePackingslipExtender_parent
{
	/**
	 * @param array $order_info
	 *
	 * @return array
	 */
	public function extendOrderInfo($order_info)
	{
		$order_info = parent::extendOrderInfo($order_info);
		
		if(!empty($order_info['PAYMENT_METHOD'][0]))
		{
			$query  = 'SELECT `gambio_hub_module_title` 
						FROM `orders` 
						WHERE 
							`orders_id` = ' . (int)$this->v_data_array['order_id'] . ' AND 
							`payment_class` = "gambio_hub"';
			$result = xtc_db_query($query);
			
			if(xtc_db_num_rows($result))
			{
				$order_info['PAYMENT_METHOD'][1] = xtc_db_fetch_array($result)['gambio_hub_module_title'];
			}
		}
		
		return $order_info;
	}
}