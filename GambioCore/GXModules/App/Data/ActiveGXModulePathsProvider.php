<?php
/* --------------------------------------------------------------
   ActiveGXModulePathsProvider.php 2022-10-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data;

use DirectoryIterator;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use IteratorIterator;

/**
 * Class ActiveGXModulePathsProvider
 *
 * @package Gambio\Core\GXModules\App\Data
 */
class ActiveGXModulePathsProvider
{
    /**
     * @var ConfigurationFinder
     */
    private $configurationFinder;
    
    /**
     * @var Path
     */
    private $path;
    
    
    /**
     * ActiveGXModulePathsProvider constructor.
     *
     * @param ConfigurationFinder $configurationFinder
     * @param Path                $path
     */
    public function __construct(ConfigurationFinder $configurationFinder, Path $path)
    {
        $this->configurationFinder = $configurationFinder;
        $this->path                = $path;
    }
    
    
    /**
     * @return string[]
     */
    public function getActiveGXModulePaths(): array
    {
        $modules = [];
        foreach ($this->modules() as $module) {
            $configFile = "{$module}/GXModule.json";
            if (is_file($configFile) === false) {
                $modules[] = $module;
                continue;
            }
            
            $config = json_decode(file_get_contents($configFile), true);
            if ($config['forceIncludingFiles'] ?? false) {
                $modules[] = $module;
                continue;
            }
            
            $modulePathArray = explode('/', $module);
            $moduleName      = array_pop($modulePathArray);
            $vendor          = array_pop($modulePathArray);
            
            $namespace   = "modules/{$vendor}{$moduleName}";
            $configValue = $this->configurationFinder->get("{$namespace}/active", '0');
            if ($configValue === '1' || strtolower($configValue) === 'true') {
                $modules[] = $module;
            }
        }
        
        return $modules;
    }
    
    
    /**
     * Returns all GXModules module paths.
     *
     * @return array
     */
    private function modules(): array
    {
        $modules = [];
        
        foreach ($this->vendors() as $vendor) {
            foreach ($this->directoryIterator($vendor) as $module) {
                if ($module->isDir() && !$module->isDot()) {
                    $modules[] = $module->getPathname();
                }
            }
        }
        
        return $modules;
    }
    
    
    /**
     * Returns all GXModules vendor paths.
     *
     * @return array
     */
    private function vendors(): array
    {
        $gxModulesIterator = $this->directoryIterator("{$this->path->base()}/GXModules");
        $vendors           = [];
        
        foreach ($gxModulesIterator as $vendor) {
            if ($vendor->isDir() && !$vendor->isDot()) {
                $vendors[] = $vendor->getPathname();
            }
        }
        
        return $vendors;
    }
    
    
    /**
     * Utility method to create a directory iterator.
     *
     * @param string $path
     *
     * @return IteratorIterator|DirectoryIterator[]
     */
    private function directoryIterator(string $path): IteratorIterator
    {
        return new IteratorIterator(new DirectoryIterator($path));
    }
}