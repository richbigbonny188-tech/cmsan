<?php
/*--------------------------------------------------------------------------------------------------
    AttributeQuantity.php 2020-3-19
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2016 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Attributes\SellingUnitQuantitiy\ValueObjects;


use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollection;
use Gambio\Shop\ProductModifiers\Modifiers\Collections\ModifierIdentifierCollectionInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use Gambio\Shop\SellingUnit\Unit\ValueObjects\ModifierQuantityInterface;

class AttributeQuantity implements ModifierQuantityInterface
{
    /**
     * @var float
     */
    protected $value;
    /**
     * @var string
     */
    protected $measureUnit;
    /**
     * @var ModifierIdentifierCollection
     */
    /**
     * @var ModifierIdentifierCollection
     */
    protected $linkedModifiers;

    /**
     * AttributeQuantity constructor.
     * @param float $value
     * @param string $measureUnit
     * @param ModifierIdentifierInterface $id
     */
    public function __construct(float $value, string $measureUnit, ModifierIdentifierInterface $id)
    {
        $this->value = $value;
        $this->measureUnit = $measureUnit;
        $this->linkedModifiers = new ModifierIdentifierCollection([$id]);
    }

    /**
     * @inheritDoc
     */
    public function linkedModifiers(): ModifierIdentifierCollectionInterface
    {
        return $this->linkedModifiers;
    }

    /**
     * @inheritDoc
     */
    public function measureUnit(): string
    {
        return $this->measureUnit;
    }

    /**
     * @inheritDoc
     */
    public function value(): float
    {
        return $this->value;
    }
}