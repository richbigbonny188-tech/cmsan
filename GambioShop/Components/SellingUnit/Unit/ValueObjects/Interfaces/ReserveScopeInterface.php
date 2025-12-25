<?php
/*--------------------------------------------------------------------------------------------------
    ReserveScopeInterface.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces;


use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\SellingUnitId;

interface ReserveScopeInterface
{
    /**
     * @param ProductId $productId
     * @param ModifierIdentifierCollectionInterface $collection
     * @return mixed
     */
    public function quantityFor(ProductId $productId,ModifierIdentifierCollectionInterface $collection): float;

    /**
     * @param ProductId $productId
     * @param ModifierIdentifierInterface $modifierIdentifier
     * @return float
     */
    public function quantityForModifier(ProductId $productId, ModifierIdentifierInterface $modifierIdentifier): float;

    /**
     * @param SellingUnitId $id
     * @param float $quantity
     * @return mixed
     */
    public function addIdentifier(SellingUnitId $id, float $quantity): void;
}