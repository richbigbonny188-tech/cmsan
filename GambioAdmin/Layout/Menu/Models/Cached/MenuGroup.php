<?php
/* --------------------------------------------------------------
 MenuGroup.php 2020-03-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cached;

use Gambio\Admin\Layout\Menu\Models\Cached\Collections\MenuItems;

/**
 * Class MenuGroup
 * @package Gambio\Admin\Layout\Menu\Models\Cached
 */
class MenuGroup
{
    /**
     * @var string
     */
    private $id;
    
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string
     */
    private $class;
    
    /**
     * @var string
     */
    private $brand;
    
    /**
     * @var string
     */
    private $type;
    
    /**
     * @var MenuItems
     */
    private $items;
    
    /**
     * @var bool
     */
    private $isFavourites;
    
    
    /**
     * MenuGroup constructor.
     *
     * @param string      $id
     * @param string      $title
     * @param string      $class
     * @param string|null $brand
     * @param string|null $type
     * @param MenuItems   $items
     */
    public function __construct(
        string $id,
        string $title,
        string $class,
        ?string $brand,
        ?string $type,
        MenuItems $items
    ) {
        $this->id           = $id;
        $this->title        = $title;
        $this->class        = $class;
        $this->brand        = $brand;
        $this->type         = $type;
        $this->items        = $items;
        $this->isFavourites = $this->id === 'BOX_HEADING_FAVORITES';
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'class'     => $this->class,
            'brand'     => $this->brand ?? '',
            'type'      => $this->type ?? '',
            'menuitems' => $this->items->toArray(),
            'isActive'  => $this->items->isActive() && !$this->isFavourites
        ];
    }
    
    
    /**
     * @return bool
     */
    public function hasItems(): bool
    {
        return $this->items->hasItems();
    }
    
    
    /**
     * @return bool
     */
    public function isFavourites(): bool
    {
        return $this->isFavourites;
    }
    
    
    /**
     * @param FavouritesHashList $favouritesList
     *
     * @return MenuItem[]
     */
    public function favourites(FavouritesHashList $favouritesList): array
    {
        return $this->items->favourites($favouritesList);
    }
    
    
    /**
     * Adds a new menu item.
     *
     * @param MenuItem $item
     */
    public function addItem(MenuItem $item): void
    {
        $this->items->add($item);
    }
}