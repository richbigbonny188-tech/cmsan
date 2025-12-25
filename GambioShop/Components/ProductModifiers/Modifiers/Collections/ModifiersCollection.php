<?php
/*--------------------------------------------------------------------------------------------------
    ModifiersCollection.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Modifiers\Collections;

use ArrayIterator;
use Gambio\Shop\ProductModifiers\Modifiers\ModifierInterface;
use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use InvalidArgumentException;

/**
 * Class ModifiersCollection
 * @package Gambio\Shop\ProductModifiers\Modifiers\Collections
 */
class ModifiersCollection implements ModifiersCollectionInterface
{
    
    /**
     * @var array
     */
    protected $modifiers = [];
    
    
    /**
     * ModifiersCollection constructor.
     *
     * @param $modifiers
     */
    public function __construct($modifiers = null)
    {
        if ($modifiers) {
            foreach ($modifiers as $modifier) {
                $this->addModifier($modifier);
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function addModifier(ModifierInterface $modifier)
    {
        $this->modifiers[] = $modifier;
    }
    
    
    /**
     * @inheritDoc
     */
    public function addModifiers(ModifiersCollectionInterface $modifiers)
    {
        foreach ($modifiers as $modifier) {
            $this->addModifier($modifier);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->modifiers);
    }
    
    
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->modifiers);
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->modifiers;
    }
    
    
    /**
     * @inheritDoc
     */
    public function removeModifier(ModifierInterface $modifier)
    {
        foreach ($this->modifiers as $key => $item) {
            if ($item === $modifier || ($modifier->id() && $item->id() && $item->id()->equals($modifier->id()))) {
                unset($this->modifiers[$key]);
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function getById(ModifierIdentifierInterface $id): ModifierInterface
    {
        foreach ($this->modifiers as $item) {
            if ($id->equals($item->id())) {
                return $item;
            }
        }
        throw new InvalidArgumentException('Invalid Modifier Identifier');
    }
}