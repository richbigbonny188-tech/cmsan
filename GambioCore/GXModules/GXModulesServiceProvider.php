<?php
/* --------------------------------------------------------------
   GXModulesServiceProvider.php 2020-11-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\GXModules;

use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\ClearCacheService;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\GXModules\App\Data\ActiveGXModulePathsProvider;
use Gambio\Core\GXModules\App\Data\ComponentsRegistryCache;
use Gambio\Core\GXModules\App\Data\ComponentsRegistryRepository;
use Gambio\Core\GXModules\App\Data\Processors\AdminMenuJsonProcessor;
use Gambio\Core\GXModules\App\Data\Processors\AutoloaderProcessor;
use Gambio\Core\GXModules\App\Data\Processors\GX4ModuleProcessor;
use Gambio\Core\GXModules\App\Data\Processors\GXModuleJsonProcessor;
use Gambio\Core\GXModules\App\Data\Processors\LanguageFileProcessor;
use Gambio\Core\GXModules\App\Data\Processors\RoutesProcessor;
use Gambio\Core\GXModules\App\Data\Processors\ServiceProviderProcessor;
use Gambio\Core\GXModules\App\Data\Processors\TemplateProcessor;
use Gambio\Core\GXModules\Model\Collections\AdminMenuJsonRegistry;
use Gambio\Core\GXModules\Model\Collections\AutoloaderRegistry;
use Gambio\Core\GXModules\Model\Collections\GX4ModuleRegistry;
use Gambio\Core\GXModules\Model\Collections\GXModuleJsonRegistry;
use Gambio\Core\GXModules\Model\Collections\LanguageFileRegistry;
use Gambio\Core\GXModules\Model\Collections\RoutesRegistry;
use Gambio\Core\GXModules\Model\Collections\ServiceProviderRegistry;
use Gambio\Core\GXModules\Model\Collections\TemplateRegistry;
use Gambio\Core\GXModules\Model\ComponentsRegistry;
use Gambio\Core\GXModules\Model\ValueObjects\AdminMenuJson;
use Gambio\Core\GXModules\Model\ValueObjects\Autoloader;
use Gambio\Core\GXModules\Model\ValueObjects\GX4Module;
use Gambio\Core\GXModules\Model\ValueObjects\GXModuleComponent;
use Gambio\Core\GXModules\Model\ValueObjects\GXModuleJson;
use Gambio\Core\GXModules\Model\ValueObjects\LanguageFile;
use Gambio\Core\GXModules\Model\ValueObjects\Routes;
use Gambio\Core\GXModules\Model\ValueObjects\ServiceProvider;
use Gambio\Core\GXModules\Model\ValueObjects\Template;
use Gambio\Core\GXModules\Services\GXModulesComponentsService;

/**
 * Class GXModulesServiceProvider
 *
 * @package Gambio\Core\GXModules
 */
class GXModulesServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            GXModulesComponentsService::class,
            ComponentsRegistryCache::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(GXModulesComponentsService::class, App\GXModulesComponentsService::class)
            ->addArgument(ComponentsRegistryRepository::class);
        
        $this->application->registerShared(ComponentsRegistryRepository::class)
            ->addArgument(ActiveGXModulePathsProvider::class)
            ->addArgument(ComponentsRegistryCache::class)
            ->addArguments([
                               AdminMenuJsonProcessor::class,
                               AutoloaderProcessor::class,
                               GX4ModuleProcessor::class,
                               GXModuleJsonProcessor::class,
                               LanguageFileProcessor::class,
                               RoutesProcessor::class,
                               ServiceProviderProcessor::class,
                               TemplateProcessor::class,
                           ]);
        
        $this->application->registerShared(ActiveGXModulePathsProvider::class)
            ->addArgument(ConfigurationFinder::class)
            ->addArgument(Path::class);
        
        $this->application->registerShared(ComponentsRegistryCache::class)->addArgument(CacheFactory::class);
        
        $this->application->registerShared(AdminMenuJsonProcessor::class);
        $this->application->registerShared(AutoloaderProcessor::class);
        $this->application->registerShared(GX4ModuleProcessor::class)->addArgument(Path::class);
        $this->application->registerShared(GXModuleJsonProcessor::class);
        $this->application->registerShared(LanguageFileProcessor::class);
        $this->application->registerShared(RoutesProcessor::class);
        $this->application->registerShared(ServiceProviderProcessor::class)->addArgument(Path::class);
        $this->application->registerShared(TemplateProcessor::class);
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(ClearCacheService::class)
            ->invokeMethod('addNamespaceToModuleCaches', ['gxmodules_components']);
        $this->application->inflect(CacheFactory::class)->invokeMethod('allowDeserializationOf',
                                                                       [
                                                                           ComponentsRegistry::class,
                                                                           GXModuleComponent::class,
                                                                           AdminMenuJsonRegistry::class,
                                                                           AdminMenuJson::class,
                                                                           AutoloaderRegistry::class,
                                                                           Autoloader::class,
                                                                           GX4ModuleRegistry::class,
                                                                           GX4Module::class,
                                                                           GXModuleJsonRegistry::class,
                                                                           GXModuleJson::class,
                                                                           LanguageFileRegistry::class,
                                                                           LanguageFile::class,
                                                                           RoutesRegistry::class,
                                                                           Routes::class,
                                                                           ServiceProviderRegistry::class,
                                                                           ServiceProvider::class,
                                                                           TemplateRegistry::class,
                                                                           Template::class,
                                                                       ]);
    }
}