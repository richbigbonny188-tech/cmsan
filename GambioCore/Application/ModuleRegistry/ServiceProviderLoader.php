<?php
/* --------------------------------------------------------------
   ServiceProviderLoader.php 2023-03-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\ModuleRegistry;

use DirectoryIterator;
use Gambio\Core\Application\ValueObjects\Path;
use IteratorIterator;
use function strlen;

/**
 * Class ServiceProviderLoader
 *
 * @package Gambio\Admin\Application\ModuleRegistry
 */
class ServiceProviderLoader
{
    /**
     * @var Path
     */
    private Path $path;
    
    
    /**
     * ServiceProviderLoader constructor.
     *
     * @param Path $path
     */
    public function __construct(Path $path)
    {
        $this->path = $path;
    }
    
    
    /**
     * Provides a list of
     *
     * @param string $modulesDirectory
     *
     * @return array
     */
    public function provideModulesServiceProviderSuffix(string $modulesDirectory): array
    {
        $modulePaths          = $this->getModulesPaths($modulesDirectory);
        $serviceProviderPaths = $this->findServiceProviders($modulePaths);
        
        return $this->changePathToNamespace($serviceProviderPaths);
    }
    
    
    /**
     * @param string $modulesDirectory
     *
     * @return array
     */
    public function provideSubmodulesServiceProviderSuffix(string $modulesDirectory): array
    {
        $modulePaths               = $this->getModulesPaths($modulesDirectory);
        $submodulePaths            = $this->findSubmodulePaths($modulePaths);
        $submoduleServiceProviders = $this->findServiceProviders($submodulePaths);
        
        return $this->changePathToNamespace($submoduleServiceProviders);
    }
    
    
    /**
     * Find submodule paths.
     * Searches in the given $modulesPaths array.
     *
     * @param array $modulePaths
     *
     * @return array
     */
    private function findSubmodulePaths(array $modulePaths): array
    {
        $submodulePaths = [];
        foreach ($modulePaths as $modulePath) {
            $submoduleDir = "$modulePath/Submodules";
            if (is_dir($submoduleDir)) {
                foreach ($this->dirIterator($submoduleDir) as $submoduleEntry) {
                    if ($submoduleEntry->isFile() || $submoduleEntry->isDot()) {
                        continue;
                    }
                    
                    $submodulePaths[] = $submoduleEntry->getPathname();
                }
            }
        }
        
        return $submodulePaths;
    }
    
    
    /**
     * Iterates through the given module paths and search for classes
     * ending with "ServiceProvider.php".
     *
     * @param array $modulePaths
     *
     * @return array
     */
    private function findServiceProviders(array $modulePaths): array
    {
        $serviceProviderPaths = [];
        foreach ($modulePaths as $modulePath) {
            foreach ($this->dirIterator($modulePath) as $moduleEntry) {
                if ($this->strEndsWithServiceProvider($moduleEntry->getBasename())) {
                    $serviceProviderPaths[] = $moduleEntry->getPathname();
                }
            }
        }
        
        return $serviceProviderPaths;
    }
    
    
    /**
     * Returns a list of module paths.
     *
     * @param string $modulesDirectory
     *
     * @return array
     */
    private function getModulesPaths(string $modulesDirectory): array
    {
        $modulePaths = [];
        foreach ($this->dirIterator($modulesDirectory) as $entry) {
            /** @var DirectoryIterator $entry */
            if ($entry->isFile() || $entry->isDot()) {
                continue;
            }
            $modulePaths[] = $entry->getPathname();
        }
        
        return $modulePaths;
    }
    
    
    /**
     * Modifies the given array of paths to service providers,
     * building the latter part of the service providers' namespace.
     *
     * @param array $submoduleServiceProviders
     *
     * @return array
     */
    private function changePathToNamespace(array $submoduleServiceProviders): array
    {
        $serviceProviders = [];
        foreach ($submoduleServiceProviders as $submoduleServiceProvider) {
            $serviceProviders[] = str_replace(["{$this->path->base()}/", '.php', '/'],
                                              ['', '', '\\'],
                                              $submoduleServiceProvider);
        }
        
        return $serviceProviders;
    }
    
    
    /**
     * Directory iterator utility method.
     *
     * @param string $path
     *
     * @return IteratorIterator|DirectoryIterator[]
     */
    private function dirIterator(string $path): IteratorIterator
    {
        return new IteratorIterator(new DirectoryIterator($path));
    }
    
    
    /**
     * Checks if $haystack ends with "ServiceProvider.php".
     *
     * @param string $haystack
     *
     * @return bool
     */
    private function strEndsWithServiceProvider(string $haystack): bool
    {
        $needle = 'ServiceProvider.php';
        
        return ('' !== $haystack && 0 === substr_compare($haystack, $needle, -strlen($needle)));
    }
}