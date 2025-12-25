<?php
/* --------------------------------------------------------------
   RouteLoader.php 2022-09-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\ModuleRegistry;

use DirectoryIterator;
use IteratorIterator;

/**
 * Class RouteLoader
 *
 * @package Gambio\Core\Application\ModuleRegistry
 */
class RouteLoader
{
    /**
     * Loads the absolute path of route files for modules.
     *
     * @param string $modulesDirectory
     *
     * @return array
     */
    public function loadModuleRoutes(string $modulesDirectory): array
    {
        $routes = [];
        foreach ($this->iterator($modulesDirectory) as $entry) {
            if ($entry->isDot() || $entry->isFile()) {
                continue;
            }
            $routesFile = "{$entry->getPathname()}/routes.php";
            if (is_file($routesFile)) {
                $routes[] = $routesFile;
            }
        }
        
        return $routes;
    }
    
    
    /**
     * Loads the absolute path of route files for submodules.
     *
     * @param string $modulesDirectory
     *
     * @return array
     */
    public function loadSubmoduleRoutes(string $modulesDirectory): array
    {
        $routes = [];
        foreach ($this->iterator($modulesDirectory) as $entry) {
            if ($entry->isDot() || $entry->isFile()) {
                continue;
            }
            $submodulesDirectory = "{$entry->getPathname()}/Submodules";
            if (is_dir($submodulesDirectory)) {
                foreach ($this->loadModuleRoutes($submodulesDirectory) as $route) {
                    $routes[] = $route;
                }
            }
        }
        
        return $routes;
    }
    
    
    /**
     * Iterates through a directory.
     *
     * @param string $path
     *
     * @return IteratorIterator|DirectoryIterator[]
     */
    private function iterator(string $path): IteratorIterator
    {
        return new IteratorIterator(new DirectoryIterator($path));
    }
}