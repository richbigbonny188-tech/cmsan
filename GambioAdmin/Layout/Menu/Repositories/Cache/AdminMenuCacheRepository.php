<?php
/* --------------------------------------------------------------
 AdminMenuCacheRepositoryInterface.php 2020-03-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Repositories\Cache;

use Gambio\Admin\Layout\Menu\Models\Cache\Collections\MenuGroups;

/**
 * Interface AdminMenuCacheRepositoryInterface
 * @package Gambio\Admin\Layout\Menu\Repositories\Cache
 */
interface AdminMenuCacheRepository
{
    /**
     * Checks if admin menu cache file is available.
     *
     * @return bool
     */
    public function hasCache(): bool;
    
    
    /**
     * Rebuilds the gambio admin menu cache file.
     *
     * @param MenuGroups $groups
     * @param array      $menuData
     */
    public function buildCache(MenuGroups $groups, array $menuData): void;
    
    
    /**
     * Provides cached gambio admin menu.
     *
     * @return array
     */
    public function getCache(): array;
    
    
    /**
     * Deletes the menu cache.
     */
    public function deleteCache(): void;
}