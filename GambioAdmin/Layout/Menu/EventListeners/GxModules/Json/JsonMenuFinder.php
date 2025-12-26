<?php
/* --------------------------------------------------------------
 JsonMenuFinder.php 2020-04-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\EventListeners\GxModules\Json;

use Gambio\Admin\Layout\Menu\EventListeners\GxModules\ModulesFinder;
use RecursiveDirectoryIterator as Directory;
use RecursiveIteratorIterator as Iterator;
use SplFileInfo;

/**
 * Class JsonMenuFinder
 * @package Gambio\Admin\Layout\Menu\EventListeners\GxModules\Json
 * @codeCoverageIgnore
 */
class JsonMenuFinder
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
     * Finds json menu files.
     *
     * All GXModules files with a ".menu.json" extension will be returned.
     *
     * @return array
     */
    public function findMenuFiles(): array
    {
        $modules   = $this->modulesFinder->findModules();
        $menuFiles = [];
        
        foreach ($modules as $module) {
            $iterator = new Iterator(new Directory($module, Directory::SKIP_DOTS), Iterator::LEAVES_ONLY);
            
            foreach ($iterator as $file) {
                /** @var SplFileInfo $file */
                $path = $file->getPathname();
                if ($this->endsWith('.menu.json', $path)) {
                    $menuFiles[] = $path;
                }
            }
        }
        
        return $menuFiles;
    }
    
    
    private function endsWith(string $searchFor, string $subject): bool
    {
        $length = strlen($searchFor);
        if ($length === 0) {
            return true;
        }
        
        return (substr($subject, -$length) === $searchFor);
    }
}