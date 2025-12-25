<?php
/* --------------------------------------------------------------
 MenuMapper.php 2020-01-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 30 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Repositories\Cache\Implementation;

use Gambio\Admin\Layout\Menu\Factories\CacheMenuFactory;
use Gambio\Admin\Layout\Menu\Models\Cache\Collections\MenuGroups;
use Gambio\Admin\Layout\Menu\Models\Cache\MenuGroup;

/**
 * Class MenuMapper
 * @package Gambio\Admin\Layout\Menu\Repositories\Cache
 */
class MenuMapper
{
    /**
     * @var CacheMenuFactory
     */
    private $menuFactory;
    
    
    /**
     * MenuMapper constructor.
     *
     * @param CacheMenuFactory $menuFactory
     */
    public function __construct(CacheMenuFactory $menuFactory)
    {
        $this->menuFactory = $menuFactory;
    }
    
    
    /**
     * Maps given $menuData to $groups.
     *
     * This function adds the data from $menuData to the passed MenuGroups collection.
     * Right before this happens, the collection gets reset to work on a clean state.
     * If the function succeeds, $groups contains all data in a well format.
     *
     * @param MenuGroups $groups
     * @param array      $menuData
     */
    public function map(MenuGroups $groups, array $menuData): void
    {
        $groups->reset();
        
        foreach ($menuData as $menuGroup) {
            $group = $this->mapGroup($menuGroup);
            $groups->add($group);
        }
    }
    
    
    public function mapGroup(array $menuGroup): MenuGroup
    {
        $conditions = $this->menuFactory->createConditions($menuGroup);
        $group      = $this->menuFactory->createMenuGroup($menuGroup, $conditions);
        
        foreach ($menuGroup['items'] as $menuItem) {
            $conditions = $this->menuFactory->createConditions($menuItem);
            $item       = $this->menuFactory->createMenuItem($menuItem, $conditions);
            
            $group->add($item);
        }
        
        return $group;
    }
}