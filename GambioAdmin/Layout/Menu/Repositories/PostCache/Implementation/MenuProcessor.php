<?php
/* --------------------------------------------------------------
 MenuProcessor.php 2020-03-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Repositories\PostCache\Implementation;

use Gambio\Admin\Layout\Menu\Factories\PostCacheMenuFactory;
use Gambio\Admin\Layout\Menu\Models\Cached\Collections\MenuGroups;
use Gambio\Admin\Layout\Menu\Repositories\PostCache\FavouritesHashListRepository;

/**
 * Class MenuProcessor
 * @package Gambio\Admin\Layout\Menu\Repositories\PostCache
 */
class MenuProcessor
{
    /**
     * @var FavouritesHashListRepository
     */
    private $favouritesRepository;
    
    /**
     * @var PostCacheMenuFactory
     */
    private $factory;
    
    
    /**
     * MenuProcessor constructor.
     *
     * @param FavouritesHashListRepository $favouritesRepository
     * @param PostCacheMenuFactory         $factory
     */
    public function __construct(
        FavouritesHashListRepository $favouritesRepository,
        PostCacheMenuFactory $factory
    ) {
        $this->favouritesRepository = $favouritesRepository;
        $this->factory              = $factory;
    }
    
    
    /**
     * Processes the menu cache data.
     *
     * This function checks if the current user has permissions to get the menu item displayed.
     * Additionally, it creates a url for the menu item and hashes the value,
     * so menu items can be added to the favourites.
     *
     * @param MenuGroups  $groups
     * @param array       $menuCacheData
     * @param string|null $connectedPage
     *
     * @return array
     */
    public function process(MenuGroups $groups, array $menuCacheData, string $connectedPage = null): array
    {
        $groups->reset();
        
        foreach ($menuCacheData as $groupData) {
            $items = $this->factory->createMenuItems($groupData['menuitems'], $connectedPage);
            $group = $this->factory->createMenuGroup($groupData, $items);
            
            $groups->add($group);
        }
        
        $favourites = $this->favouritesRepository->favouritesList();
        $groups->applyFavourites($favourites);
        
        return $groups->toArray();
    }
}