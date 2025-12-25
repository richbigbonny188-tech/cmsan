<?php

/* --------------------------------------------------------------
   OrderStatusRepositoryInterface.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusRepositoryInterface
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Repositories
 */
interface OrderStatusRepositoryInterface
{
    /**
     * Returns a collection with all order status.
     *
     * @return OrderStatusCollection Collection of all order status resources.
     */
    public function findAll();
    
    
    /**
     * Returns an order status entity by the given order status id. If no data was found, null is returned.
     *
     * @param \IntType $orderStatusId Id of expected OrderStatus entity.
     *
     * @return OrderStatus|null Whether the found order status entity or null.
     */
    public function find(IntType $orderStatusId);
    
    
    /**
     * Returns an order status entity by the given order status id. If no data was found, an exception is thrown.
     *
     * @param \IntType $orderStatusId Id of expected OrderStatus entity.
     *
     * @return OrderStatus Expected order status entity.
     * @throws OrderStatusNotFoundException If expected order status entity was not found by the given id.
     */
    public function get(IntType $orderStatusId);
    
    
    /**
     * Creates a new order status.
     *
     * @param \OrderStatus $orderStatus Order status entity with new data.
     *
     * @return int Id of new order status entity.
     */
    public function create(OrderStatus $orderStatus);
    
    
    /**
     * Updates an order status.
     *
     * @param \OrderStatus $orderStatus Order status entity with updated data.
     *
     * @return $this|OrderStatusRepositoryInterface Same instance for chained method calls.
     */
    public function update(OrderStatus $orderStatus);
    
    
    /**
     * Removes an order status.
     *
     * @param \IntType $orderStatusId Id of order status entity to be removed.
     *
     * @return $this|OrderStatusRepositoryInterface Same instance for chained method calls.
     */
    public function remove(IntType $orderStatusId);
}