<?php
/* --------------------------------------------------------------
   ScaledDiscount.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects;

/**
 * Class ScaledDiscount
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes\Products\ValueObjects
 */
class ScaledDiscount
{
    /**
     * @var int
     */
    private int $scaledQuantity;
    
    
    /**
     * @var float
     */
    private float $scaledPrice;
    
    
    /**
     * @var float
     */
    private float $scaledDPrice;
    
    
    /**
     * @param int   $scaledQuantity
     * @param float $scaledPrice
     * @param float $scaledDPrice
     */
    public function __construct(int $scaledQuantity, float $scaledPrice, float $scaledDPrice)
    {
        $this->scaledQuantity = $scaledQuantity;
        $this->scaledPrice    = $scaledPrice;
        $this->scaledDPrice   = $scaledDPrice;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'ScaledQuantity' => $this->scaledQuantity,
            'ScaledPrice'    => $this->scaledPrice,
            'ScaledDPrice'   => $this->scaledDPrice,
        ];
    }
    
    
    /**
     * @return int
     */
    public function getScaledQuantity(): int
    {
        return $this->scaledQuantity;
    }
    
    
    /**
     * @param int $scaledQuantity
     */
    public function setScaledQuantity(int $scaledQuantity): void
    {
        $this->scaledQuantity = $scaledQuantity;
    }
    
    
    /**
     * @return float
     */
    public function getScaledPrice(): float
    {
        return $this->scaledPrice;
    }
    
    
    /**
     * @param float $scaledPrice
     */
    public function setScaledPrice(float $scaledPrice): void
    {
        $this->scaledPrice = $scaledPrice;
    }
    
    
    /**
     * @return float
     */
    public function getScaledDPrice(): float
    {
        return $this->scaledDPrice;
    }
    
    
    /**
     * @param float $scaledDPrice
     */
    public function setScaledDPrice(float $scaledDPrice): void
    {
        $this->scaledDPrice = $scaledDPrice;
    }
    
}
