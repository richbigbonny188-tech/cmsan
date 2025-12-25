<?php
/* --------------------------------------------------------------
   UpdateDetailsCollection.php 2022-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed\Services\ShopInformation\Collections;

use ArrayIterator;
use Countable;
use Gambio\AdminFeed\Services\ShopInformation\ValueObjects\UpdateDetails;
use IteratorAggregate;
use Traversable;

/**
 * Class UpdateDetailsCollection
 *
 * @package Gambio\AdminFeed\Services\ShopInformation\Collections
 */
class UpdateDetailsCollection implements IteratorAggregate, Countable
{
    /**
     * @var array
     */
    private $items = [];
    
    
    /**
     * UpdateDetailsCollection constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }
    
    
    /**
     * Creates and returns a new UpdateDetailsCollection instance.
     *
     * @param array $items
     *
     * @return UpdateDetailsCollection
     */
    static function create(array $items = [])
    {
        return new self($items);
    }
    
    
    /**
     * Returns a list of all contained collection items.
     *
     * @return array
     */
    public function items()
    {
        return $this->items;
    }
    
    
    /**
     * Returns iterator for this collection.
     *
     * @return ArrayIterator|Traversable
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }
    
    
    /**
     * Returns the number of contained items.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }
    
    
    /**
     * Adds an item to this collection.
     *
     * @param UpdateDetails $item
     */
    public function add(UpdateDetails $item)
    {
        $this->items[] = $item;
    }
}