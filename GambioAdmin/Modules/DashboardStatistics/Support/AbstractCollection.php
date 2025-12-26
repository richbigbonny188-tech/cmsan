<?php
/*--------------------------------------------------------------
   AbstractCollection.php 2021-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\Support;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use JsonSerializable;

abstract class AbstractCollection implements Countable, Iterator, ArrayAccess, JsonSerializable
{
    /**
     * @var Object[]
     */
    protected $values = [];
    
    /**
     * @var int
     */
    protected $position = 0;
    
    
    /**
     * Constructor.
     */
    public function __construct(array $values = [])
    {
        if (!count($values)) {
            return;
        }
        
        foreach ($values as $value) {
            $this[] = $value;
        }
    }
    
    
    /**
     * Return current iteration value.
     */
    public function currentValue(): object
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
    public function offsetGet($offset): object
    {
        return $this->values[$offset];
    }
    
    
    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value): void
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException(static::class . ' does not accepts ' . get_class($value));
        }
        
        if (!empty($offset)) {
            $this->values[$offset] = $value;
            
            return;
        }
        
        $this->values[] = $value;
    }
    
    
    /**
     * Return whether the provided value is valid.
     */
    abstract protected function isValid($value): bool;
    
    
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
    public function jsonSerialize(): array
    {
        return $this->values;
    }
}