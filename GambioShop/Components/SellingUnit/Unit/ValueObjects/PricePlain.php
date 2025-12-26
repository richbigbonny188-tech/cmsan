<?php
/*--------------------------------------------------------------------
 PricePlain.php 2020-2-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class PricePlain
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class PricePlain
{
    /**
     * @var float
     */
    protected $value;
    
    
    /**
     * PricePlain constructor.
     *
     * @param float $value
     */
    public function __construct(float $value)
    {
        $this->value = $value;
    }
    
    
    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }
}