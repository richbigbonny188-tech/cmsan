<?php
/* --------------------------------------------------------------
   OrderReadService.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderReadServiceInterface');

/**
 * Class OrderReadService
 *
 * @category System
 * @package  Order
 */
class OrderReadService implements OrderReadServiceInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
    
    /**
     * @var OrderItemRepositoryInterface
     */
    protected $orderItemRepository;
    
    /**
     * @var OrderListGenerator
     */
    protected $orderListGenerator;
    
    
    /**
     * OrderReadService Constructor
     *
     * @param OrderRepositoryInterface     $orderRepository
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param OrderListGeneratorInterface  $orderListGenerator
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderItemRepositoryInterface $orderItemRepository,
        OrderListGeneratorInterface $orderListGenerator
    ) {
        $this->orderRepository     = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderListGenerator  = $orderListGenerator;
    }
    
    
    /**
     * Get Order by ID
     *
     * Returns an order, depending on the provided order ID.
     *
     * @param IdType $orderId Order ID of the wanted order
     *
     * @return OrderInterface
     */
    public function getOrderById(IdType $orderId)
    {
        return $this->orderRepository->getById($orderId);
    }
    
    
    /**
     * Get a stored order item by ID.
     *
     * Returns a stored order item, depending on the provided order item ID.
     *
     * @param IdType $orderItemId
     *
     * @return StoredOrderItemInterface
     */
    public function getOrderItemById(IdType $orderItemId)
    {
        return $this->orderItemRepository->getItemById($orderItemId);
    }
    
    
    /**
     * Get Order List
     *
     * Returns an OrderListItemCollection depending on the provided arguments.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderList(\Pager $pager = null, array $sorters = [])
    {
        return $this->orderListGenerator->getOrderListByConditions([], $pager, $sorters);
    }
    
    
    /**
     * @param \OrderSearchCondition $searchCondition
     * @param \Pager|null           $pager   (Optional) Pager object with pagination information
     * @param array                 $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return mixed
     */
    public function searchOrders(OrderSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        return $this->orderListGenerator->getOrderListByConditions($searchCondition->buildSql(), $pager, $sorters);
    }
    
    
    /**
     * Filter the order records with specific conditions.
     *
     * Provide the filtering values in the conditions array in order to fetch a filtered result set.
     *
     * @param array       $filterParameters Contains an array of the GET parameters to be used for filtering the order
     *                                      records.
     * @param \Pager|null $pager            (Optional) Pager object with pagination information
     * @param array       $sorters          (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection
     */
    public function filterOrderList(array $filterParameters, \Pager $pager = null, array $sorters = [])
    {
        return $this->orderListGenerator->filterOrderList($filterParameters, $pager, $sorters);
    }
    
    
    /**
     * Get the filtered orders count.
     *
     * @param array $filterParameters
     *
     * @return int
     *
     * @throws BadMethodCallException
     */
    public function filterOrderListCount(array $filterParameters)
    {
        return $this->orderListGenerator->filterOrderListCount($filterParameters);
    }
    
    
    /**
     * Returns an OrderListItemCollection depending on the provided customer ID.
     *
     * @param IdType      $customerId Customer ID
     * @param \Pager|null $pager      (Optional) Pager object with pagination information
     * @param array       $sorters    (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderListByCustomerId(IdType $customerId, \Pager $pager = null, array $sorters = [])
    {
        return $this->orderListGenerator->getOrderListByConditions(['orders.customers_id' => $customerId->asInt()],
                                                                   $pager,
                                                                   $sorters);
    }
    
    
    /**
     * Returns an OrderListItemCollection depending on the provided order status ID.
     *
     * @param IntType     $orderStatusId Order status ID
     * @param \Pager|null $pager         (Optional) Pager object with pagination information
     * @param array       $sorters       (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderListByOrderStatusId(IntType $orderStatusId, \Pager $pager = null, array $sorters = [])
    {
        return $this->orderListGenerator->getOrderListByConditions(['orders.orders_status' => $orderStatusId->asInt()],
                                                                   $pager,
                                                                   $sorters);
    }
    
    
    /**
     * Filter the order list by a string keyword.
     *
     * @param StringType  $keyword Keyword to be used for searching the order list items.
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderListByKeyword(StringType $keyword, \Pager $pager = null, array $sorters = [])
    {
        return $this->orderListGenerator->getOrderListByKeyword($keyword, $pager, $sorters);
    }
    
    
    /**
     * Get Count of orders filtered by keyword
     *
     * @param StringType $keyword Keyword to be used for searching in orders list items.
     *
     * @return int
     */
    public function getOrderListByKeywordCount(StringType $keyword)
    {
        return $this->orderListGenerator->getOrderListByKeywordCount($keyword);
    }
    
    
    /**
     * Get the total count of all orders
     *
     * @return int
     */
    public function getOrderListCount()
    {
        return $this->orderListGenerator->getOrderListCount();
    }
}
