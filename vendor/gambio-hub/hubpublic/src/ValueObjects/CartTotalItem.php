<?php
/* --------------------------------------------------------------
   CartItem.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

/**
 * Class CartTotalItem
 *
 * An auxiliary cart item, such as shipping cost, discount or surcharge
 *
 * @package HubPublic\ValueObjects
 */
class CartTotalItem
{
    /**
     * @var string
     */
    private $code;
    
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var float
     */
    private $value;
    
    /**
     * @var float
     */
    private $changeTotal;
    
    /**
     * @var float
     */
    private $changeTax;
    
    /**
     * @var float
     */
    private $changeShippingCost;
    
    
    /**
     * CartTotalItem constructor.
     *
     * @param string $code
     * @param string $title
     * @param float  $value
     * @param float  $changeTotal
     * @param float  $changeTax
     * @param float  $changeShippingCost
     */
    public function __construct(
        string $code,
        string $title,
        float $value,
        float $changeTotal,
        float $changeTax,
        float $changeShippingCost
    ) {
        $this->code               = $code;
        $this->title              = $title;
        $this->value              = $value;
        $this->changeTotal        = $changeTotal;
        $this->changeTax          = $changeTax;
        $this->changeShippingCost = $changeShippingCost;
    }
    
    
    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
    
    
    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
    
    
    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
    
    
    /**
     * @return float
     */
    public function getChangeTotal(): float
    {
        return $this->changeTotal;
    }
    
    
    /**
     * @return float
     */
    public function getChangeTax(): float
    {
        return $this->changeTax;
    }
    
    
    /**
     * @return float
     */
    public function getChangeShippingCost(): float
    {
        return $this->changeShippingCost;
    }
}
