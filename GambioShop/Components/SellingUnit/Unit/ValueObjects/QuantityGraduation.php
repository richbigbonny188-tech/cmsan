<?php
/*--------------------------------------------------------------------------------------------------
    QuantityGraduation.php 2020-3-3
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;

use InvalidArgumentException;

/**
 * Class QuantityGraduation
 */
class QuantityGraduation extends AbstractQuantity
{
    /**
     * @var float
     */
    protected $value;


    /**
     * QuantityGraduation constructor.
     *
     * @param float $quantity
     * @param string $measureUnit
     */
    public function __construct(float $quantity, string $measureUnit = '')
    {
        parent::__construct($quantity, $measureUnit);
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Granulated quantity mus be bigger than 0');
        }
    }
}