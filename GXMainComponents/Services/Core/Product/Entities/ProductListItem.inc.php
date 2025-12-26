<?php

/* --------------------------------------------------------------
   ProductListItem.inc.php 2022-02-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductListItem
 *
 * @category   System
 * @package    Product
 * @subpackage Entities
 */
class ProductListItem
{
    /**
     * Product repository.
     *
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * Product ID
     *
     * @var int
     */
    protected $productId = 0;
    
    /**
     * Product Active Status
     *
     * @var boolean
     */
    protected $active = false;
    
    /**
     * Sort Order
     *
     * @var int
     */
    protected $sortOrder = 0;
    
    /**
     * Added DateTime
     *
     * @var DateTime
     */
    protected $addedDateTime;
    
    /**
     * Available DateTime
     *
     * @var DateTime
     */
    protected $availableDateTime;
    
    /**
     * Last Modified DateTime
     *
     * @var DateTime
     */
    protected $lastModifiedDateTime;
    
    /**
     * Ordered Count
     *
     * @var int
     */
    protected $orderedCount = 0;
    
    /**
     * Product Model
     *
     * @var string
     */
    protected $productModel = '';
    
    /**
     * EAN
     *
     * @var string
     */
    protected $ean = '';
    
    /**
     * Price
     *
     * @var float
     */
    protected $price = 0.00;
    
    /**
     * Discount Allowed
     *
     * @var float
     */
    protected $discountAllowed = 0.00;
    
    /**
     * Tax Class ID
     *
     * @var int
     */
    protected $taxClassId = 0;
    
    /**
     * Quantity
     *
     * @var float
     */
    protected $quantity = 0.00;
    
    /**
     * Product Name
     *
     * @var string
     */
    protected $name = '';
    
    /**
     * Image
     *
     * @var string
     */
    protected $image = '';
    
    /**
     * Image alternative text
     *
     * @var string
     */
    protected $imageAltText = '';
    
    /**
     * URL Keywords
     *
     * @var string
     */
    protected $urlKeywords = '';
    
    /**
     * Weight
     *
     * @var float
     */
    protected $weight = 0.00;
    
    /**
     * Shipping costs
     *
     * @var float
     */
    protected $shippingCosts = 0.00;
    
    /**
     * Shipping Time ID
     *
     * @var int
     */
    protected $shippingTimeId = 0;
    
    /**
     * Product Type ID
     *
     * @var int
     */
    protected $productTypeId = 0;
    
    /**
     * Manufacturer ID
     *
     * @var int
     */
    protected $manufacturerId = 0;
    
    /**
     * Manufacturer ID
     *
     * @var int
     */
    protected $quantityUnitId = 0;
    
    /**
     * FSK 18 Status
     *
     * @var bool
     */
    protected $fsk18 = false;
    
    /**
     * VPE Active Status
     *
     * @var bool
     */
    protected $vpeActive = false;
    
    /**
     * VPE ID
     *
     * @var int
     */
    protected $vpeId = 0;
    
    /**
     * VPE Value
     *
     * @var float
     */
    protected $vpeValue = 0.00;
    
    
    /**
     * Special offer id.
     *
     * @var int
     */
    protected $specialOfferId = 0;
    
