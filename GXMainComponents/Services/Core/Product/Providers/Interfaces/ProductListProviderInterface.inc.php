<?php
/* --------------------------------------------------------------
   ProductListProviderInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductListProviderInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductListProviderInterface
{
    /**
     * Returns a product list item collection by the provided category ID.
     *
     * @param IdType $categoryId Category ID.
     *
     * @return ProductListItemCollection
     */
    public function getByCategoryId(IdType $categoryId);
    
    
    /**
     * Returns all product list items.
     *
     * @return ProductListItemCollection
     */
    public function getAll();
    
    
    /**
     * Returns a paged list of product items.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ProductListItemCollection
     */
    public function getAllPaged(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * Filters products records by a given ProductSearchCondition object and returns an array with results.
     *
     * @param \ProductSearchCondition $condition Conditions object for search.
     * @param \Pager|null             $pager     (Optional) Pager object with pagination information
     * @param array                   $sorters   (Optional) array of Sorter objects with data sorting information
     *
     * @return \ProductListItemCollection
     */
    public function searchProducts(ProductSearchCondition $condition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Count the total of filtered products.
     *
     * @param \ProductSearchCondition $condition Conditions object for search.
     *
     * @return \IntType
     */
    public function searchProductsCount(ProductSearchCondition $condition);
}