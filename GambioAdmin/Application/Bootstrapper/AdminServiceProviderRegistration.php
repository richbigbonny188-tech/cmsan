<?php
/* --------------------------------------------------------------
 AdminServiceProviderRegistration.php 2022-09-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Bootstrapper;

use Gambio\Admin\Application\ErrorHandling\AdminErrorServiceProvider;
use Gambio\Admin\Application\Http\AdminHttpServiceProvider;
use Gambio\Admin\Layout\Favorites\FavoritesServiceProvider;
use Gambio\Admin\Layout\Menu\AdminMenuServiceProvider;
use Gambio\Admin\Layout\Renderer\AdminRendererServiceProvider;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ModuleRegistry\ServiceProviderLoader;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Cache\Services\CacheFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Class AdminServiceProviderRegistration
 *
 * @package Gambio\Admin\Application\Bootstrapper
 */
class AdminServiceProviderRegistration implements Bootstrapper
{
    private const CACHE_NAMESPACE = 'application';
    private const CACHE_KEY       = 'admin_service_provider_registration';
    
    private const CORE_SERVICE_PROVIDERS = [
        FavoritesServiceProvider::class,
        AdminRendererServiceProvider::class,
        AdminMenuServiceProvider::class,
        AdminHttpServiceProvider::class,
        AdminErrorServiceProvider::class,
    ];
    
    
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $this->registerCoreServiceProvider($application);
        
        $cacheFactory = $this->getCacheFactory($application);
        $cache        = $cacheFactory->createCacheFor(self::CACHE_NAMESPACE);
        $environment  = $this->getEnvironment($application);
        
        if (!$environment->isDev() && $cache->has(self::CACHE_KEY)) {
            $serviceProviders = $cache->get(self::CACHE_KEY);
            $this->registerServiceProviders($serviceProviders, $application);
            
            return;
        }
        
        $modulesPath           = dirname(__DIR__, 2) . '/Modules';
        $serviceProviderLoader = $this->getServiceProviderLoader($application);
        
        $modulesSP           = $serviceProviderLoader->provideModulesServiceProviderSuffix($modulesPath);
        $submodulesSP        = $serviceProviderLoader->provideSubmodulesServiceProviderSuffix($modulesPath);
        $serviceProviderData = array_merge($modulesSP, $submodulesSP);
        
        $callback         = static fn(string $element): string => str_replace('GambioAdmin', 'Gambio\\Admin', $element);
        $serviceProviders = array_map($callback, $serviceProviderData);
        if (!$environment->isDev()) {
            $cache->set(self::CACHE_KEY, $serviceProviders);
        }
        
        foreach ($serviceProviders as $serviceProvider) {
            $application->registerProvider($serviceProvider);
        }
    }
    
    
    /**
     * Registers all the core admin service providers.
     *
     * @param Application $application
     */
    private function registerCoreServiceProvider(Application $application): void
    {
        $this->registerServiceProviders(self::CORE_SERVICE_PROVIDERS, $application);
    }
    
    
    /**
     * Tries to register service providers by using the provided list.
     * This method silently continues if anything fails on the registration process.
     *
     * @param array       $serviceProviders
     * @param Application $application
     */
    private function registerServiceProviders(array $serviceProviders, Application $application): void
    {
        foreach ($serviceProviders as $provider) {
            $application->registerProvider($provider);
        }
    }
    
    
    /**
     * @param Application $application
     *
     * @return CacheFactory
     */
    private function getCacheFactory(Application $application): CacheFactory
    {
        if (!$application->has(CacheFactory::class)) {
            throw new RuntimeException(CacheFactory::class . ' must be registered!');
        }
        
        try {
            return $application->get(CacheFactory::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $type    = get_class($e);
            $message = 'Could not get ' . Environment::class . " from DI-Container ($type): {$e->getMessage()}";
            throw new RuntimeException($message);
        }
    }
    
    
    /**
     * @param Application $application
     *
     * @return Environment
     */
    private function getEnvironment(Application $application): Environment
    {
        if (!$application->has(Environment::class)) {
            throw new RuntimeException(Environment::class . ' must be registered!');
        }
        
        try {
            return $application->get(Environment::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $type    = get_class($e);
            $message = 'Could not get ' . Environment::class . " from DI-Container ($type): {$e->getMessage()}";
            throw new RuntimeException($message);
        }
    }
    
    
    /**
     * Returns a service provider loader instance.
     *
     * @param Application $application
     *
     * @return ServiceProviderLoader
     */
    private function getServiceProviderLoader(Application $application): ServiceProviderLoader
    {
        if (!$application->has(Path::class)) {
            throw new RuntimeException(Path::class . ' must be registered first');
        }
        
        try {
            return new ServiceProviderLoader($application->get(Path::class));
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $type    = get_class($e);
            $message = 'Could not get ' . ServiceProviderLoader::class
                       . " from DI-Container ($type): {$e->getMessage()}";
            throw new RuntimeException($message);
        }
    }
}