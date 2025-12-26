<?php
/*--------------------------------------------------------------------------------------------------
    TotalAmount.php 2022-11-15
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class TotalAmount
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class TotalFinalPrice
{
    /**
     * @var float
     */
    protected float $total;
    
    
    /**
     * Total constructor.
     *
     * @param float $total
     */
    public function __construct(float $total)
    {
        $this->total = $total;
    }
    
    
    /**
     * @return float
     */
    public function value(): float
    {
        return $this->total;
    }
}
