<?php
/* --------------------------------------------------------------
 MenuGroups.php 2020-01-30
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
use Gambio\Admin\Layout\Menu\Models\Cache\MenuGroup;
use Gambio\Admin\Layout\Menu\Models\Cache\MenuSortTrait;
use function array_key_exists;
use function count;

/**
 * Class MenuGroups
 * @package Gambio\Admin\Layout\Menu\Models\Cache\Collections
 */
class MenuGroups
{
    use MenuSortTrait;
    
    /**
     * @var MenuGroup[]
     */
    private $items = [];
    
    
    /**
     * Adds a menu group.
     *
     * @param MenuGroup $menuGroup
     */
    public function add(MenuGroup $menuGroup): void
    {
        if (!$this->tryMerge($menuGroup)) {
            $this->items[$menuGroup->id()] = $menuGroup;
        }
    }
    
    
    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }
    
    
    /**
     * Resets the internal collection data.
     */
    public function reset(): void
    {
        $this->items = [];
    }
    
    
    /**
     * Sort collected menu groups and their items.
     */
    public function sort(): void
    {
        usort($this->items, $this->sortCallback());
        foreach ($this->items as $item) {
            $item->sort();
        }
    }
    
    
    /**
     * Filters menu group items.
     *
     * This function filter menu group items by using conditions, if available and performing
     * a check if the menu group should be used in the final structure, or is restricted by
     * some configurations.
     *
     * @param FilterFactory $filterFactory
     */
    public function filter(FilterFactory $filterFactory): void
    {
        foreach ($this->items as $key => $group) {
            $conditions = $group->conditions();
            
            if ($conditions) {
                foreach ($conditions as $condition) {
                    $filter = $filterFactory->create($condition);
                    if (!$filter->check($condition)) {
                        unset($this->items[$key]);
                        continue;
                    }
                }
            }
            
            $group->filterItems($filterFactory);
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
     * Merges the given menu group if another group with the same id is already collected.
     * Returns true if the group was merged and false otherwise.
     *
     * @param MenuGroup $group
     *
     * @return bool
     */
    private function tryMerge(MenuGroup $group): bool
    {
        if ($this->has($group)) {
            $this->items[$group->id()]->merge($group);
            
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Checks if any menu group with same id is already collected.
     *
     * @param MenuGroup $group
     *
     * @return bool
     */
    private function has(MenuGroup $group): bool
    {
        return array_key_exists($group->id(), $this->items);
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