<?php
/* --------------------------------------------------------------
 ModulesFinder.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\EventListeners\GxModules;

use DirectoryIterator;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use IteratorIterator;

/**
 * Class ModulesFinder
 * @package Gambio\Admin\Layout\Menu\EventListeners\GxModules
 * @codeCoverageIgnore
 */
class ModulesFinder
{
    /**
     * @var Path
     */
    private $path;
    
    /**
     * @var ConfigurationFinder
     */
    private $configFinder;
    
    
    /**
     * ModulesFinder constructor.
     *
     * @param Path                $path
     * @param ConfigurationFinder $configFinder
     */
    public function __construct(Path $path, ConfigurationFinder $configFinder)
    {
        $this->path         = $path;
        $this->configFinder = $configFinder;
    }
    
    
    /**
     * Returns a list of all gx modules.
     *
     * @return array
     */
    public function findModules(): array
    {
        $gxModulesDir = "{$this->path->base()}/GXModules";
        
        $modules  = [];
        $iterator = new IteratorIterator(new DirectoryIterator($gxModulesDir));
        
        foreach ($iterator as $dir) {
            /** @var DirectoryIterator $dir */
            if ($dir->isDir() && !$dir->isDot()) {
                $vendor    = realpath($dir->getPathname());
                $modules[] = $this->vendorsModules($vendor);
            }
        }
        
        return array_merge(...$modules);
    }
    
    
    /**
     * Returns a list of modules for the given vendor dir.
     *
     * @param string $vendorDir
     *
     * @return array
     */
    private function vendorsModules(string $vendorDir): array
    {
        $modules  = [];
        $iterator = new IteratorIterator(new DirectoryIterator($vendorDir));
        
        foreach ($iterator as $dir) {
            /** @var DirectoryIterator $dir */
            if ($dir->isDir() && !$dir->isDot() && $this->checkIncludeFlagInGXModuleJson($dir)) {
                $modules[] = realpath($dir->getPathname());
            }
        }
        
        return $modules;
    }
    
    
    /**
     * @param DirectoryIterator $module
     *
     * @return bool
     */
    private function checkIncludeFlagInGXModuleJson(DirectoryIterator $module): bool
    {
        $gxmoduleJsonFile = $module->getPathname() . '/GXModule.json';
        if (file_exists($gxmoduleJsonFile)) {
            $gxmoduleJsonContent = json_decode(file_get_contents($gxmoduleJsonFile), true);
            if (isset($gxmoduleJsonContent['forceIncludingFiles'])
                && $gxmoduleJsonContent['forceIncludingFiles'] === false
                && json_last_error() === JSON_ERROR_NONE) {
                $vendorName = basename($module->getPath());
                $moduleName = $module->getBasename();
                
                return $this->configFinder->get("modules/{$vendorName}{$moduleName}/active", '0') === '1';
            }
        }
        
        return true;
    }
}