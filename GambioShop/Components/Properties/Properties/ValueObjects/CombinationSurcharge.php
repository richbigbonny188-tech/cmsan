<?php
/*--------------------------------------------------------------------------------------------------
    CombinationSurcharge.php 2020-03-18
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Properties\Properties\ValueObjects;


class CombinationSurcharge
{
    /**
     * @var bool
     */
    protected $nonLinear;
    /**
     * @var float
     */
    protected $value;

    /**
     * CombinationSurcharge constructor.
     * @param float $value
     * @param bool $nonLinear
     */
    public function __construct(float $value, bool $nonLinear)
 {
     $this->nonLinear = $nonLinear;
     $this->value = $value;
 }

    /**
     * @return bool
     */
    public function isNonLinear(): bool
    {
        return $this->nonLinear;
    }

    /**
     * @return float
     */
    public function value(): float
    {
        return $this->value;
    }
}