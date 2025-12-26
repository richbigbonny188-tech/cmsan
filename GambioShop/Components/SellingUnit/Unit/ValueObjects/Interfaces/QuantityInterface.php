<?php
/*--------------------------------------------------------------------------------------------------
    QuantityInterface.php 2020-3-3
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces;


interface QuantityInterface
{
    /**
     * @return float
     */
    public function value(): float;

    /**
     * @return string
     */
    public function measureUnit(): string;
}