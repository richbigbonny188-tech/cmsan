<?php
/*--------------------------------------------------------------------------------------------------
    QuantitySurpassMaximumAllowedQuantityException.php 2020-3-9
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Unit\Exceptions;

use Throwable;

/**
 * Class QuantitySurpassMaximumAllowedQuantityException
 */
class QuantitySurpassMaximumAllowedQuantityException extends \Exception
{
    /**
     * @var float
     */
    private $maxQuantity;
    /**
     * @var int
     */
    private $productId;
    /**
     * @var float
     */
    private $requested;
    
    
    /**
     * QuantitySurpassMaximumAllowedQuantityException constructor.
     *
     * @param int            $productId
     * @param float          $requested
     * @param float          $maxQuantity
     * @param Throwable|null $previous
     */
    public function __construct(int $productId, float $requested, float $maxQuantity, Throwable $previous = null)
    {
        parent::__construct('', 0, $previous);
        $this->productId   = $productId;
        $this->requested   = $requested;
        $this->maxQuantity = $maxQuantity;
    }


    /**
     * @return float
     */
    public function maxQuantity(): float
    {
        return $this->maxQuantity;
    }


    /**
     * @return int
     */
    public function productId(): int
    {
        return $this->productId;
    }
    
    
    /**
     * @return float
     */
    public function requested(): float
    {
        return $this->requested;
    }
    
}