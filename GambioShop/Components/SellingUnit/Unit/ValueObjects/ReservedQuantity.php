<?php
/*--------------------------------------------------------------------------------------------------
    ReservedQuantity.php 2020-07-08
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2020 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\SellingUnit\Unit\ValueObjects;


use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollection;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\Interfaces\QuantityInterface;

class ReservedQuantity extends AbstractValidatableObject implements QuantityInterface
{

    /**
     * @var ModifierQuantityInterface[]
     */
    protected $quantities = [];
    /**
     * @var array
     */
    protected $sum = null;

    /**
     * @param ModifierQuantityInterface $quantity
     */
    public function addReserve(ModifierQuantityInterface $quantity): void
    {
        if (count($this->quantities) && $this->quantities[0]->measureUnit() !== $quantity->measureUnit()) {
            throw new \InvalidArgumentException('Invalid measure unit: ' . $quantity->measureUnit());
        }
        $this->quantities[] = $quantity;
        $this->sum = null;
    }

    /**
     * @param ModifierIdentifierInterface $id
     * @return ModifierQuantityInterface|null
     */
    public function byModifier(ModifierIdentifierInterface $id): ?ModifierQuantityInterface
    {
        $result = null;
        foreach($this->quantities as $quantity){
            foreach($quantity->linkedModifiers() as $linkedModifier){
                if($id->equals($linkedModifier))
                    return $quantity;
            }
        }
        return null;
    }

    /**
     *
     */
    protected function calculateTotals(): void
    {

        $this->sum = [];
        foreach ($this->quantities as $quantity) {
            $key = $this->indexNameForIdList($quantity->linkedModifiers());
            if (!isset($this->sum[$key])) {
                $this->sum[$key] = ['list' => $quantity->linkedModifiers(), 'quantity' => $quantity->value()];
            } else {
                $this->sum[$key]['quantity'] += $quantity->value();
            }
        }
    }

    public function byModifiers(ModifierIdentifierCollectionInterface $modifiers): float
    {
        $result = 0;
        foreach ($this->totals() as $quantity) {
            if ($modifiers->contains($quantity['list']) || $quantity['list']->contains($modifiers)) {
                if($result < $quantity['quantity'])
                {
                    $result = $quantity['quantity'];
                }
            }
        }
        return $result;

    }

    /**
     * @inheritDoc
     */
    public function measureUnit(): string
    {
        return count($this->quantities) ? $this->quantities[0]->measureUnit() : '';
    }

    /**
     * @inheritDoc
     */
    public function value(): float
    {
        $result = 0;
        foreach ($this->quantities as $quantity) {
            if ($result < $quantity->value()) {
                $result = $quantity->value();
            }
        }
        return $result;
    }

    /**
     * @param ModifierIdentifierCollectionInterface $modifiers
     * @return string
     */
    protected function indexNameForIdList(ModifierIdentifierCollectionInterface $modifiers): string
    {
        $key = '';
        foreach ($modifiers as $modifierIdentifier) {
            $key .= "_{$modifierIdentifier->type()}_{$modifierIdentifier->value()}";
        }
        return $key;
    }

    /**
     * @return array
     */
    protected function totals() : array
    {
        if($this->sum === null) {
            $this->calculateTotals();
        }
        return $this->sum;

    }
}