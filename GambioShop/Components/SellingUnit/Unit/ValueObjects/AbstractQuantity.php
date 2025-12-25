<?php
/*--------------------------------------------------------------------
 Quantity.php 2020-03-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/
namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;

abstract class AbstractQuantity extends AbstractValidatableObject implements QuantityInterface
{
    /**
     * @var float
     */
    protected $quantity;
    /**
     * @var string
     */
    private $measureUnit;
    
    
    /**
     * Quantity constructor.
     *
     * @param float  $quantity
     * @param string $measureUnit
     */
    public function __construct(float $quantity, string $measureUnit = '')
    {
        $this->quantity = $quantity;
        $this->measureUnit = $measureUnit;
    }
    
    
    /**
     * @return float
     */
    public function value(): float
    {
        return $this->quantity;
    }
    
    
    /**
     * @return string
     */
    public function measureUnit(): string
    {
        return $this->measureUnit;
    }
}