<?php
/* --------------------------------------------------------------
 MenuItems.php 2020-04-21
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
use Gambio\Admin\Layout\Menu\Models\Cached\MenuItem;
use function count;

/**
 * Class MenuItems
 * @package Gambio\Admin\Layout\Menu\Models\Cached
 */
class MenuItems
{
    /**
     * @var MenuItem[]
     */
    private $items = [];
    
    /**
     * @var bool
     */
    private $isActive = false;
    
    
    /**
     * @param MenuItem $item
     */
    public function add(MenuItem $item): void
    {
        if ($item->isActive()) {
            $this->isActive = true;
        }
        
        $this->items[] = $item;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        foreach ($this->items as $item) {
            $data[] = $item->toArray();
        }
        
        return $data;
    }
    
    
    /**
     * @param FavouritesHashList $favouritesList
     *
     * @return MenuItem[]
     */
    public function favourites(FavouritesHashList $favouritesList): array
    {
        $favourites = [];
        foreach ($this->items as $item) {
            if ($favouritesList->isFavourite($item)) {
                $favourites[] = $item;
            }
        }
        
        return $favourites;
    }
    
    
    /**
     * Checks if collection contains an active item.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }
    
    
    /**
     * @return bool
     */
    public function hasItems(): bool
    {
        return count($this->items) > 0;
    }
}