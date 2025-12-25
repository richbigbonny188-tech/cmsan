<?php
/* --------------------------------------------------------------
   OrderReadServiceInterface.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderReadServiceInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderReadServiceInterface
{
    /**
     * Returns an order, depending on the provided order ID.
     *
     * @param IdType $orderId Order ID.
     *
     * @return Order Found order.
     */
    public function getOrderById(IdType $orderId);
    
    
    /**
     * Returns a stored order item, depending on the provided order item ID.
     *
     * @param IdType $orderItemId Order item ID.
     *
     * @return StoredOrderItemInterface Found stored order item interface.
     */
    public function getOrderItemById(IdType $orderItemId);
    
    
    /**
     * Returns an OrderListItemCollection depending on the provided arguments.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderList(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * @param \OrderSearchCondition $searchCondition
     * @param \Pager|null           $pager   (Optional) Pager object with pagination information
     * @param array                 $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return mixed
     */
    public function searchOrders(OrderSearchCondition $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
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
    public function filterOrderList(array $filterParameters, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Get the filtered orders count.
     *
     * @param array $filterParameters
     *
     * @return int
     *
     * @throws BadMethodCallException
     */
    public function filterOrderListCount(array $filterParameters);
    
    
    /**
     * Returns an OrderListItemCollection depending on the provided customer ID.
     *
     * @param IdType      $customerId Customer ID
     * @param \Pager|null $pager      (Optional) Pager object with pagination information
     * @param array       $sorters    (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderListByCustomerId(IdType $customerId, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns an OrderListItemCollection depending on the provided order status ID.
     *
     * @param IntType     $orderStatusId Order status ID
     * @param \Pager|null $pager         (Optional) Pager object with pagination information
     * @param array       $sorters       (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderListByOrderStatusId(IntType $orderStatusId, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Filter the order list by a string keyword.
     *
     * @param StringType  $keyword Keyword to be used for searching the order list items.
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderListByKeyword(StringType $keyword, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Get Count of orders filtered by keyword
     *
     * @param StringType $keyword Keyword to be used for searching in orders list items.
     *
     * @return int
     */
    public function getOrderListByKeywordCount(StringType $keyword);
    
    
    /**
     * Get the total count of all orders
     *
     * @return int
     */
    public function getOrderListCount();
}
