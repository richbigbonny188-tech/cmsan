<?php

/* --------------------------------------------------------------
   GambioHubOrderRepositoryWriter.inc.php 2019-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GambioHubOrderRepositoryWriter
 *
 * Enables the output of the payment title and module of a gambio hub order.
 */
class GambioHubOrderRepositoryWriter extends GambioHubOrderRepositoryWriter_parent
{
	/**
	 * Record the order update.
	 *
	 * @param \OrderInterface $order Order instance.
	 */
	public function update(OrderInterface $order)
	{
		parent::update($order);
		
		// Record the database change only if this is a Hub order.
		if(preg_match('/^.*Hub$/', $order->getPaymentType()->getModule()))
		{
			$rowId = $order->getOrderId();
			DataObserverRegistry::updated('order', $rowId);
		}
	}
	
	
	/**
	 * Reset the "payment_method" & "payment_class" fields to "gambio_hub" in order to maintain data integrity.
	 *
	 * @param \OrderInterface $order Order instance.
	 *
	 * @return array
	 */
	protected function _serializeOrder(OrderInterface $order)
	{
		$record = parent::_serializeOrder($order);
		
		if(preg_match('/^.*Hub$/', $order->getPaymentType()->getModule()))
		{
			$record['payment_method'] = 'gambio_hub';
			$record['payment_class']  = 'gambio_hub';
		}
		
		return $record;
	}
}
