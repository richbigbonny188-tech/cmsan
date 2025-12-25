<?php
/*--------------------------------------------------------------------------------------------------
    SellingUnitRepositoryInterface.php 2020-3-3
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */


namespace Gambio\Shop\SellingUnit\Unit;

use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SelectedQuantity;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

/**
 * Interface SellingUnitRepositoryInterface
 * @package Gambio\Shop\SellingUnit\Unit
 */
interface SellingUnitRepositoryInterface
{
    
    /**
     * @param SellingUnitId         $id
     * @param null                  $product
     * @param null                  $xtcPrice
     * @param QuantityInterface|null $quantity
     *
     * @return SellingUnitInterface
     */
    public function getSellingUnitBy(
        SellingUnitId $id,
        $product = null,
        $xtcPrice = null,
        QuantityInterface $quantity = null
    ): SellingUnitInterface;
    
}