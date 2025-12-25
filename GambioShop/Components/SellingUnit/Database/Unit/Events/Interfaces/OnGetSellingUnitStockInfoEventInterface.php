<?php
/*--------------------------------------------------------------------------------------------------
    OnGetSellingUnitStockInfoEventInterface.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Database\Unit\Events\Interfaces;


use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitStockInterface;

interface OnGetSellingUnitStockInfoEventInterface
{

    /**
     * @param SellingUnitStockInterface $stock
     */
    public function setStock(SellingUnitStockInterface $stock) : void;

    /**
     * @return SellingUnitStockInterface
     */
    public function stock(): SellingUnitStockInterface;

    /**
     * @return QuantityInterface
     */
    public function requestedQuantity(): QuantityInterface;
}