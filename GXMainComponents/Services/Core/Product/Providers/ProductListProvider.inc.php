<?php
/* --------------------------------------------------------------
   ProductListProvider.inc.php 2022-04-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductListProvider
 *
 * @category   System
 * @package    Product
 * @subpackage Providers
 */
class ProductListProvider extends AbstractDataPaginator implements ProductListProviderInterface
{
    
    /**
     * Maps the entity Fields with the database fields
     *
     * @var $fieldMap
     */
    public static $fieldMap = [
        'id'              => 'products.products_id',
        'isactive'        => 'products_status',
        'sortorder'       => 'products_sort',
        'dateadded'       => 'products_date_added',
        'dateavailable'   => 'products_date_available',
        'lastmodified'    => 'products_last_modified',
        'orderedcount'    => 'products_ordered',
        'productmodel'    => 'products_model',
        'ean'             => 'products_ean',
        'price'           => 'products_price',
        'discountallowed' => 'products_discount_allowed',
        'taxclassid'      => 'products_tax_class_id',
        'quantity'        => 'products_quantity',
        'name'            => 'products_name',
        'image'           => 'products_image',
        'imagealttext'    => 'gm_alt_text',
        'urlkeywords'     => 'products_meta_keywords',
        'weight'          => 'products_weight',
        'shippingcosts'   => 'nc_ultra_shipping_costs',
        'shippingtimeid'  => 'products_shippingtime',
        'producttypeid'   => 'product_type',
        'manufacturerid'  => 'manufacturers_id',
        'quantityunitid'  => 'quantity_unit_id',
        'isfsk18'         => 'products_fsk18',
        'isvpeactive'     => 'products_vpe_status',
        'vpeid'           => 'products_vpe',
        'vpevalue'        => 'products_vpe_value',
        'specialofferid'  => 'specials_id',
        'maincategoryid'  => 'main_category_id',
    ];
    
    
    /**
     * Two-letter language code.
     *
     * @var LanguageCode
     */
    protected $languageCode;
    
    /**
     * Database query conditions.
     *
     * @var array
     */
    protected $conditions;
    
    /**
     * Product repository.
     *
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    
    
    /**
     * ProductListProvider constructor.
     *
     * @param LanguageCode               $languageCode Two-letter language code.
     * @param ProductRepositoryInterface $productRepo  Product repository.
     * @param CI_DB_query_builder        $db           Database connection.
     * @param array                      $conditions   Database query conditions.
     */
    public function __construct(
        LanguageCode $languageCode,
        ProductRepositoryInterface $productRepo,
        CI_DB_query_builder $db,
        array $conditions = []
    ) {
        parent::__construct($db);
        $this->languageCode      = $languageCode;
        $this->conditions        = $conditions;
        $this->productRepository = $productRepo;
    }
    
    
    /**
     * Returns a product list item collection by the provided category ID.
     *
     * @param IdType $categoryId Category ID.
     *
     * @return ProductListItemCollection
     * @throws InvalidArgumentException if the provided category ID is not valid.
     *
     */
    public function getByCategoryId(IdType $categoryId)
    {
        $this->_selectWithCategories()->_applyExtraConditions();
        
        $this->db->where('products_to_categories.categories_id', $categoryId->asInt());
        
        $result = $this->db->get()->result_array();
        
        return $this->_prepareCollection($result);
    }
    
    
    /**
     * Get all product list items.
     *
     * @return ProductListItemCollection
     */
    public function getAll()
    {
        // Build select part of query.
        $result = $this->_select()->_applyExtraConditions()->_applySorting()->db->get()->result_array();
        
        return $this->_prepareCollection($result);
    }
    
    
    /**
     * Returns a paged list of product items.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ProductListItemCollection
     */
    public function getAllPaged(\Pager $pager = null, array $sorters = [])
    {
        $result = $this->_select()
            ->_applyExtraConditions()
            ->_applyPagination($pager)
            ->_applySorting($sorters)->db->get()->result_array();
        
        return $this->_prepareCollection($result);
    }
    
    
    /**
     * return the child class Field Map array.
     *
     * @return array.
     */
    
