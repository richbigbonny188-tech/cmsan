<?php
/* --------------------------------------------------------------
   ProductRepositoryReader.inc.php 2022-05-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductRepositoryReader
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductRepositoryReader implements ProductRepositoryReaderInterface
{
    /**
     * DB Connection.
     *
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * Product Factory
     *
     * @var ProductFactory
     */
    protected $productFactory;
    
    
    /**
     * Customer Status Provider
     *
     * @var CustomerStatusProviderInterface
     */
    protected $customerStatusProvider;
    
    
    /**
     * ProductRepositoryReader constructor.
     *
     * @param CI_DB_query_builder             $db
     * @param ProductFactoryInterface         $productFactory
     * @param CustomerStatusProviderInterface $customerStatusProvider
     */
    public function __construct(
        CI_DB_query_builder $db,
        ProductFactoryInterface $productFactory,
        CustomerStatusProviderInterface $customerStatusProvider
    ) {
        $this->db                     = $db;
        $this->productFactory         = $productFactory;
        $this->customerStatusProvider = $customerStatusProvider;
    }
    
    
    /**
     * Returns a product entity instance by the given product id.
     *
     * @param IdType $productId Id of product entity.
     *
     * @return StoredProductInterface Product entity with the expected product id.
     * @throws InvalidArgumentException
     *
     * @throws UnexpectedValueException
     */
    public function getById(IdType $productId)
    {
        $product = $this->db->select('products.*, specials.specials_id')
            ->from('products')
            ->join('specials',
                   'specials.products_id = products.products_id',
                   'left')
            ->where('products.products_id', $productId->asInt())
            ->get()
            ->row_array();
        
        $productDescriptionQuery = $this->db->select('products_description.*, languages.code AS language_code')
            ->from('products_description')
            ->join('languages',
                   'languages.languages_id = products_description.language_id',
                   'inner')
            ->where('products_description.products_id', $productId->asInt());
        
        $productDescription = $productDescriptionQuery->get()->result_array();
        
        if ($product === null) {
            throw new UnexpectedValueException('The requested product was not found in database (ID: '
                                               . $productId->asInt() . ')');
        }
        
        if (count($productDescription) === 0) {
            throw new UnexpectedValueException('The requested product description was not found in database (ID: '
                                               . $productId->asInt() . ')');
        }
        
        // Set quantity unit id of this product
        $product['quantity_unit_id'] = 0;
        $quantityUnit                = $this->db->get_where('products_quantity_unit',
                                                            ['products_id' => $productId->asInt()])->row_array();
        if ($product !== null && $quantityUnit !== null) {
            $product['quantity_unit_id'] = $quantityUnit['quantity_unit_id'];
        }
        
        return $this->_createProductByArray($product, $productDescription);
    }
    
    
    /**
     * Returns the count of all product entries.
     *
     * @return int
     */
    public function getProductsCount()
    {
        return $this->db->count_all('products');
    }
    
    /*
     | -----------------------------------------------------------------------------------------------------------------
     | Helper Methods
     | -----------------------------------------------------------------------------------------------------------------
     */
    
    /**
     * Create Product by Array
     *
     * Creates and returns a StoredProduct.
     *
     * @param array $product            Product query result.
     * @param array $productDescription Product description query result.
     *
     * @return StoredProduct StoredProduct object.
     * @throws UnexpectedValueException
     *
     * @throws InvalidArgumentException
     */
    protected function _createProductByArray(array $product, array $productDescription)
    {
        $storedProduct = $this->productFactory->createStoredProduct(new IdType($product['products_id']));
        
        // Set language specific data
        foreach ($productDescription as $row) {
            $languageCode = new LanguageCode(new NonEmptyStringType($row['language_code']));
            
            $storedProduct->setName(new StringType((string)$row['products_name']), $languageCode);
            $storedProduct->setDescription(new StringType((string)$row['products_description']), $languageCode);
            $storedProduct->setShortDescription(new StringType((string)$row['products_short_description']),
                                                $languageCode);
            $storedProduct->setKeywords(new StringType((string)$row['products_keywords']), $languageCode);
            $storedProduct->setMetaTitle(new StringType((string)$row['products_meta_title']), $languageCode);
            $storedProduct->setMetaDescription(new StringType((string)$row['products_meta_description']),
                                               $languageCode);
            $storedProduct->setMetaKeywords(new StringType((string)$row['products_meta_keywords']), $languageCode);
            $storedProduct->setInfoUrl(new StringType((string)$row['products_url']), $languageCode);
            $storedProduct->setUrlKeywords(new StringType((string)$row['gm_url_keywords']), $languageCode);
            $storedProduct->setCheckoutInformation(new StringType((string)$row['checkout_information']), $languageCode);
            $storedProduct->setViewedCount(new IntType((int)$row['products_viewed']), $languageCode);
        }
        
        $storedProduct->setActive(new BoolType((bool)$product['products_status']));
        $storedProduct->setSortOrder(new IntType((int)$product['products_sort']));
        $storedProduct->setOrderedCount(new IntType((int)$product['products_ordered']));
        $storedProduct->setProductModel(new StringType((string)$product['products_model']));
        $storedProduct->setEan(new StringType((string)$product['products_ean']));
        $storedProduct->setPrice(new DecimalType((float)$product['products_price']));
        $storedProduct->setTaxClassId(new IdType((int)$product['products_tax_class_id']));
        $storedProduct->setQuantity(new DecimalType((float)$product['products_quantity']));
        $storedProduct->setMinimumQuantity(new DecimalType((float)$product['gm_min_order']));
        $storedProduct->setWeight(new DecimalType((float)$product['products_weight']));
        $storedProduct->setDiscountAllowed(new DecimalType((float)$product['products_discount_allowed']));
        $storedProduct->setShippingCosts(new DecimalType((float)$product['nc_ultra_shipping_costs']));
        $storedProduct->setShippingTimeId(new IdType((int)$product['products_shippingtime']));
        $storedProduct->setProductTypeId(new IdType((int)$product['product_type']));
        $storedProduct->setManufacturerId(new IdType((int)$product['manufacturers_id']));
        $storedProduct->setQuantityUnitId(new IdType((int)$product['quantity_unit_id']));
        $storedProduct->setFsk18(new BoolType((bool)$product['products_fsk18']));
        $storedProduct->setVpeActive(new BoolType((bool)$product['products_vpe_status']));
        $storedProduct->setVpeId(new IdType((int)$product['products_vpe']));
        $storedProduct->setVpeValue(new DecimalType((float)$product['products_vpe_value']));
        $storedProduct->setAddedDateTime(new EmptyDateTime($product['products_date_added']));
        $storedProduct->setAvailableDateTime(new EmptyDateTime($product['products_date_available']));
        $storedProduct->setLastModifiedDateTime(new EmptyDateTime($product['products_last_modified']));
        $storedProduct->setSpecialOfferId(new IdType((int)$product['specials_id']));
    
        if (!is_null($product['main_category_id'])) {
            $storedProduct->setMainCategoryId(new IdType((int)$product['main_category_id']));
        }
        
        return $storedProduct;
    }
    
    
    /**
     * @inheritDoc
     */
    public function countImageUsage(string $productImagePath): int
    {
        $primaryCount   = (int)$this->db->reset_query()
                                   ->select('COUNT(products_image) AS "usage"')
                                   ->from('products')
                                   ->where('products_image', $productImagePath)
                                   ->get()
                                   ->row_array()['usage'];
        $secondaryCount = (int)$this->db->reset_query()
                                   ->select('COUNT(image_name) AS "usage"')
                                   ->from('products_images')
                                   ->where('image_name', $productImagePath)
                                   ->get()
                                   ->row_array()['usage'];
        
        return $primaryCount + $secondaryCount;
    }
}