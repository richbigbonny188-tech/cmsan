<?php
/* --------------------------------------------------------------
   GXEngineShoppingCart.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class GXEngineShoppingCart
 *
 * @category   System
 * @package    ShoppingCart
 */
class GXEngineShoppingCart implements ShoppingCartInterface
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var int
     */
    protected $customerId;
    
    /**
     * @var string
     */
    protected $productId;
    
    /**
     * @var float
     */
    protected $quantity;
    
    /**
     * @var float
     */
    protected $finalPrice;
    
    /**
     * @var string
     */
    protected $creationDate;
    
    
    /**
     * GXEngineShoppingCart constructor.
     *
     * @param \IdType      $id
     * @param \IdType      $customerId
     * @param \StringType  $productId
     * @param \DecimalType $quantity
     * @param \DecimalType $finalPrice
     * @param \StringType  $creationDate
     */
    public function __construct(
        IdType $id,
        IdType $customerId,
        StringType $productId,
        DecimalType $quantity,
        DecimalType $finalPrice,
        StringType $creationDate
    ) {
        $this->id           = $id->asInt();
        $this->customerId   = $customerId->asInt();
        $this->productId    = $productId->asString();
        $this->quantity     = $quantity->asDecimal();
        $this->finalPrice   = $finalPrice->asDecimal();
        $this->creationDate = $creationDate->asString();
    }
    
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
    
    
    /**
     * @return string
     */
    public function getProductId()
    {
        return $this->productId;
    }
    
    
    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    
    /**
     * @return float
     */
    public function getFinalPrice()
    {
        return $this->finalPrice;
    }
    
    
    /**
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
}