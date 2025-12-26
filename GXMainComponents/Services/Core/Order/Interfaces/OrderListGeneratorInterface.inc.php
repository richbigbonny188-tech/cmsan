<?php

/* --------------------------------------------------------------
   OrderListGeneratorInterface.inc.php 2017-03-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderListGeneratorInterface
 *
 * @category   System
 * @package    Order
 * @subpackage Interfaces
 */
interface OrderListGeneratorInterface
{
    /**
     * Get Order List Items
     *
     * Returns an order list item collection.
     *
     * @param string|array $conditions Provide a WHERE clause string or an associative array (actually any parameter
     *                                 that is acceptable by the "where" method of the CI_DB_query_builder method).
     * @param \Pager|null  $pager      (Optional) Pager object with pagination information
     * @param array        $sorters    (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection
     *
     * @throws InvalidArgumentException If the result rows contain invalid values.
     */
    public function getOrderListByConditions($conditions = [], \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Filters records by a single keyword string.
     *
     * @param StringType  $keyword Keyword string to be used for searching in order records.
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection Order list item collection.
     */
    public function getOrderListByKeyword(StringType $keyword, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Get count of orders filtered by keyword
     *
     * @param StringType $keyword Keyword string to be used for searching in order records.
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
    
    
    /**
     * Filter order list items by the provided parameters.
     *
     * The following slug names need to be used:
     *
     *   - number => orders.orders_id
     *   - customer => orders.customers_lastname orders.customers_firstname
     *   - group => orders.customers_status_name
     *   - sum => orders_total.value
     *   - payment => orders.payment_method
     *   - shipping => orders.shipping_method
     *   - countryIsoCode => orders.delivery_country_iso_code_2
     *   - date => orders.date_purchased
     *   - status => orders_status.orders_status_name
     *   - invoiceNumber => invoices.invoice_number
     *
     * @param array       $filterParameters Contains the column slug-names and their values.
     * @param \Pager|null $pager            (Optional) Pager object with pagination information
     * @param array       $sorters          (Optional) array of Sorter objects with data sorting information
     *
     * @return OrderListItemCollection
     */
    public function filterOrderList(array $filterParameters, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Get the filtered orders count.
     *
     * This number is useful for pagination functionality where the app needs to know the number of the filtered rows.
     *
     * @param array $filterParameters
     *
     * @return int
     *
     * @throws BadMethodCallException
     */
    public function filterOrderListCount(array $filterParameters);
}
