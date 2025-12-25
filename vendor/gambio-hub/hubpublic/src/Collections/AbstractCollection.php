<?php
/* --------------------------------------------------------------
   AbstractCollection.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Collections;

use Countable;
use HubPublic\Exceptions\InvalidCollectionItemException;

/**
 * Class AbstractCollection
 *
 * @package HubPublic\Collections
 */
abstract class AbstractCollection implements Countable
{
    /**
     * Content Collection
     *
     * @var array
     */
    protected $items = [];
    
    
    /**
     * Add an item to the collection.
     *
     * @param mixed $item Item which should be added to the collection
     *
     * @return $this|AbstractCollection Same instance for chained method calls.
     * @throws \HubPublic\Exceptions\InvalidCollectionItemException if item is not valid.
     *
     */
    public function add($item): AbstractCollection
    {
        if (!$this->_isValid($item)) {
            throw new InvalidCollectionItemException($this->_getExceptionMessage($item));
        }
        $this->items[] = $item;
        
        return $this;
    }
    
    
    /**
     * Get the collection as an array.
     *
     * @return array Collection in array format
     */
    public function asArray(): array
    {
        return $this->items;
    }
    
    
    /**
     * Count elements of collection items.
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     */
    public function count(): int
    {
        return count($this->items);
    }
    
    
    /**
     * Validate the item.
     *
     * @param mixed $item Item which should be validated
     *
     * @return bool true if $item is valid | false if $item is invalid
     */
    protected function _isValid($item): bool
    {
        return is_object($item) && is_a($item, $this->_getValidType());
    }
    
    
    /**
     * Build and return the exception message for invalid item types.
     *
     * @param mixed $item Invalid item for which the exception message is generated.
     *
     * @return string Exception message
     */
    protected function _getExceptionMessage($item): string
    {
        return 'Invalid item type "' . gettype($item) . '", expected "' . $this->_getValidType() . '".';
    }
    
    
    /**
     * Return the valid type of an item.
     *
     * @return string Valid Type
     */
    abstract protected function _getValidType(): string;
}
