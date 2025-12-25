<?php
/* --------------------------------------------------------------
   ProductReadServiceInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductReadServiceInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
Interface ProductReadServiceInterface
{
    /**
     * Get Product by ID.
     *
     * Returns a specific product, depending on the provided ID.
     *
     * @param IdType $productId The ID of the product to return.
     *
     * @return StoredProductInterface The stored product.
     */
    public function getProductById(IdType $productId);
    
    
    /**
     * Get Product List
     *
     * Returns a specific product list.
     *
     * @param LanguageCode $languageCode        The language code.
     * @param IdType|null  $categoryId          The category ID of the product list.
     * @param IdType|null  $customerStatusLimit The customers status limit.
     *
     * @return ProductListItemCollection
     */
    public function getProductList(
        LanguageCode $languageCode,
        IdType $categoryId = null,
        IdType $customerStatusLimit = null
    );
    
    
    /**
     * Returns a paged product list.
     *
     * @param \LanguageCode $languageCode Language of location specific data.
     * @param \Pager|null   $pager        (Optional) Pager object with pagination information
     * @param array         $sorters      (Optional) array of Sorter objects with data sorting information
     *
     * @return \ProductListItemCollection
     */
    public function getPagedList(LanguageCode $languageCode, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns the count of all product entries.
     *
     * @return int
     */
    public function getProductsCount();
    
    
    /**
     * Get Active Product List
     *
     * Returns an active products list.
     *
     * @param LanguageCode $languageCode        The language code.
     * @param IdType|null  $categoryId          The category ID of the product list.
     * @param IdType|null  $customerStatusLimit The customers status limit.
     *
     * @return ProductListItemCollection
     */
    public function getActiveProductList(
        LanguageCode $languageCode,
        IdType $categoryId = null,
        IdType $customerStatusLimit = null
    );
    
    
    /**
     * Returns the category Ids which are linked to the given product id.
     *
     * @param IdType $productId
     *
     * @return IdCollection
     */
    public function getProductLinks(IdType $productId);
    
    
    /**
     * Returns an UrlRewriteCollection with UrlRewrite instances for the provided content ID.
     *
     * @param IdType $productId
     *
     * @return UrlRewriteCollection
     */
    public function getRewriteUrls(IdType $productId);
    
    
    /**
     * Returns a single UrlRewrite instance for the provided content ID and language ID or NULL if no entry was found.
     *
     * @param IdType $productId
     * @param IdType $languageId
     *
     * @return null|UrlRewrite
     */
    public function findRewriteUrl(IdType $productId, IdType $languageId);
    
    
    /**
     * Returns an UrlRewriteCollection with UrlRewrite instances for the provided rewrite url.
     *
     * @param NonEmptyStringType $rewriteUrl
     *
     * @return UrlRewriteCollection
     */
    public function findUrlRewritesByRewriteUrl(NonEmptyStringType $rewriteUrl);
    
    
    /**
     * Filters products records by a given ProductSearchCondition object and returns an array with results.
     *
     * @param \LanguageCode           $languageCode Language code which should be used for products descriptions etc.
     * @param \ProductSearchCondition $condition    Conditions object for search.
     * @param \Pager|null             $pager        (Optional) Pager object with pagination information
     * @param array                   $sorters      (Optional) array of Sorter objects with data sorting information
     *
     * @return \ProductListItemCollection
     */
    public function searchProducts(
        LanguageCode $languageCode,
        ProductSearchCondition $condition,
        \Pager $pager = null,
        array $sorters = []
    );
}