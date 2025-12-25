<?php
/* --------------------------------------------------------------
 MenuGroupsMappedEvent.php 2020-02-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Events;

use Gambio\Admin\Layout\Menu\Models\Cache\Collections\MenuGroups;
use Gambio\Admin\Layout\Menu\Models\Cache\MenuGroup;

/**
 * Class BuildMenuCacheEvent
 * @package Gambio\Admin\Layout\Menu\Events
 *
 * @codeCoverageIgnore
 */
class CoreMenuDataCollected
{
    /**
     * @var MenuGroups
     */
    private $groups;
    
    
    /**
     * BuildMenuCacheEvent constructor.
     *
     * @param MenuGroups $groups
     */
    public function __construct(MenuGroups $groups)
    {
        $this->groups = $groups;
    }
    
    
    /**
     * Adds a new menu group.
     *
     * @param MenuGroup $group
     */
    public function addGroup(MenuGroup $group): void
    {
        $this->groups->add($group);
    }
}