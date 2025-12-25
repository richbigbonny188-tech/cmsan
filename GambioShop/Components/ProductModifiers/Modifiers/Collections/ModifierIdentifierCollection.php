<?php
/*--------------------------------------------------------------------------------------------------
    ModifierIdentifierCollection.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Shop\ProductModifiers\Modifiers\Collections;

use Gambio\Shop\ProductModifiers\Modifiers\ValueObjects\ModifierIdentifierInterface;
use InvalidArgumentException;

/**
 * Class ModifierIdentifierCollection
 * @package Gambio\Shop\ProductModifiers\Modifiers\Collections
 */
class ModifierIdentifierCollection implements ModifierIdentifierCollectionInterface
{
    /**
     * @var int
     */
    protected $position = 0;
    /**
     * @var ModifierIdentifierInterface[]
     */
    protected $values = [];
    
    
    /**
     * SellingUnitImagesCollection constructor.
     *
     * @param ModifierIdentifierInterface[] $identifiers
     */
    public function __construct(array $identifiers = [])
    {
        if (count($identifiers)) {
            
            foreach ($identifiers as $identifier) {
                
                $this[] = $identifier;
            }
        }
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->values[$this->position];
    }
    
    
    /**
     * @inheritDoc
     */
    public function next(): void
    {
        $this->position++;
    }
    
    
    /**
     * @inheritDoc
     */
    public function key(): int
    {
        return $this->position;
    }
    
    
    /**
     * @inheritDoc
     */
    public function valid(): bool
    {
        return isset($this->values[$this->position]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function rewind(): void
    {
        $this->position = 0;
    }
    
    
    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->values[$offset]);
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->values[$offset];
    }
    
    
    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        if (!$value instanceof ModifierIdentifierInterface) {
            
            throw new InvalidArgumentException(static::class . ' only accepts ' . ModifierIdentifierInterface::class);
        }
        
        if (empty($offset)) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }
    
    
    /**
     * @inheritDoc
     */
    public function offsetUnset($offset): void
    {
        unset($this->values[$offset]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->values);
    }
    
    
    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->values;
    }
    
    
    /**
     * @param ModifierIdentifierInterface $value
     *
     * @return int
     */
    public function indexOf(ModifierIdentifierInterface $value): int
    {
        foreach ($this->values as $key => $modifier) {
            if ($modifier->equals($value)) {
                return $key;
            }
        }
        
        return -1;
    }

    public function contains(ModifierIdentifierCollectionInterface $list): bool
    {
        $result = true;
        foreach ($list as $value) {
            if ($this->indexOf($value) < 0) {
                $result = false;
            }
        }
        return $result;
    }
}