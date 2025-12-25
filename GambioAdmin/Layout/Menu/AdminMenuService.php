<?php
/* --------------------------------------------------------------
 AdminMenuService.php 2020-10-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 31 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu;

use Gambio\Admin\Layout\Menu\Repositories\Cache\AdminMenuCacheRepository;
use Gambio\Admin\Layout\Menu\Repositories\PostCache\Implementation\MenuProcessor;
use function file_get_contents;

/**
 * Class AdminMenuService
 * @package Gambio\Admin\Layout\Menu
 */
class AdminMenuService
{
    /**
     * @var AdminMenuCacheRepository
     */
    private $repository;
    
    /**
     * @var MenuProcessor
     */
    private $processor;
    
    /**
     * @var string|null
     */
    private $selectedAdminPage;
    
    
    /**
     * AdminMenuService constructor.
     *
     * @param AdminMenuCacheRepository $repository
     * @param MenuProcessor            $processor
     */
    public function __construct(
        AdminMenuCacheRepository $repository,
        MenuProcessor $processor
    ) {
        $this->repository = $repository;
        $this->processor  = $processor;
    }
    
    
    /**
     * Returns the finalized gambio admin menu structure.
     *
     * @return array
     */
    public function getAdminMenu(): array
    {
        $menuCache = $this->getAdminMenuCache();
        
        return $this->processor->process(new Models\Cached\Collections\MenuGroups(),
                                         $menuCache,
                                         $this->selectedAdminPage);
    }
    
    
    /**
     * Deletes all cache data of the admin menu.
     */
    public function deleteMenuCache(): void
    {
        $this->repository->deleteCache();
    }
    
    
    /**
     * @param string $adminPage
     */
    public function changeSelectedAdminPage(string $adminPage): void
    {
        $this->selectedAdminPage = $adminPage;
    }
    
    
    /**
     * Returns the admin menu cache.
     * If no cache data exists, new cache data will be created.
     *
     * @return array
     */
    private function getAdminMenuCache(): array
    {
        if (!$this->repository->hasCache()) {
            $menuJsonPath = __DIR__ . '/data/GambioAdminMenu.json';
            $menuJson     = file_get_contents($menuJsonPath);
            $menuData     = json_decode($menuJson, true);
            
            $this->repository->buildCache(new Models\Cache\Collections\MenuGroups(), $menuData);
        }
        
        return $this->repository->getCache();
    }
}