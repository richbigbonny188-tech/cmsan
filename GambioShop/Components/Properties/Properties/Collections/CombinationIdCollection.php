<?php
/*--------------------------------------------------------------------------------------------------
    CombinationIdCollection.php 2022-08-05
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2022 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace Gambio\Shop\Properties\Properties\Collections;

use Gambio\Shop\Properties\Properties\ValueObjects\CombinationId;
use InvalidArgumentException;

class CombinationIdCollection implements CombinationIdCollectionInterface
{
    
    /**
     * @var int
     */
    protected $position = 0;
    /**
     * @var CombinationId[]
     */
    protected $values = [];
    
    
    /**
     * SellingUnitImagesCollection constructor.
     *
     * @param CombinationId[] $identifiers
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
        if (!$value instanceof CombinationId) {
            
            throw new InvalidArgumentException(static::class . ' only accepts ' . CombinationId::class);
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
     * @param CombinationId $value
     *
     * @return int
     */
    public function indexOf(CombinationId $value): int
    {
        foreach ($this->values as $key => $modifier) {
            if ($modifier->equals($value)) {
                return $key;
            }
        }
        
        return -1;
    }
    
}