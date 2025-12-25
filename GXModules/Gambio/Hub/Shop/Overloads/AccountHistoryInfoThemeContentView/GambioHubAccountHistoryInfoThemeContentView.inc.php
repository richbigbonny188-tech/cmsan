<?php
/* --------------------------------------------------------------
   GambioHubAccountHistoryInfoThemeContentView.inc.php 2017-02-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubAccountHistoryInfoThemeContentView
 */
class GambioHubAccountHistoryInfoThemeContentView extends GambioHubAccountHistoryInfoThemeContentView_parent
{
	protected function _assignPaymentData()
	{
		parent::_assignPaymentData();
		
		if($this->order->info['payment_method'] === 'gambio_hub')
		{
			$query = 'SELECT `gambio_hub_module_title` FROM `orders` WHERE `orders_id` = ' . (int) $this->orderId;
			$result = xtc_db_query($query);
			
			$this->set_content_data('PAYMENT_METHOD', xtc_db_fetch_array($result)['gambio_hub_module_title']);
		}
	}
}
