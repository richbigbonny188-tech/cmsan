<?php
/* --------------------------------------------------------------
 JsonMenuListener.php 2020-03-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\EventListeners\GxModules\Json;

use Gambio\Admin\Layout\Menu\Events\CoreMenuDataCollected;
use Gambio\Admin\Layout\Menu\Repositories\Cache\Implementation\MenuMapper;

/**
 * Class JsonMenuListener
 * @package Gambio\Admin\Layout\Menu\EventListeners\GxModules\Json
 * @codeCoverageIgnore
 */
class JsonMenuListener
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
     * JsonMenuListener constructor.
     *
     * @param JsonMenuFinder $finder
     * @param MenuMapper     $mapper
     */
    public function __construct(JsonMenuFinder $finder, MenuMapper $mapper)
    {
        $this->finder = $finder;
        $this->mapper = $mapper;
    }
    
    
    public function __invoke(CoreMenuDataCollected $event): CoreMenuDataCollected
    {
        $menuFiles = $this->finder->findMenuFiles();
        
        foreach ($menuFiles as $menuFile) {
            $menuData = json_decode(file_get_contents($menuFile), true);
            
            foreach ($menuData ?? [] as $menuGroup) {
                $group = $this->mapper->mapGroup($menuGroup);
                $event->addGroup($group);
            }
        }
        
        return $event;
    }
}