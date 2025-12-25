<?php
/*--------------------------------------------------------------------------------------------------
    OrderInfoQuantity.php 2020-3-9
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;


use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\IncludeReservedOnQuantityInterface;

/**
 * Class OrderInfoQuantity
 * @package Gambio\Shop\SellingUnit\Unit\ValueObjects
 */
class OrderInfoQuantity extends AbstractQuantity implements IncludeReservedOnQuantityInterface
{
}