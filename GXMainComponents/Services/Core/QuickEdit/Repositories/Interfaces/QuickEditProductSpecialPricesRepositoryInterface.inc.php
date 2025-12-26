<?php

/* --------------------------------------------------------------
   QuickEditProductSpecialPricesRepositoryInterface.inc.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditProductSpecialPricesRepositoryInterface
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Interfaces
 */
interface QuickEditProductSpecialPricesRepositoryInterface
{
    /**
     * Returns the special prices of the indicated products.
     *
     * @param array $productIds       Array containing the product IDs to be processed.
     * @param array $filterParameters Contains filter parameters.
     *
     * @return array Returns the query result as a pure array, or an empty array when no result is produced.
     */
    public function getFilteredSpecialPrices(array $productIds, array $filterParameters);
    
    
    /**
     * Returns the record number of the filtered special prices.
     *
     * @param array $productIds       Array containing the product IDs to be processed.
     * @param array $filterParameters Contains filter parameters.
     *
     * @return int Returns the number of special prices found.
     */
    public function getFilteredSpecialPricesCount(array $productIds, array $filterParameters);
    
    
    /**
     * Get special prices record count.
     *
     * @return int Returns the number of all special prices found.
     */
    public function getSpecialPricesCount();
    
    
    /**
     * Saves the changed data regarding the special price.
     *
     * @param array $specialPrice Contains the special prices data.
     *
     * @return bool Returns the operation result.
     */
    public function setSpecialPriceById(array $specialPrice);
    
    
    /**
     * Sets the starting point of the pagination and the number of special prices.
     *
     * @param IntType|null $start  Pagination start index.
     * @param IntType|null $length Page length value.
     *
     * @return QuickEditProductSpecialPricesRepository QuickEdit products special price repository for chained method
     *                                                 calls.
     */
    public function paginateSpecialPrices(IntType $start = null, IntType $length = null);
    
    
    /**
     * Sets the sorting order of the special prices.
     *
     * @param StringType|null $orderBy Sorting order (ASC or DESC)
     *
     * @return QuickEditProductSpecialPricesRepository QuickEdit products special price repository for chained method
     *                                                 calls.
     */
    public function sortSpecialPrices(StringType $orderBy = null);
}