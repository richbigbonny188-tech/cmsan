<?php
/*--------------------------------------------------------------------------------------------------
    RequestedQuantityBelowMinimumException.php 2020-3-9
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Unit\Exceptions;

use Exception;

/**
 * Class RequestedQuantityBelowMinimumException
 */
class RequestedQuantityBelowMinimumException extends Exception
{
    /**
     * @var float
     */
    private $minOrder;
    /**
     * @var int
     */
    private $productId;
    /**
     * @var float
     */
    private $requested;
    
    
    /**
     * RequestedQuantityBelowMinimumException constructor.
     *
     * @param int       $productId
     * @param float     $requested
     * @param float     $minOrder
     * @param Exception $previous
     */
    public function __construct(int $productId, float $requested, float $minOrder, ?Exception $previous)
    {
        parent::__construct('', 0, $previous);
        $this->productId = $productId;
        $this->requested = $requested;
        $this->minOrder  = $minOrder;
    }
    
    
    /**
     * @return float
     */
    public function minOrder(): float
    {
        return $this->minOrder;
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