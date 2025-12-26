<?php
/*--------------------------------------------------------------------------------------------------
    ReserveScope.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;


use Gambio\Shop\Product\ValueObjects\ProductId;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\ReserveScopeInterface;

class ReserveScope implements ReserveScopeInterface
{
    /**
     * @var array
     */
    protected $collections = [];

    public function addIdentifier(SellingUnitId $id, float $quantity): void
    {
        $this->collections[] = ['id' => $id, 'quantity' => $quantity];
    }

    public function quantityFor(ProductId $productId, ModifierIdentifierCollectionInterface $collection): float
    {
        $result = 0.0;
        foreach ($this->collections as $element) {
            if ($element['id']->productId()->equals($productId) &&  $element['id']->modifiers()->contains($collection)) {
                $result += $element['quantity'];
            }
        }
        return $result;

    }
    public function quantityForModifier(ProductId $productId, ModifierIdentifierInterface $modifierIdentifier): float
    {
        $result = 0.0;
        foreach ($this->collections as $element) {
            if ($element['id']->productId()->equals($productId) &&  $element['id']->modifiers()->indexOf($modifierIdentifier)!==-1) {
                $result += $element['quantity'];
            }
        }
        return $result;

    }
}