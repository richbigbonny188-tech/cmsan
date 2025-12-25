<?php

/* --------------------------------------------------------------
   QuickEditProductListItem.inc.php 2023-03-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductListItem
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
class QuickEditProductListItem
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var string
     */
    protected $model;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $manufacturer;
    
    /**
     * @var float
     */
    protected $quantity;
    
    /**
     * @var int
     */
    protected $shippingTimeId;
    
    /**
     * @var string
     */
    protected $shippingStatusName;
    
    /**
     * @var float
     */
    protected $weight;
    
    /**
     * @var float
     */
    protected $shippingCosts;
    
    /**
     * @var int
     */
    protected $taxClassId;
    
    /**
     * @var float
     */
    protected $tax;
    
    /**
     * @var float
     */
    protected $price;
    
    /**
     * @var float
     */
    protected $discount;
    
    /**
     * @var int
     */
    protected $specialPriceId;
    
    /**
     * @var float
     */
    protected $specialPrice;
    
    /**
     * @var string
     */
    protected $specialPriceExpiresDate;
    
    /**
     * @var float
     */
    protected $specialPriceQuantity;
    
    /**
     * @var bool
     */
    protected $specialPriceStatus;
    
    /**
     * @var string
     */
    protected $category;
    
    /**
     * @var bool
     */
    protected $status;
    
    
    /**
     * QuickEditProductListItem constructor.
     *
     * @param array $value Contains product data.
     */
    public function __construct($value)
    {
        if (PRICE_IS_BRUTTO === 'true') {
            $this->setTax(new DecimalType((double)($value['tax_rate'] ?? 0)));
        }
        
        $this->setId(new IdType((int)($value['products_id'] ?? 0)));
        $this->setModel(new StringType((string)($value['products_model'] ?? '')));
        $this->setName(new StringType((string)($value['products_name'] ?? '')));
        $this->setManufacturer(new StringType((string)($value['products_manufacturer'] ?? '')));
        $this->setQuantity(new DecimalType((double)($value['products_quantity'] ?? 0.0)));
        $this->setShippingTimeId(new IntType((int)($value['products_shippingtime'] ?? '')));
        $this->setShippingStatusName(new StringType((string)($value['shipping_status_name'] ?? '')));
        $this->setWeight(new DecimalType((double)($value['products_weight'] ?? 0.0)));
        $this->setShippingCosts(new DecimalType((double)($value['nc_ultra_shipping_costs'] ?? 0)));
        $this->setTaxClassId(new IntType((int)($value['products_tax_class_id'] ?? '')));
        $this->setPrice(new DecimalType((double)($value['products_price'] ?? 0.0)));
        $this->setDiscount(new DecimalType((double)($value['products_discount_allowed'] ?? '')));
        $this->setSpecialPriceId(new IdType((int)($value['specials_id'] ?? 0)));
        $this->setSpecialPrice(new DecimalType((double)($value['specials_new_products_price'] ?? 0.0)));
        $this->setSpecialPriceExpiresDate($value['expires_date'] ?? '');
        $this->setSpecialPriceQuantity(new DecimalType((double)($value['specials_quantity'] ?? 0.0)));
        $this->setSpecialPriceStatus(new BoolType((bool)($value['status'] ?? false)));
        $this->setCategory(new StringType((string)($value['products_categories'] ?? '')));
        $this->setStatus(new BoolType((bool)($value['products_status'] ?? false)));
    }
    
    
    /**
     * Returns the Id of the product.
     *
     * @return int Returns the Id of the product.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Sets the id of the product.
     *
     * @param IdType $id Product id.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the model of the product.
     *
     * @return int Returns the model of the product.
     */
    public function getModel()
    {
        return $this->model;
    }
    
    
    /**
     * Sets the model of the product.
     *
     * @param StringType $model Product model.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setModel(StringType $model)
    {
        $this->model = $model->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the name of the product.
     *
     * @return int Returns the name of the product.
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Sets the name of the product.
     *
     * @param StringType $name Product name.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setName(StringType $name)
    {
        $this->name = $name->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the manufacturer of the product.
     *
     * @return int Returns the manufacturer of the product.
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }
    
    
    /**
     * Sets the manufacturer of the product.
     *
     * @param StringType $manufacturer Product manufacturer.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setManufacturer(StringType $manufacturer)
    {
        $this->manufacturer = $manufacturer->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the quantity of the product.
     *
     * @return int Returns the quantity of the product.
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    
    /**
     * Sets the quantity of the product.
     *
     * @param DecimalType $quantity Product quantity.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setQuantity(DecimalType $quantity)
    {
        $this->quantity = $quantity->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the shipping time id of the product.
     *
     * @return int Returns the shipping time id of the product.
     */
    public function getShippingTimeId()
    {
        return $this->shippingTimeId;
    }
    
    
    /**
     * Sets the shipping time id of the product.
     *
     * @param IntType $shippingTimeId Product shipping time id.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setShippingTimeId(IntType $shippingTimeId)
    {
        $this->shippingTimeId = $shippingTimeId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the shipping status name of the product.
     *
     * @return int Returns the shipping status name of the product.
     */
    public function getShippingStatusName()
    {
        return $this->shippingStatusName;
    }
    
    
    /**
     * Sets the shipping status name of the product.
     *
     * @param StringType $shippingStatusName Product shipping status name.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setShippingStatusName(StringType $shippingStatusName)
    {
        $this->shippingStatusName = $shippingStatusName->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the weight of the product.
     *
     * @return int Returns the weight of the product.
     */
    public function getWeight()
    {
        return sprintf('%01.3f', $this->weight);
    }
    
    
    /**
     * Sets the weight of the product.
     *
     * @param DecimalType $weight Product weight.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setWeight(DecimalType $weight)
    {
        $this->weight = $weight->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the shipping costs of the product.
     *
     * @return int Returns the shipping costs of the product.
     */
    public function getShippingCosts()
    {
        return sprintf('%01.2f', $this->shippingCosts);
    }
    
    
    /**
     * Sets the shipping costs of the product.
     *
     * @param DecimalType $shippingCosts Product shipping costs.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setShippingCosts(DecimalType $shippingCosts)
    {
        $this->shippingCosts = $shippingCosts->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the tax class id of the product.
     *
     * @return int Returns the tax class id of the product.
     */
    public function getTaxClassId()
    {
        return $this->taxClassId;
    }
    
    
    /**
     * Sets the tax class id of the product.
     *
     * @param IntType $taxClassId Product tax class id.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setTaxClassId(IntType $taxClassId)
    {
        $this->taxClassId = $taxClassId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the tax of the product.
     *
     * @return int Returns the tax of the product.
     */
    public function getTax()
    {
        return $this->tax;
    }
    
    
    /**
     * Sets the tax of the product.
     *
     * @param DecimalType $tax Product tax.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setTax(DecimalType $tax)
    {
        $this->tax = $tax->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the price of the product.
     *
     * @return int Returns the price of the product.
     */
    public function getPrice()
    {
        return sprintf('%01.2f', round($this->price * (1 + $this->tax / 100), 2));
    }
    
    
    /**
     * Sets the price of the product.
     *
     * @param DecimalType $price Product price.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setPrice(DecimalType $price)
    {
        $this->price = $price->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the discount of the product.
     *
     * @return int Returns the discount of the product.
     */
    public function getDiscount()
    {
        return sprintf('%01.2f', round($this->discount, 2));
    }
    
    
    /**
     * Sets the discount of the product.
     *
     * @param DecimalType $discount Product discount.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setDiscount(DecimalType $discount)
    {
        $this->discount = $discount->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the special price id of the product.
     *
     * @return int Returns the special price id of the product.
     */
    public function getSpecialPriceId()
    {
        return $this->specialPriceId;
    }
    
    
    /**
     * Sets the special price id of the product.
     *
     * @param IdType $specialPriceId Product special price id.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setSpecialPriceId(IdType $specialPriceId)
    {
        $this->specialPriceId = $specialPriceId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the special price of the product.
     *
     * @return int Returns the special price of the product.
     */
    public function getSpecialPrice()
    {
        return sprintf('%01.2f', round($this->specialPrice * (1 + $this->tax / 100), 2));
    }
    
    
    /**
     * Sets the special price of the product.
     *
     * @param DecimalType $specialPrice Product special price.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setSpecialPrice(DecimalType $specialPrice)
    {
        $this->specialPrice = $specialPrice->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the special price expires date of the product.
     *
     * @return int Returns the special price expires date of the product.
     */
    public function getSpecialPriceExpiresDate()
    {
        return $this->specialPriceExpiresDate;
    }
    
    
    /**
     * Sets the special price expires date of the product.
     *
     * @param DateTime $specialPriceExpiresDate Product special price expires date.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setSpecialPriceExpiresDate($specialPriceExpiresDate)
    {
        $this->specialPriceExpiresDate = date('d.m.Y', strtotime((string)$specialPriceExpiresDate));
        
        return $this;
    }
    
    
    /**
     * Returns the special price quantity of the product.
     *
     * @return int Returns the special price quantity of the product.
     */
    public function getSpecialPriceQuantity()
    {
        return $this->specialPriceQuantity;
    }
    
    
    /**
     * Sets the special price quantity of the product.
     *
     * @param DecimalType $specialPriceQuanity Product special price quantity.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setSpecialPriceQuantity(DecimalType $specialPriceQuanity)
    {
        $this->specialPriceQuantity = $specialPriceQuanity->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns the special price status of the product.
     *
     * @return int Returns the special price status of the product.
     */
    public function getSpecialPriceStatus()
    {
        return $this->specialPriceStatus;
    }
    
    
    /**
     * Sets the special price status of the product.
     *
     * @param BoolType $specialPriceStatus Product special price status.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setSpecialPriceStatus(BoolType $specialPriceStatus)
    {
        $this->specialPriceStatus = $specialPriceStatus->asBool();
        
        return $this;
    }
    
    
    /**
     * Returns the category of the product.
     *
     * @return int Returns the category of the product.
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    
    /**
     * Sets the category of the product.
     *
     * @param StringType $category Product category.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setCategory(StringType $category)
    {
        $this->category = $category->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the status of the product.
     *
     * @return int Returns the status of the product.
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    
    /**
     * Sets the status of the product.
     *
     * @param BoolType $status Product status.
     *
     * @return QuickEditProductListItem QuickEdit product collection.
     */
    public function setStatus(BoolType $status)
    {
        $this->status = $status->asBool();
        
        return $this;
    }
}