    /**
     * Main category id.
     *
     * @var int
     */
    protected $mainCategoryId = 0;
    
    
    /**
     * ProductListItem constructor.
     *
     * @param ProductRepositoryInterface $productRepository Product Repository.
     * @param IdType                     $productId         Optional (null), product ID.
     * @param BoolType                   $isActive          Optional (null), product active status.
     * @param StringType                 $name              Optional (null), product name.
     * @param StringType                 $urlKeywords       Optional (null), URL keywords.
     * @param StringType                 $image             Optional (null), product image.
     * @param StringType                 $imageAltText      Optional (null), product image alternative text.
     *
     * @todo Remove optional methods from this constructor for v3.5.1.0.
     *
     * Deprecation Notice:
     *
     * The optional parameters of this constructor are deprecated as of v3.3.1.0 and will be removed in
     * v3.5.1.0. Please use the setter methods instead.
     *
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        IdType $productId = null,
        BoolType $isActive = null,
        StringType $name = null,
        StringType $urlKeywords = null,
        StringType $image = null,
        StringType $imageAltText = null
    ) {
        $this->productRepository = $productRepository;
    }
    
    
    /**
     * Returns the product ID.
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }
    
    
    /**
     * Checks if product is active or not.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }
    
    
    /**
     * Returns the name of the product.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Returns the URL keywords.
     *
     * @return string
     */
    public function getUrlKeywords()
    {
        return $this->urlKeywords;
    }
    
    
    /**
     * Returns the image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
    
    
    /**
     * Returns the alternative image text.
     *
     * @return string
     */
    public function getImageAltText()
    {
        return $this->imageAltText;
    }
    
    
    /**
     * Returns the product object.
     *
     * @return StoredProductInterface Returns same instance for chained calls.
     */
    public function getProductObject()
    {
        $id = new IdType($this->getProductId());
        
        return $this->productRepository->getProductById($id);
    }
    
    
    /**
     * Get Sort Order
     *
     * @return int The sort order.
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
    
    
    /**
     * Get Available Date Time
     *
     * @return DateTime The available date time.
     */
    public function getAvailableDateTime()
    {
        return $this->availableDateTime;
    }
    
    
    /**
     * Get Added Date Time
     *
     * @return DateTime The added date time.
     */
    public function getAddedDateTime()
    {
        return $this->addedDateTime;
    }
    
    
    /**
     * Get Last Modified Date Time
     *
     * @return DateTime The last modified date time.
     */
    public function getLastModifiedDateTime()
    {
        return $this->lastModifiedDateTime;
    }
    
    
    /**
     * Get Ordered Count
     *
     * @return int The ordered count.
     */
    public function getOrderedCount()
    {
        return $this->orderedCount;
    }
    
    
    /**
     * Get Product Model
     *
     * @return string The product model.
     */
    public function getProductModel()
    {
        return $this->productModel;
    }
    
    
    /**
     * Get EAN
     *
     * @return string The EAN of the product.
     */
    public function getEan()
    {
        return $this->ean;
    }
    
    
    /**
     * Get Price
     *
     * @return float The price of the product.
     */
    public function getPrice()
    {
        return $this->price;
    }
    
    
    /**
     * Get Tax Class ID
     *
     * @return int The tax class ID.
     */
    public function getTaxClassId()
    {
        return $this->taxClassId;
    }
    
    
    /**
     * Get Quantity
     *
     * @return float The quantity of the product.
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    
    /**
     * Get Weight
     *
     * @return float The weight of the product.
     */
    public function getWeight()
    {
        return $this->weight;
    }
    
    
    /**
     * Get Discount Allowed
     *
     * @return float The allowed discount.
     */
    public function getDiscountAllowed()
    {
        return $this->discountAllowed;
    }
    
    
    /**
     * Get Shipping Costs
     *
     * @return float The shipping costs of the product.
     */
    public function getShippingCosts()
    {
        return $this->shippingCosts;
    }
    
    
    /**
     * Get Shipping Time ID
     *
     * @return int The shipping time ID.
     */
    public function getShippingTimeId()
    {
        return $this->shippingTimeId;
    }
    
    
    /**
     * Get Product Type ID.
     *
     * @return int The product type ID.
     */
    public function getProductTypeId()
    {
        return $this->productTypeId;
    }
    
    
    /**
     * Get Manufacturer ID
     *
     * @return int The manufacturer ID.
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }
    
    
    /**
     * Get Quantity Unit ID
     *
     * @return int The quantity unit ID.
     */
    public function getQuantityUnitId()
    {
        return $this->quantityUnitId;
    }
    
    
    /**
     * Is FSK 18
     *
     * @return bool Is the product FSK18?
     */
    public function isFsk18()
    {
        return $this->fsk18;
    }
    
    
    /**
     * Is VPE Active
     *
     * @return bool Is VPE active on the product?
     */
    public function isVpeActive()
    {
        return $this->vpeActive;
    }
    
    
    /**
     * Get VPE ID
     *
     * @return int VPE ID.
     */
    public function getVpeId()
    {
        return $this->vpeId;
    }
    
    
    /**
     * Get VPE Value
     *
     * @return float The VPE value.
     */
    public function getVpeValue()
    {
        return $this->vpeValue;
    }
    
    
    /**
     * Returns the Special offer id.
     *
     * @return int The special offer id.
     */
    public function getSpecialOfferId()
    {
        return $this->specialOfferId;
    }
    
    
    /**
     * Returns the Main Category id.
     *
     * @return int The main category id.
     */
    public function getMainCategoryId()
    {
        return $this->mainCategoryId;
    }
    
    
    /**
     * Set Product ID value.
     *
     * @param IdType $id
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setProductId(IdType $id)
    {
        $this->productId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Set active state value.
     *
     * @param BoolType $status
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setActive(BoolType $status)
    {
        $this->active = $status->asBool();
        
        return $this;
    }
    
    
    /**
     * Set product name.
     *
     * @param StringType $name
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setName(StringType $name)
    {
        $this->name = $name->asString();
        
        return $this;
    }
    
    
    /**
     * Set product URL keywords.
     *
     * @param StringType $urlKeywords
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setUrlKeywords(StringType $urlKeywords)
    {
        $this->urlKeywords = $urlKeywords->asString();
        
        return $this;
    }
    
    
    /**
     * Set product image.
     *
     * @param StringType $image
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setImage(StringType $image)
    {
        $this->image = $image->asString();
        
        return $this;
    }
    
    
    /**
     * Set image alt text.
     *
     * @param StringType $imageAltText
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setImageAltText(StringType $imageAltText)
    {
        $this->imageAltText = $imageAltText->asString();
        
        return $this;
    }
    
    
    /**
     * Set Sort Order
     *
     * @param IntType $sortOrder The sort order.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setSortOrder(IntType $sortOrder)
    {
        $this->sortOrder = $sortOrder->asInt();
        
        return $this;
    }
    
    
    /**
     * Sets the added date time.
     *
     * @param DateTime $date
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setAddedDateTime(DateTime $date)
    {
        $this->addedDateTime = $date;
        
        return $this;
    }
    
    
    /**
     * Set Available Date Time
     *
     * @param DateTime $date
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setAvailableDateTime(DateTime $date)
    {
        $this->availableDateTime = $date;
        
        return $this;
    }
    
    
    /**
     * Set Last Modified Date Time
     *
     * @param DateTime $date The last modified date time.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setLastModifiedDateTime(DateTime $date)
    {
        $this->lastModifiedDateTime = $date;
        
        return $this;
    }
    
    
    /**
     * Set Ordered Count
     *
     * @param IntType $count The ordered count.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setOrderedCount(IntType $count)
    {
        $this->orderedCount = $count->asInt();
        
        return $this;
    }
    
    
    /**
     * Set Product Model
     *
     * @param StringType $model The product model.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setProductModel(StringType $model)
    {
        $this->productModel = $model->asString();
        
        return $this;
    }
    
    
    /**
     * Set EAN
     *
     * @param StringType $ean The EAN to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setEan(StringType $ean)
    {
        $this->ean = $ean->asString();
        
        return $this;
    }
    
    
    /**
     * Set Price
     *
     * @param DecimalType $price The price to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setPrice(DecimalType $price)
    {
        $this->price = $price->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Set Tax Class ID
     *
     * @param IdType $id The tax class ID to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setTaxClassId(IdType $id)
    {
        $this->taxClassId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Set Quantity
     *
     * @param DecimalType $quantity The quantity to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setQuantity(DecimalType $quantity)
    {
        $this->quantity = $quantity->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Set Weight
     *
     * @param DecimalType $weight The weight to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setWeight(DecimalType $weight)
    {
        $this->weight = $weight->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Set Discount Allowed
     *
     * @param DecimalType $discount The discount to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setDiscountAllowed(DecimalType $discount)
    {
        $this->discountAllowed = $discount->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Set Shipping Costs
     *
     * @param DecimalType $price The shipping costs to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setShippingCosts(DecimalType $price)
    {
        $this->shippingCosts = $price->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Set Shipping Time ID
     *
     * @param IdType $id The shipping time ID to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setShippingTimeId(IdType $id)
    {
        $this->shippingTimeId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Set Product Type ID.
     *
     * @param IdType $id Product type ID.
     *
     * @return ProductListItem
     */
    public function setProductTypeId(IdType $id)
    {
        $this->productTypeId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Set Manufacturer ID
     *
     * @param IdType $id The manufacturer ID to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setManufacturerId(IdType $id)
    {
        $this->manufacturerId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Set Quantity Unit ID
     *
     * @param IdType $id The quantity unit ID to set.
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setQuantityUnitId(IdType $id)
    {
        $this->quantityUnitId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Set FSK 18
     *
     * @param BoolType $status
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setFsk18(BoolType $status)
    {
        $this->fsk18 = $status->asBool();
        
        return $this;
    }
    
    
    /**
     * Set VPE Active
     *
     * @param BoolType $status
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setVpeActive(BoolType $status)
    {
        $this->vpeActive = $status->asBool();
        
        return $this;
    }
    
    
    /**
     * Set VPE ID
     *
     * @param IdType $id
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setVpeId(IdType $id)
    {
        $this->vpeId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Set VPE Value
     *
     * @param DecimalType $vpeValue
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setVpeValue(DecimalType $vpeValue)
    {
        $this->vpeValue = $vpeValue->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Set Special offer id.
     *
     * @param IdType $id
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setSpecialOfferId(IdType $id)
    {
        $this->specialOfferId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Set main category id.
     *
     * @param IdType $id
     *
     * @return ProductListItem Returns same instance for chained calls.
     */
    public function setMainCategoryId(IdType $id)
    {
        $this->mainCategoryId = $id->asInt();
    
        return $this;
    }
}