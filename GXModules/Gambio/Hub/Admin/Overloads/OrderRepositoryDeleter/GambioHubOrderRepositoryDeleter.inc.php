<?php

/* --------------------------------------------------------------
   GambioHubOrderRepositoryDeleter.inc.php 2019-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubOrderRepositoryDeleter
 *
 * Records a Hub order removal, which will finally be delegated to Hub.
 */
class GambioHubOrderRepositoryDeleter extends GambioHubOrderRepositoryDeleter_parent
{
	/**
	 * Record the order removal.
	 *
	 * @param \IdType $orderId Order ID.
	 */
	public function deleteById(IdType $orderId)
	{
		$queryBuilder  = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$rowId         = $orderId->asInt();
		$paymentMethod = $queryBuilder->get_where('orders', ['orders_id' => $rowId])->row()->payment_method;
		
		parent::deleteById($orderId);
		
		// Record the database change only if this is a Hub order.
		if($paymentMethod === 'gambio_hub')
		{
			DataObserverRegistry::deleted('order', $rowId);
		}
	}
}
