<?php
/**
 * TaxInfo.php 2020-3-23
 * Gambio GmbH
 * http://www.gambio.de
 * Copyright (c) 2020 Gambio GmbH
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

/**
 * Class TaxInfo
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class TaxInfo
{
    /**
     * @var string
     */
    protected $taxInfoString;
    
    /**
     * @var float
     */
    protected $taxInfoFloat;
    
    /**
     * @var int
     */
    protected $taxClassId;
    
    
    /**
     * TaxInfo constructor.
     *
     * @param string $taxInfoString
     * @param float  $taxInfoFloat
     * @param int    $taxClassId
     */
    public function __construct(string $taxInfoString, float $taxInfoFloat, int $taxClassId)
    {
        $this->taxInfoString = $taxInfoString;
        $this->taxInfoFloat  = $taxInfoFloat;
        $this->taxClassId    = $taxClassId;
    }
    
    
    /**
     * @return string
     */
    public function asString(): string
    {
        return $this->taxInfoString;
    }
    
    
    /**
     * @return float
     */
    public function asFloat(): float
    {
        return $this->taxInfoFloat;
    }
    
    
    /**
     * @return int
     */
    public function taxClassId(): int
    {
        return $this->taxClassId;
    }
}