<?php
/*--------------------------------------------------------------------------------------------------
    ModifierDTOCollection.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers;

use ArrayIterator;

/**
 * Class ModifierDTOCollection
 * @package Gambio\Shop\ProductModifiers\Database\Core\DTO\Modifiers
 */
class ModifierDTOCollection implements ModifierDTOCollectionInterface
{
    /**
     * @var array
     */
    protected $modifiers = [];
    
    
    /**
     * @inheritDoc
     */
    public function addModifiers(ModifierDTOCollectionInterface $modifiers)
    {
        foreach ($modifiers as $modifier) {
            $this->addModifier($modifier);
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function addModifier(ModifierDTO $modifier)
    {
        $this->modifiers[] = $modifier;
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
}