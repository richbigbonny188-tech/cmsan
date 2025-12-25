<?php
/* --------------------------------------------------------------
 MenuGroups.php 2020-04-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cached\Collections;

use Gambio\Admin\Layout\Menu\Models\Cached\FavouritesHashList;
use Gambio\Admin\Layout\Menu\Models\Cached\MenuGroup;
use Gambio\Admin\Layout\Menu\Models\Cached\MenuItem;
use function array_merge;

/**
 * Class MenuGroups
 * @package Gambio\Admin\Layout\Menu\Models\Cached
 */
class MenuGroups
{
    /**
     * @var MenuGroup[]
     */
    private $groups = [];
    
    /**
     * @var MenuGroup
     */
    private $favouritesGroup;
    
    
    /**
     * Adds new menu group.
     *
     * @param MenuGroup $group
     */
    public function add(MenuGroup $group): void
    {
        if ($group->hasItems() && !$group->isFavourites()) {
            $this->groups[] = $group;
        }
        if ($group->isFavourites()) {
            $this->favouritesGroup = $group;
        }
    }
    
    
    /**
     * Iterates through all groups, finding as favourite marked menu items and adds them
     * to the favourites group.
     *
     * @param FavouritesHashList $favouritesList
     */
    public function applyFavourites(FavouritesHashList $favouritesList): void
    {
        foreach ($this->groups as $group) {
            foreach ($group->favourites($favouritesList) as $favourite) {
                $this->addFavourite($favourite);
            }
        }
    }
    
    
    /**
     * Adds an item to the favourites group.
     *
     * @param MenuItem $item
     */
    private function addFavourite(MenuItem $item): void
    {
        if ($this->favouritesGroup) {
            $this->favouritesGroup->addItem($item);
        }
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        foreach ($this->groups as $group) {
            $data[] = $group->toArray();
        }
        
        if ($this->favouritesGroup) {
            return array_merge([$this->favouritesGroup->toArray()], $data);
        }
        
        return $data;
    }
    
    
    /**
     * Resets internal collection data.
     */
    public function reset(): void
    {
        $this->groups          = [];
        $this->favouritesGroup = null;
    }
}