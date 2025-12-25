<?php
/* --------------------------------------------------------------
 MenuItems.php 2020-01-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 30 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cache\Collections;

use Gambio\Admin\Layout\Menu\Filter\FilterFactory;
use Gambio\Admin\Layout\Menu\Models\Cache\MenuItem;
use Gambio\Admin\Layout\Menu\Models\Cache\MenuSortTrait;
use function count;

/**
 * Class MenuItems
 * @package Gambio\Admin\Layout\Menu\Models\Cache\Collections
 */
class MenuItems
{
    use MenuSortTrait;
    
    /**
     * @var MenuItem[]
     */
    private $items = [];
    
    
    /**
     * Adds new menu item to collection.
     *
     * @param MenuItem $item
     */
    public function add(MenuItem $item): void
    {
        $this->items[] = $item;
    }
    
    
    /**
     * Checks if collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }
    
    
    /**
     * Flushes collected items.
     */
    public function reset(): void
    {
        $this->items = [];
    }
    
    
    /**
     * Sorts collection by items sort order.
     */
    public function sort(): void
    {
        usort($this->items, $this->sortCallback());
    }
    
    
    /**
     * Merges this with other menu items.
     *
     * @param MenuItems $other
     */
    public function merge(self $other): void
    {
        foreach ($other->items as $item) {
            $this->add($item);
        }
    }
    
    
    /**
     * Filters menu items.
     *
     * This function filter menu items by using conditions, if available and performing
     * a check if the menu group should be used in the final structure, or is restricted by
     * some configurations.
     *
     * @param FilterFactory $filterFactory
     */
    public function filter(FilterFactory $filterFactory): void
    {
        foreach ($this->items as $key => $item) {
            $conditions = $item->conditions();
            
            if ($conditions) {
                foreach ($conditions as $condition) {
                    $filter = $filterFactory->create($condition);
                    
                    if (!$filter->check($condition)) {
                        unset($this->items[$key]);
                    }
                }
            }
        }
    }
    
    
    /**
     * Array serialization.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        foreach ($this->items as $item) {
            $data[] = $item->toArray();
        }
        
        return $this->recursiveArrayUnique($data);
    }
    
    
    /**
     * Recursively filter the given array and returns a result with unique items only.
     *
     * @param array $array
     *
     * @return array
     */
    private function recursiveArrayUnique(array $array): array
    {
        return array_intersect_key($array, array_unique(array_map('serialize', $array)));
    }
}