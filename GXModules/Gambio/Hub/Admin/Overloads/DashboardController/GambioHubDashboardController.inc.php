<?php
/* --------------------------------------------------------------
   GambioHubDashboardController.inc.php 2017-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubDashboardController
 */
class GambioHubDashboardController extends GambioHubDashboardController_parent
{
	/**
	 * Returns the latest orders.
	 */
	public function actionGetLatestOrders()
	{
		/** @var JsonHttpControllerResponse $response */
		$response = parent::actionGetLatestOrders();
		
		$db     = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$orders = json_decode($response->getBody(), true);
		
		foreach($orders['data'] as &$order)
		{
			$db->select('gambio_hub_module_title')->from('orders')->where(['orders.orders_id' => $order['orders_id']]);
			
			$title = $db->get()->result_array();
			
			if($title[0]['gambio_hub_module_title'] !== '')
			{
				$order['payment_method'] = $title[0]['gambio_hub_module_title'];
			}
		}
		
		return MainFactory::create('JsonHttpControllerResponse', $orders, $response->getHeaders());
	}
}