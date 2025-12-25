<?php
/* --------------------------------------------------------------
 GXModulesJsonUtility.php 2020-03-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Compatibility;

use Gambio\Admin\Layout\Menu\EventListeners\GxModules\Json\JsonMenuFinder;
use Gambio\Admin\Layout\Menu\Models\Cache\Collections\MenuGroups;
use Gambio\Admin\Layout\Menu\Repositories\Cache\Implementation\MenuMapper;

/**
 * Class GXModulesJsonUtility
 * @package Gambio\Admin\Layout\Menu\Compatibility
 * @codeCoverageIgnore
 */
class GXModulesJsonUtility
{
    /**
     * @var JsonMenuFinder
     */
    private $finder;
    
    /**
     * @var MenuMapper
     */
    private $mapper;
    
    
    /**
     * GXModulesJsonUtility constructor.
     *
     * @param JsonMenuFinder $finder
     * @param MenuMapper     $mapper
     */
    public function __construct(JsonMenuFinder $finder, MenuMapper $mapper)
    {
        $this->finder = $finder;
        $this->mapper = $mapper;
    }
    
    
    /**
     * Parses json menu files from GXModules, so they can be cached for the legacy admin menu.
     *
     * @return array
     */
    public function parseJsonMenuFiles(): array
    {
        $menuFiles = $this->finder->findMenuFiles();
        $rawGroups = [];
        foreach ($menuFiles as $menuFile) {
            $menuGroups = new MenuGroups();
            $menuGroup  = json_decode(file_get_contents($menuFile), true);
            $this->mapper->map($menuGroups, $menuGroup);
            
            $rawGroups[] = $menuGroups->toArray();
        }
        if (count($rawGroups) === 0) {
            return [];
        }
        
        $rawGroups = array_merge(...$rawGroups);
        
        $groups = [];
        foreach ($rawGroups as $rawGroup) {
            $id          = $rawGroup['id'];
            $groups[$id] = array_merge($rawGroup, $groups[$id] ?? []);
        }
        
        return $groups;
    }
}