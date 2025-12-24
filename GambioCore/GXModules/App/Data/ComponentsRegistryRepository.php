<?php
/* --------------------------------------------------------------
   ComponentsRegistryRepository.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules\App\Data;

use Gambio\Core\GXModules\App\Data\Processors\GXModuleComponentProcessor;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Webmozart\Assert\Assert;

/**
 * Class ComponentsRegistryRepository
 *
 * @package Gambio\Core\GXModules\App\Data
 */
class ComponentsRegistryRepository
{
    /**
     * @var ActiveGXModulePathsProvider
     */
    private $pathsProvider;
    
    /**
     * @var ComponentsRegistryCache
     */
    private $registryCache;
    
    /**
     * @var GXModuleComponentProcessor[]
     */
    private $processors;
    
    /**
     * @var string[]
     */
    private $availableRegistries;
    
    
    /**
     * ComponentsRegistryRepository constructor.
     *
     * @param ActiveGXModulePathsProvider $pathsProvider
     * @param ComponentsRegistryCache     $registryCache
     * @param GXModuleComponentProcessor  ...$processors
     */
    public function __construct(
        ActiveGXModulePathsProvider $pathsProvider,
        ComponentsRegistryCache $registryCache,
        GXModuleComponentProcessor ...$processors
    ) {
        $this->pathsProvider = $pathsProvider;
        $this->registryCache = $registryCache;
        foreach ($processors as $processor) {
            $this->addComponentProcessor($processor);
        }
    }
    
    
    /**
     * @param string $type
     *
     * @return ComponentsRegistry
     */
    public function getRegistry(string $type): ComponentsRegistry
    {
        Assert::oneOf($type,
                      $this->availableRegistries,
                      'Invalid type provided. Must be one of: ' . implode(', ', $this->availableRegistries));
        
        if ($this->registryCache->has($type) === false) {
            $this->buildRegistryCache();
        }
        
        return $this->registryCache->get($type);
    }
    
    
    /**
     * @param GXModuleComponentProcessor $processor
     */
    public function addComponentProcessor(GXModuleComponentProcessor $processor): void
    {
        $this->availableRegistries[] = $processor->getType();
        $this->processors[]          = $processor;
        
        $this->availableRegistries = array_unique($this->availableRegistries);
        $this->processors          = array_unique($this->processors, SORT_REGULAR);
    }
    
    
    private function buildRegistryCache(): void
    {
        foreach ($this->processors as $processor) {
            $processor->resetInternalState();
        }
        
        foreach ($this->pathsProvider->getActiveGXModulePaths() as $modulePath) {
            foreach ($this->recursiveDirectoryIterator($modulePath) as $moduleFile) {
                foreach ($this->processors as $processor) {
                    $processor->processModuleFile($moduleFile);
                }
            }
        }
        
        foreach ($this->processors as $processor) {
            $this->registryCache->set($processor->getType(), $processor->createRegistry());
        }
    }
    
    
    /**
     * @param string $path
     *
     * @return RecursiveIteratorIterator|SplFileInfo[]
     */
    private function recursiveDirectoryIterator(string $path): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path,
                                                                            RecursiveDirectoryIterator::SKIP_DOTS),
                                             RecursiveIteratorIterator::LEAVES_ONLY);
    }
}