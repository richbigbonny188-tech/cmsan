<?php

/* --------------------------------------------------------------
   QuickEditProductPropertiesRepositoryReaderInterface.inc.php 2017-03-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuickEditProductsPropertiesReaderInterface
 *
 * @category   System
 * @package    QuickEdit
 * @subpackage Interfaces
 */
interface QuickEditProductPropertiesReaderInterface
{
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
     * Sets the starting point of the pagination and the number of products.
     *
     * @param IntType|null $start  Starting point.
     * @param IntType|null $length Number of products.
     *
     * @return QuickEditProductPropertiesReaderInterface Returns same instance for chained method calls.
     */
    public function paginateProperties(IntType $start = null, IntType $length = null);
    
    
    /**
     * Sets the sorting order of the products
     *
     * @param StringType|null $orderBy Sorting order (ASC or DESC)
     *
     * @return QuickEditProductPropertiesReaderInterface Returns same instance for chained method calls.
     */
    public function sortProperties(StringType $orderBy = null);
}