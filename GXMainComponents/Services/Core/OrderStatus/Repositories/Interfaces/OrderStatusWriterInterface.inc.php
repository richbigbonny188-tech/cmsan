<?php

/* --------------------------------------------------------------
   OrderStatusWriterInterface.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusWriterInterface
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Repositories
 */
interface OrderStatusWriterInterface
{
    /**
     * Creates a new order status.
     *
     * @param \OrderStatus $orderStatus Order status to be created.
     *
     * @return int Id of new order status entity.
     */
    public function createOrderStatus(OrderStatus $orderStatus);
    
    
    /**
     * Updates the given order status entity.
     *
     * @param \OrderStatus $orderStatus Order status to be update.
     *
     * @return $this|OrderStatusWriterInterface Same instance for chained method calls.
     */
    public function updateOrderStatus(OrderStatus $orderStatus);
}