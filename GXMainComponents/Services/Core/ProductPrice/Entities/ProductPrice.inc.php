<?php
/* --------------------------------------------------------------
   ProductPrice.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPrice
 *
 * @category   System
 * @package    ProductPrice
 * @subpackage ValueObjects
 */
class ProductPrice implements ProductPriceInterface
{
    /**
     * @var int
     */
    protected $productId;
    
    /**
     * @var double
     */
    protected $price;
    
    /**
     * int
     */
    protected $taxClassId;
    
    /**
     * @var \ProductGroupPriceCollection|null
     */
    protected $groupPrices;
    
    
    /**
     * ProductPrice constructor.
     *
     * @param \IdType                           $productId   Product id.
     * @param \DecimalType                      $price       Product default price.
     * @param \IdType                           $taxClassId
     * @param \ProductGroupPriceCollection|null $groupPrices Product's group price.
     */
    public function __construct(
        IdType $productId,
        DecimalType $price,
        IdType $taxClassId = null,
        ProductGroupPriceCollection $groupPrices = null
    ) {
        $this->productId   = $productId->asInt();
        $this->price       = $price->asDecimal();
        $this->taxClassId  = $taxClassId !== null ? $taxClassId->asInt() : null;
        $this->groupPrices = $groupPrices;
    }
    
    
    /**
     * Named constructor of product price.
     *
     * @param int                               $productId   Product id.
     * @param double                            $price       Product default price.
     * @param int                               $taxClassId
     * @param \ProductGroupPriceCollection|null $groupPrices Product's group price.
     *
     * @return \ProductPrice New instance
     */
    public static function create($productId, $price, $taxClassId, ProductGroupPriceCollection $groupPrices = null)
    {
        $productId  = new IdType($productId);
        $price      = new DecimalType($price);
        $taxClassId = new IdType($taxClassId);
        
        return MainFactory::create(static::class, $productId, $price, $taxClassId, $groupPrices);
    }
    
    
    public static function createWithoutTaxClassId($productId, $price, ProductGroupPriceCollection $groupPrices = null)
    {
        $productId = new IdType($productId);
        $price     = new DecimalType($price);
        
        return MainFactory::create(static::class, $productId, $price, null, $groupPrices);
    }
    
    
    /**
     * Returns the product id.
     *
     * @return int Product id.
     */
    public function productId()
    {
        return $this->productId;
    }
    
    
    /**
     * Returns the product price
     *
     * @return double Product price.
     */
    public function price()
    {
        return $this->price;
    }
    
    
    /**
     * Returns the tax class id.
     *
     * @return int Tax class id.
     */
    public function taxClassId()
    {
        return $this->taxClassId;
    }
    
    
    /**
     * Returns the group prices.
     *
     * @return ProductGroupPriceCollection|null
     */
    public function groupPrices()
    {
        return $this->groupPrices;
    }
}