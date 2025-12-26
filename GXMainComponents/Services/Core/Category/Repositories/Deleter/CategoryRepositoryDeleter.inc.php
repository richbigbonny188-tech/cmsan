<?php

/* --------------------------------------------------------------
   CategoryRepositoryDeleter.inc.php 2017-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategoryRepositoryDeleter
 *
 * This class deletes category records from the database and is used in the category repository among the classes for
 * writing and reading category records.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategoryRepositoryDeleter implements CategoryRepositoryDeleterInterface
{
    /**
     * Database connector.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var \ProductRepositoryInterface
     */
    protected $productRepository;
    
    
    /**
     * CategoryRepositoryDeleter constructor.
     *
     * @param CI_DB_query_builder        $db                Database connector.
     * @param ProductRepositoryInterface $productRepository Products repository with functionality to remove products.
     */
    public function __construct(CI_DB_query_builder $db, ProductRepositoryInterface $productRepository)
    {
        $this->db                = $db;
        $this->productRepository = $productRepository;
    }
    
    
    /**
     * Deletes a specific category entity.
     *
     * @param IdType $categoryId Category ID.
     *
     * @return CategoryRepositoryDeleter Same instance for chained method calls.
     */
    public function deleteById(IdType $categoryId)
    {
        $this->db->delete(['categories', 'categories_description', 'products_to_categories'],
                          ['categories_id' => $categoryId->asInt()]);
        
        return $this;
    }
    
    
    /**
     * Deletes related products of given category.
     * All products that are only connected to the given category gets removed.
     *
     * @param \IdCollection $categoryIds Ids of categories with products to be removed.
     *
     * @return $this|CategoryRepositoryDeleterInterface Same instance for chained method calls.
     */
    public function deleteRelatedProductsOfCategory(IdCollection $categoryIds)
    {
        $productIdsToRemove = [];
        $products           = [];
        foreach ($categoryIds->getArray() as $categoryId) {
            $productsInCategory = $this->db->select('products_id')
                ->from('products_to_categories')
                ->where('categories_id',
                        $categoryId->asInt())
                ->get()
                ->result_array();
            
            foreach ($productsInCategory as $productInCategory) {
                $products[$productInCategory['products_id']]['categories'][] = $categoryId->asInt();
            }
        }
        
        foreach ($products as $productId => $product) {
            $productsTotal = $this->db->select('COUNT(*) as total')
                                 ->from('products_to_categories')
                                 ->where('products_id',
                                         $productId)
                                 ->where_not_in('categories_id', $product['categories'])
                                 ->get()
                                 ->row_array()['total'];
            
            if ((int)$productsTotal === 0) {
                $productIdsToRemove[] = $productId;
            }
        }
        
        foreach ($productIdsToRemove as $productId) {
            $this->productRepository->deleteProductById(new IdType($productId));
        }
        
        return $this;
    }
}