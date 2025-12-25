<?php

/* --------------------------------------------------------------
   OrderStatusService.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderStatusService
 *
 * @category   System
 * @package    OrderStatus
 */
class OrderStatusService implements OrderStatusServiceInterface
{
    /**
     * @var OrderStatusRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * Class Constructor
     *
     * @param OrderStatusRepositoryInterface                     orderStatusRepository
     */
    public function __construct(OrderStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns a collection with all order status.
     *
     * @return OrderStatusCollection Collection of all order status resources.
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }
    
    
    /**
     * Returns an order status entity by the given order status id. If no data was found, null is returned.
     *
     * @param \IntType $orderStatusId Id of expected OrderStatus entity.
     *
     * @return OrderStatus|null Whether the found order status entity or null.
     */
    public function find(IntType $orderStatusId)
    {
        return $this->repository->find($orderStatusId);
    }
    
    
    /**
     * Returns an order status entity by the given order status id. If no data was found, an exception is thrown.
     *
     * @param \IntType $orderStatusId Id of expected OrderStatus entity.
     *
     * @return OrderStatus Expected order status entity.
     * @throws OrderStatusNotFoundException If expected order status entity was not found by the given id.
     */
    public function get(IntType $orderStatusId)
    {
        return $this->repository->get($orderStatusId);
    }
    
    
    /**
     * Creates a new order status.
     *
     * @param \OrderStatus $orderStatus Order status entity with new data.
     *
     * @return int Id of new order status entity.
     */
    public function create(OrderStatus $orderStatus)
    {
        return $this->repository->create($orderStatus);
    }
    
    
    /**
     * Updates an order status.
     *
     * @param \OrderStatus $orderStatus Order status entity with updated data.
     *
     * @return $this|OrderStatusServiceInterface Same instance for chained method calls.
     */
    public function update(OrderStatus $orderStatus)
    {
        $this->repository->update($orderStatus);
        
        return $this;
    }
    
    
    /**
     * Removes an order status.
     *
     * @param \IntType $orderStatusId Id of order status entity to be removed.
     *
     * @return $this|OrderStatusServiceInterface Same instance for chained method calls.
     */
    public function remove(IntType $orderStatusId)
    {
        $this->repository->remove($orderStatusId);
        
        return $this;
    }
}