    protected function &_getFieldMap()
    {
        return self::$fieldMap;
    }
    
    
    /**
     * Applies the class default sorting
     */
    protected function _applyDefaultSorting()
    {
        $this->db->order_by('products.products_id', 'asc');
    }
    
    
    /**
     * Build the select part of the query build.
     *
     * @return ProductListProvider Same instance for chained method calls.
     */
    protected function _select()
    {
        // Build the database query.
        $this->db->select('products.*, products_description.*, products_quantity_unit.quantity_unit_id, specials.specials_id')
            ->from('products')
            ->join('products_description',
                   'products_description.products_id = products.products_id',
                   'left')
            ->join('products_quantity_unit', 'products_quantity_unit.products_id = products.products_id', 'left')
            ->join('languages', 'languages.languages_id = products_description.language_id', 'inner')
            ->join('specials', 'products.products_id = specials.products_id', 'left')
            ->where('languages.code', $this->languageCode->asString());
        
        return $this;
    }
    
    
    /**
     * Build the select part of the query build and additionally join the products_to_categories table.
     *
     * @return ProductListProvider Same instance for chained method calls.
     */
    protected function _selectWithCategories()
    {
        // Build the database query.
        $this->_select()->db->join('products_to_categories',
                                   'products_to_categories.products_id = products.products_id',
                                   'left');
        
        return $this;
    }
    
    
    /**
     * Apply extra query conditions.
     *
     * @return ProductListProvider Same instance for chained method calls.
     */
    protected function _applyExtraConditions()
    {
        // Check for additional conditions to be appended to query (the AND operator will be used).
        if (count($this->conditions) > 0) {
            $this->db->where($this->conditions);
        }
        
        return $this;
    }
    
    
    /**
     * Prepares the ProductListItemCollection object.
     *
     * @param array $result Query result.
     *
     * @return ProductListItemCollection
     * @throws InvalidArgumentException if the provided result is not valid.
     *
     */
    protected function _prepareCollection(array $result)
    {
        $listItems = [];
        
        // Iterate over each query result row and create a ProductListItem for each row which will be pushed
        // into $listItems array.
        foreach ($result as $row) {
            $productId            = new IdType((int)$row['products_id']);
            $isActive             = new BoolType((bool)$row['products_status']);
            $sortOrder            = new IntType((int)$row['products_sort']);
            $addedDateTime        = new EmptyDateTime($row['products_date_added']);
            $availableDateTime    = new EmptyDateTime($row['products_date_available']);
            $lastModifiedDateTime = new EmptyDateTime($row['products_last_modified']);
            $orderedCount         = new IntType((int)$row['products_ordered']);
            $productModel         = new StringType((string)$row['products_model']);
            $ean                  = new StringType((string)$row['products_ean']);
            $price                = new DecimalType((float)$row['products_price']);
            $discountAllowed      = new DecimalType((float)$row['products_discount_allowed']);
            $taxClassId           = new IdType((int)$row['products_tax_class_id']);
            $quantity             = new DecimalType($row['products_quantity']);
            $name                 = new StringType((string)$row['products_name']);
            $image                = new StringType((string)$row['products_image']);
            $imageAltText         = new StringType((string)$row['gm_alt_text']);
            $urlKeyWords          = new StringType((string)$row['products_meta_keywords']);
            $weight               = new DecimalType((float)$row['products_weight']);
            $shippingCosts        = new DecimalType((float)$row['nc_ultra_shipping_costs']);
            $shippingTimeId       = new IdType((int)$row['products_shippingtime']);
            $productTypeId        = new IdType((int)$row['product_type']);
            $manufacturerId       = new IdType((int)$row['manufacturers_id']);
            $quantityUnitId       = new IdType((int)$row['quantity_unit_id']);
            $isFsk18              = new BoolType((bool)$row['products_fsk18']);
            $isVpeActive          = new BoolType((bool)$row['products_vpe_status']);
            $vpeId                = new IdType((int)$row['products_vpe']);
            $vpeValue             = new DecimalType((float)$row['products_vpe_value']);
            $specialId            = new IdType((int)$row['specials_id']);
            $mainCategoryId       = new IdType((int)$row['main_category_id']);
            
            $productListItem = MainFactory::create('ProductListItem', $this->productRepository);
            
            $productListItem->setProductId($productId)
                ->setActive($isActive)
                ->setSortOrder($sortOrder)
                ->setAddedDateTime($addedDateTime)
                ->setAvailableDateTime($availableDateTime)
                ->setLastModifiedDateTime($lastModifiedDateTime)
                ->setOrderedCount($orderedCount)
                ->setProductModel($productModel)
                ->setEan($ean)
                ->setPrice($price)
                ->setDiscountAllowed($discountAllowed)
                ->setTaxClassId($taxClassId)
                ->setQuantity($quantity)
                ->setName($name)
                ->setImage($image)
                ->setImageAltText($imageAltText)
                ->setUrlKeywords($urlKeyWords)
                ->setWeight($weight)
                ->setShippingCosts($shippingCosts)
                ->setShippingTimeId($shippingTimeId)
                ->setProductTypeId($productTypeId)
                ->setManufacturerId($manufacturerId)
                ->setQuantityUnitId($quantityUnitId)
                ->setFsk18($isFsk18)
                ->setVpeActive($isVpeActive)
                ->setVpeId($vpeId)
                ->setVpeValue($vpeValue)
                ->setSpecialOfferId($specialId)
                ->setMainCategoryId($mainCategoryId);
            
            $listItems[] = $productListItem;
        }
        
        $collection = MainFactory::create('ProductListItemCollection', $listItems);
        
        return $collection;
    }
    
    
    /**
     * Filters products records by a given ProductSearchCondition object and returns an collection with results.
     *
     * @param \ProductSearchCondition $condition Conditions object for search.
     * @param \Pager|null             $pager     (Optional) Pager object with pagination information
     * @param array                   $sorters   (Optional) array of Sorter objects with data sorting information
     *
     * @return \ProductListItemCollection
     */
    public function searchProducts(ProductSearchCondition $condition, \Pager $pager = null, array $sorters = [])
    {
        $this->_selectWithCategories()->_applyPagination($pager)->_applySorting($sorters);
        $this->db->where($condition->buildSql());
        $result = $this->db->group_by('products.products_id')->get()->result_array();
        
        return $this->_prepareCollection($result);
    }
    
    
    /**
     * Count the total of filtered products.
     *
     * @param \ProductSearchCondition $condition Conditions object for search.
     *
     * @return \IntType
     */
    public function searchProductsCount(ProductSearchCondition $condition)
    {
        $this->_selectWithCategories();
        $result = $this->db->where($condition->buildSql())->count_all_results();
        
        return new IntType($result);
    }
}