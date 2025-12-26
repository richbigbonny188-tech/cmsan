<?php
/* --------------------------------------------------------------
 XmlMenuFinder.php 2021-04-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\EventListeners\GxModules\Xml;

use Gambio\Admin\Layout\Menu\EventListeners\GxModules\ModulesFinder;

/**
 * Class XmlMenuFinder
 * @package    Gambio\Admin\Layout\Menu\EventListeners\GxModules\Xml
 * @deprecated Support for XML menu files will ends in upcoming feature version. Use JSON menu files instead.
 * @codeCoverageIgnore
 */
class XmlMenuFinder
{
    /**
     * @var ModulesFinder
     */
    private $modulesFinder;
    
    
    /**
     * XmlMenuFinder constructor.
     *
     * @param ModulesFinder $modulesFinder
     */
    public function __construct(ModulesFinder $modulesFinder)
    {
        $this->modulesFinder = $modulesFinder;
    }
    
    
    /**
     * Returns a list with all menu files of gx modules.
     *
     * @return array
     */
    public function findMenuFiles(): array
    {
        $modules   = $this->modulesFinder->findModules();
        $menuFiles = [];
        
        foreach ($modules as $module) {
            $menuDir = "{$module}/Admin/Menu";
            
            if (is_dir($menuDir)) {
                $menuFiles[] = glob("{$menuDir}/menu_*.xml");
            }
        }
        
        return array_merge(...$menuFiles);
    }
}