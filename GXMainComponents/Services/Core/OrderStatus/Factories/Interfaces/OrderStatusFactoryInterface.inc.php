<?php

/* --------------------------------------------------------------
   OrderStatusFactoryInterface.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusFactoryInterface
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Factories
 */
interface OrderStatusFactoryInterface
{
    /**
     * Creates a new instance of order status.
     *
     * @param \IntType $orderStatusId (Optional) Id of order status entity to be created.
     *
     * @return \OrderStatus Order status entity.
     */
    public function createOrderStatus(IntType $orderStatusId = null);
    
    
    /**
     * Creates a new instance of an order status collection.
     *
     * @param OrderStatus[] $orderStatuses Array with all order status items of the created collection.
     *
     * @return OrderStatusCollection Collected order status entities.
     */
    public function createOrderStatusCollection(array $orderStatuses);
}