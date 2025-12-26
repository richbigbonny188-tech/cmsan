<?php
/* --------------------------------------------------------------
   QuickEditProductSpecialPriceListItem.inc.php 2018-04-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuickEditProductSpecialPriceListItem
 *
 * @category   System
 * @package    Extensions
 * @subpackage QuickEdit
 */
class QuickEditProductSpecialPriceListItem
{
    /**
     * @var int
     */
    protected $productsId;
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var string
     */
    protected $model;
    
    /**
     * @var float
     */
    protected $quantity;
    
    /**
     * @var string
     */
    protected $isNewEntry;
    
    /**
     * @var string
     */
    protected $expiresDate;
    
    /**
     * @var float
     */
    protected $price;
    
    /**
     * @var float
     */
    protected $productsPrice;
    
    /**
     * @var float
     */
    protected $tax;
    
    /**
     * @var bool
     */
    protected $status;
    
    
    /**
     * QuickEditProductSpecialPriceListItem constructor.
     *
     * @param array $value Contains special price data.
     */
    public function __construct($value)
    {
        if (PRICE_IS_BRUTTO === 'true') {
            $this->setTax(new DecimalType((double)$value['tax_rate']));
        }
        
        $this->setProductsId(new IdType((int)$value['products_id']));
        $this->setProductsName(new StringType((string)$value['products_name']));
        $this->setProductsModel(new StringType((string)$value['products_model']));
        $this->setProductsPrice(new DecimalType((double)$value['products_price']));
        $this->setQuantity(new DecimalType((double)$value['specials_quantity']));
        $this->setPrice(new DecimalType((double)$value['specials_new_products_price']));
        
        if ($value['expires_date'] === null) {
            $this->setNewEntry(new BoolType(true));
            $this->setExpiresDate(new DateTime(date('Y-m-d', strtotime('+1 months'))));
        } else {
            $this->setNewEntry(new BoolType(false));
            $this->setExpiresDate(new DateTime($value['expires_date']));
        }
        
        $this->setStatus(new BoolType((int)$value['status']));
    }
    
    
    /**
     * Returns the Id of the product.
     *
     * @return int Returns the Id of the product.
     */
    public function getProductsId()
    {
        return $this->productsId;
    }
    
    
    /**
     * Sets the id of the product.
     *
     * @param IdType $id Product id.
     *
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setProductsId(IdType $id)
    {
        $this->productsId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the name of the product.
     *
     * @return int Returns the name of the product.
     */
    public function getProductsName()
    {
        return $this->name;
    }
    
    
    /**
     * Sets the name of the product.
     *
     * @param StringType $productsName Product name.
     *
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setProductsName(StringType $productsName)
    {
        $this->name = $productsName->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the model of the product.
     *
     * @return int Returns the model of the product.
     */
    public function getProductsModel()
    {
        return $this->model;
    }
    
    
    /**
     * Sets the model of the product.
     *
     * @param StringType $model Product model.
     *
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setProductsModel(StringType $model)
    {
        $this->model = $model->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the price of the product.
     *
     * @return int Returns the price of the product.
     */
    public function getProductsPrice()
    {
        return sprintf('%01.2f', round($this->productsPrice * (1 + $this->tax / 100), 2));
    }
    
    
    /**
     * Sets the model of the product.
     *
     * @param DecimalType $productsPrice Product price.
     *
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setProductsPrice(DecimalType $productsPrice)
    {
        $this->productsPrice = $productsPrice->asDecimal();
        
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
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setQuantity(DecimalType $quantity)
    {
        $this->quantity = $quantity->asDecimal();
        
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
     * @return QuickEditProductSpecialPriceListItem QuickEdit product special price  collection.
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
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setPrice(DecimalType $price)
    {
        $this->price = $price->asDecimal();
        
        return $this;
    }
    
    
    /**
     * Returns whether the entry is new.
     *
     * @return boolean Returns whether the entry is new.
     */
    public function isNewEntry()
    {
        return $this->isNewEntry;
    }
    
    
    /**
     * Saves whether the entry is new.
     *
     * @param BoolType $newEntry
     *
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setNewEntry(BoolType $newEntry)
    {
        $this->isNewEntry = $newEntry->asBool();
        
        return $this;
    }
    
    
    /**
     * Returns the expires date of the product.
     *
     * @return int Returns the expires date of the product.
     */
    public function getExpiresDate()
    {
        return $this->expiresDate;
    }
    
    
    /**
     * Sets the special price expires date of the product.
     *
     * @param DateTime $expiresDate Product special price expires date.
     *
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setExpiresDate(DateTime $expiresDate)
    {
        $format = ($_SESSION['language_code'] === 'de') ? 'd.m.Y' : 'm.d.Y';
        
        $this->expiresDate = $expiresDate->format($format);
        
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
     * @return QuickEditProductSpecialPriceListItem Returns same instance for chained method calls.
     */
    public function setStatus(BoolType $status)
    {
        $this->status = $status->asBool();
        
        return $this;
    }
}