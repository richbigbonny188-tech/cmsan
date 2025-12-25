<?php
/* --------------------------------------------------------------
  ProductModifierDisplayTypeCollection.php 2020-01-17
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2020 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

/**
 * Class ProductModifierDisplayTypeCollection
 */
class ProductModifierDisplayTypeCollection implements Countable, Iterator, ArrayAccess
{
    /**
     * @var AbstractProductModifierDisplayType[]
     */
    protected $values = [];
    
    /**
     * @var int
     */
    protected $position = 0;
    
    
    /**
     * ProductModifierDisplayTypeCollection constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        if (count($values)) {
            
            foreach ($values as $value) {
            
                $this->addProductModifierDisplayType($value);
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
    #[\ReturnTypeWillChange]
    public function key()
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
        if (!$value instanceof AbstractProductModifierDisplayType) {
    
            throw new InvalidArgumentException(static::class . ' only accepts '
                                               . AbstractProductModifierDisplayType::class);
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
     * @param AbstractProductModifierDisplayType $displayType
     */
    public function addProductModifierDisplayType(AbstractProductModifierDisplayType $displayType): void
    {
        $this->values[] = $displayType;
    }
    
    
    /**
     * @param string $name
     *
     * @return AbstractProductModifierDisplayType
     * @throws ProductModifierDisplayTypeNotFoundException
     */
    public function getProductModifierDisplayTypeByName(string $name): AbstractProductModifierDisplayType
    {
        foreach ($this->values as $displayType) {
            
            if ($displayType->name() === $name) {
                
                return $displayType;
            }
        }
        
        throw new ProductModifierDisplayTypeNotFoundException('No DisplayType found with the name: ' . $name);
    }
}