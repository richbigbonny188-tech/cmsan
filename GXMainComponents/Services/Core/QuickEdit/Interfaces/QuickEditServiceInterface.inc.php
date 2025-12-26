<?php

/* --------------------------------------------------------------
   QuickEditServiceInterface.inc.php 2017-03-09
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditServiceInterface
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Interfaces
 */
interface QuickEditServiceInterface
{
    /**
     * Returns the graduated prices of a product or an empty array nothing was found.
     *
     * @param array|null $productIds Array containing the selected product IDs to be processed.
     *
     * @return array Returns array that contains the graduated prices information.
     */
    public function getGraduatedPrices(array $productIds = null);
    
    
    /**
     * Returns filtered product properties based on the provided filter criteria.
     *
     * @param array $productIds       Array containing the selected product IDs to be processed.
     * @param array $filterParameters Contains the filter parameters.
     *
     * @return array Returns the query result as a pure array, or an empty array when no result is produced.
     */
    public function getFilteredProductProperties(array $productIds, array $filterParameters);
    
    
    /**
     * Returns products that are subject to the specified filter criteria.
     *
     * @param array $productIds       Array containing the selected product IDs to be processed.
     * @param array $filterParameters Contains the filter parameters.
     *
     * @return int Returns the number of product properties found.
     */
    public function getFilteredProductPropertiesCount(array $productIds, array $filterParameters);
    
    
    /**
     * Returns the number of all product properties found.
     *
     * @return int Returns the record number.
     */
    public function getProductPropertiesCount();
    
    
    /**
     * Saves product by product-combi ID.
     *
     * @param array $productCombi Contains product data to be saved.
     *
     * @return bool Returns the operation result.
     */
    public function setByCombisId(array $productCombi);
    
    
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
     * Sets the starting point of the pagination and the number of properties.
     *
     * @param IntType|null $start  Pagination start index.
     * @param IntType|null $length Page length value.
     *
     * @return QuickEditProductPropertiesRepository QuickEdit products properties repository for chained method calls.
     */
    public function paginateProperties(IntType $start = null, IntType $length = null);
    
    
    /**
     * Sets the sorting order of the product properties.
     *
     * @param StringType|null $orderBy Sorting order (ASC or DESC).
     *
     * @return QuickEditProductPropertiesRepository QuickEdit products properties repository for chained method calls.
     */
    public function sortProperties(StringType $orderBy = null);
    
    
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
    
    
    /**
     * Saves the changed data regarding the special price.
     *
     * @param array $specialPrice Contains the special prices data.
     *
     * @return bool Returns the operation result.
     */
    public function setSpecialPriceById(array $specialPrice);
}