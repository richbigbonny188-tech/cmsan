<?php
/* --------------------------------------------------------------
   CartItem.php 2023-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

/**
 * Class CartItem
 *
 * A CartItem contains EAN, name, price, quantity and attributes.
 *
 * @package HubPublic\ValueObjects
 */
class CartItem
{
    /**
     * Cart item ean
     *
     * @var string
     */
    private $ean;
    
    /**
     * Cart item name
     *
     * @var string
     */
    private $name;
    
    /**
     * Cart item price
     *
     * @var float
     */
    private $price;
    
    /**
     * Cart item quantity
     *
     * @var float
     */
    private $quantity;
    
    /**
     * Cart item attributes
     *
     * @var array
     */
    private $attributes;
    
    /**
     * URL for primary product image
     *
     * @var string
     */
    private $imageUrl;
    
    /**
     * Category path in shop, segments separated by '>'
     *
     * @var string
     */
    private $categoryPath;
    
    /**
     * Manufacturer’s product number
     *
     * @var string
     */
    private $mpn;
    
    /**
     * URL for product page
     *
     * @var string
     */
    private $productUrl;
    
    /**
     * Quantity unit, e.g. 'kg', 'm', 'pieces'
     *
     * @var string
     */
    private $quantityUnit;
    
    /**
     * Article number (products_model)
     *
     * @var string
     */
    private $reference;
    
    /**
     * Type of cart item
     * e.g. physical|digital|discount|shipping_fee|sales_tax|gift_card|store_credit|surcharge|…
     *
     * @var string
     */
    private $type;
    
    /**
     * Brand name
     *
     * @var string
     */
    private $brand;
    
    /**
     * Tax rate in percent, i.e. 19.00 for 19%.
     *
     * @var float
     */
    private $tax;
    
    private $model;
    
    
    /**
     * @param string $ean
     * @param string $name
     * @param float  $price
     * @param float  $quantity
     * @param array  $attributes
     * @param string $imageUrl
     * @param string $categoryPath
     * @param string $mpn
     * @param string $productUrl
     * @param string $quantityUnit
     * @param string $reference
     * @param string $type
     * @param string $brand
     * @param float  $tax
     */
    public function __construct(
        string $ean,
        string $name,
        float $price,
        float $quantity,
        array $attributes,
        string $imageUrl = '',
        string $categoryPath = '',
        string $mpn = '',
        string $productUrl = '',
        string $quantityUnit = '',
        string $reference = '',
        string $type = '',
        string $brand = '',
        float $tax = 0.00,
        string $model = ''
    ) {
        $this->ean          = trim($ean);
        $this->name         = trim($name);
        $this->price        = $price;
        $this->quantity     = $quantity;
        $this->attributes   = $attributes;
        $this->imageUrl     = $imageUrl;
        $this->categoryPath = $categoryPath;
        $this->mpn          = $mpn;
        $this->productUrl   = $productUrl;
        $this->quantityUnit = $quantityUnit;
        $this->reference    = $reference;
        $this->type         = $type;
        $this->brand        = $brand;
        $this->tax          = $tax;
        $this->model        = $model;
    }
    
    
    /**
     * Returns the EAN.
     *
     * @return string Cart item EAN
     */
    public function getEan(): string
    {
        return $this->ean;
    }
    
    
    /**
     * Returns the name.
     *
     * @return string Cart item Name
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    
    /**
     * Returns the price.
     *
     * @return float Cart item price
     */
    public function getPrice(): float
    {
        return $this->price;
    }
    
    
    /**
     * Returns the quantity.
     *
     * @return float Cart item quantity
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }
    
    
    /**
     * Returns the attributes as an array.
     *
     * @return array Cart item attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    
    /**
     * Returns the product’s primary image URL
     *
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }
    
    
    /**
     * Returns the product’s category path.
     *
     * Path elements are separated by '>'
     *
     * @return string
     */
    public function getCategoryPath(): string
    {
        return $this->categoryPath;
    }
    
    
    /**
     * Returns the Manufacturer’s Product Number.
     *
     * @return string
     */
    public function getMpn(): string
    {
        return $this->mpn;
    }
    
    
    /**
     * Returns product page URL.
     *
     * @return string
     */
    public function getProductUrl(): string
    {
        return $this->productUrl;
    }
    
    
    /**
     * Returns quantity unit.
     *
     * @return string
     */
    public function getQuantityUnit(): string
    {
        return $this->quantityUnit;
    }
    
    
    /**
     * Returns product reference (products_model)
     *
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }
    
    
    /**
     * Returns type of CartItem (physical/digital/…)
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    
    /**
     * Returns product’s brand name.
     *
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }
    
    
    /**
     * Returns tax rate, e.g. 19.0 for 19%.
     *
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }
    
    
    /**
     * Returns the model (product number).
     * 
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }
}
