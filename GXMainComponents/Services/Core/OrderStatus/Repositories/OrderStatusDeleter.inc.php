<?php

/* --------------------------------------------------------------
   OrderStatusDeleter.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class OrderStatusDeleter
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Repositories
 */
class OrderStatusDeleter implements OrderStatusDeleterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected $table = 'orders_status';
    
    /**
     * @var string
     */
    protected $id = 'orders_status_id';
    
    
    /**
     * OrderStatusDeleter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder Active record instance for data access.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Removes order status by the given order status id.
     *
     * @param \IntType $orderStatusId Order status id to be removed.
     *
     * @return $this|OrderStatusWriterInterface Same instance for chained method calls.
     */
    public function removeOrderStatus(IntType $orderStatusId)
    {
        $this->queryBuilder->delete($this->table, [$this->id => $orderStatusId->asInt()]);
        
        return $this;
    }
}