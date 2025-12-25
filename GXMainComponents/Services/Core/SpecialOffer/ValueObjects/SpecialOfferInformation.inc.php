<?php
/* --------------------------------------------------------------
   SpecialOfferInformation.inc.php 2018-07-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferInformation
 */
class SpecialOfferInformation implements SpecialOfferInformationInterface
{
    /**
     * @var double
     */
    protected $quantity;
    
    /**
     * @var double
     */
    protected $price;
    
    /**
     * @var bool
     */
    protected $status;
    
    /**
     * @var int
     */
    protected $productId;
    
    
    /**
     * SpecialOfferInformation constructor.
     *
     * @param \DecimalType $quantity  Special offer's quantity.
     * @param \DecimalType $price     Special offer's price.
     * @param \BoolType    $status    Special offer's status.
     * @param \IdType      $productId Special offer's product ID.
     */
    public function __construct(DecimalType $quantity, DecimalType $price, BoolType $status, IdType $productId)
    {
        $this->quantity  = $quantity->asDecimal();
        $this->price     = $price->asDecimal();
        $this->status    = $status->asBool();
        $this->productId = $productId->asInt();
    }
    
    
    /**
     * Named constructor of special offer information.
     *
     * @param double $quantity  Special offer's quantity.
     * @param double $price     Special offer's price.
     * @param bool   $status    Special offer's status.
     * @param int    $productId Special offer's product ID.
     *
     * @return \SpecialOfferInformation New instance.
     */
    public static function create($quantity, $price, $status, $productId)
    {
        $quantity  = new DecimalType($quantity);
        $price     = new DecimalType($price);
        $status    = new BoolType($status);
        $productId = new IdType($productId);
        
        return MainFactory::create(static::class, $quantity, $price, $status, $productId);
    }
    
    
    /**
     * Returns the quantity of the special offer.
     *
     * @return double Special offer's quantity.
     */
    public function quantity()
    {
        return $this->quantity;
    }
    
    
    /**
     * Returns the special offer's price.
     *
     * @return double Price of special offer.
     */
    public function price()
    {
        return $this->price;
    }
    
    
    /**
     * Returns the special offer status.
     *
     * @return bool True if special offer is active.
     */
    public function status()
    {
        return $this->status;
    }
    
    
    /**
     * Returns the productId the special offer belongs to.
     *
     * @return int ID of the offered product.
     */
    public function productId()
    {
        return $this->productId;
    }
}