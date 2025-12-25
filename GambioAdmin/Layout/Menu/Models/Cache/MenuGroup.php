<?php
/* --------------------------------------------------------------
 MenuGroup.php 2020-01-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 29 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Models\Cache;

use Gambio\Admin\Layout\Menu\Filter\Conditions;
use Gambio\Admin\Layout\Menu\Filter\FilterFactory;
use Gambio\Admin\Layout\Menu\Models\Cache\Collections\MenuItems;
use Webmozart\Assert\Assert;

/**
 * Class MenuGroup
 * @package Gambio\Admin\Layout\Menu\Models\Cache
 */
class MenuGroup implements Sortable
{
    /**
     * @var string
     */
    private $id;
    
    /**
     * @var string|null
     */
    private $title;
    
    /**
     * @var string|null
     */
    private $icon;
    
    /**
     * @var int|null
     */
    private $sortOrder;
    
    /**
     * @var string|null
     */
    private $brand;
    
    /**
     * @var string|null
     */
    private $type;
    
    /**
     * @var MenuItems
     */
    private $items;
    
    /**
     * @var Conditions|null
     */
    private $conditions;
    
    
    /**
     * MenuGroup constructor.
     *
     * @param string          $id
     * @param string|null     $title
     * @param string|null     $icon
     * @param int|null        $sortOrder
     * @param string|null     $brand
     * @param string|null     $type
     * @param MenuItems       $items
     * @param Conditions|null $conditions
     */
    private function __construct(
        string $id,
        ?string $title,
        ?string $icon,
        ?int $sortOrder,
        ?string $brand,
        ?string $type,
        MenuItems $items,
        Conditions $conditions = null
    ) {
        $this->id         = $id;
        $this->title      = $title;
        $this->icon       = $icon;
        $this->sortOrder  = $sortOrder;
        $this->brand      = $brand;
        $this->type       = $type;
        $this->items      = $items;
        $this->conditions = $conditions;
        
        $this->items->reset();
    }
    
    
    /**
     * Factory method to create MenuGroup from an array.
     *
     * @param array           $data
     * @param MenuItems       $items
     * @param Conditions|null $conditions
     *
     * @return static
     */
    public static function fromArray(array $data, MenuItems $items, Conditions $conditions = null): self
    {
        Assert::keyExists($data, 'id');
        
        $brand = $data['brand'] ?? null;
        $type  = $data['type'] ?? null;
        $sort  = $data['sort'] ?? null;
        if (!is_null($sort)) {
            $sort = (int)$sort;
        }
        
        return new static(
            $data['id'], $data['title'], $data['class'] ?? null, $sort, $brand, $type, $items, $conditions
        );
    }
    
    
    /**
     * Adds a menu item to the menu group.
     *
     * @param MenuItem $menuItem
     */
    public function add(MenuItem $menuItem): void
    {
        $this->items->add($menuItem);
    }
    
    
    /**
     * Filter condition.
     *
     * @return Conditions|null
     */
    public function conditions(): ?Conditions
    {
        return $this->conditions;
    }
    
    
    /**
     * Filters menu items.
     *
     * @param FilterFactory $filterFactory
     */
    public function filterItems(FilterFactory $filterFactory): void
    {
        $this->items->filter($filterFactory);
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder ? : 9999;
    }
    
    
    /**
     * Sort menu item collection.
     */
    public function sort(): void
    {
        $this->items->sort();
    }
    
    
    /**
     * Compares menu group with another one.
     *
     * @param MenuGroup $other
     *
     * @return bool
     */
    public function isEqual(self $other): bool
    {
        return $this->id === $other->id;
    }
    
    
    /**
     * Merges items with given menu group.
     *
     * @param MenuGroup $other
     */
    public function merge(self $other): void
    {
        $this->items->merge($other->items);
    }
    
    
    /**
     * Returns the group identifier.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }
    
    
    /**
     * Array serialization
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title ?? '',
            'class'     => $this->icon ?? '',
            'menuitems' => $this->items->toArray(),
            
            'brand' => $this->brand ?? '',
            'type'  => $this->type ?? '',
            'sort'  => $this->sortOrder
        ];
    }
}