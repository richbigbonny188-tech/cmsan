<?php
/* --------------------------------------------------------------
   SharedShoppingCart.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCart
 *
 * @category   System
 * @package    ShoppingCart
 */
class SharedShoppingCart implements SharedShoppingCartInterface
{
    /**
     * @var string
     */
    protected $hash;
    
    /**
     * @var string
     */
    protected $shoppingCartJson;
    
    /**
     * @var DateTime
     */
    protected $creationDate;
    
    /**
     * @var int
     */
    protected $customerId;
    
    
    /**
     * SharedShoppingCart constructor.
     *
     * @param \StringType $hash
     * @param \StringType $shoppingCartJson
     * @param \DateTime   $creationDate
     * @param \IdType     $customerId
     */
    public function __construct(
        StringType $hash,
        StringType $shoppingCartJson,
        DateTime $creationDate,
        IdType $customerId
    ) {
        $this->hash             = $hash->asString();
        $this->shoppingCartJson = $shoppingCartJson->asString();
        $this->creationDate     = $creationDate;
        $this->customerId       = $customerId->asInt();
    }
    
    
    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
    
    
    /**
     * @return string
     */
    public function getShoppingCartJson()
    {
        return $this->shoppingCartJson;
    }
    
    
    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
    
    
    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}