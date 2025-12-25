<?php
/* --------------------------------------------------------------
 ApiServiceProviderRegistration.php 2022-11-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Api\Application\Bootstrapper;

use Gambio\Api\Application\Auth\RequestAuthenticationServiceServiceProvider;
use Gambio\Api\Application\ErrorHandling\ApiErrorHandlerServiceProvider;
use Gambio\Api\Application\Middleware\ApiMiddlewareServiceProvider;
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
 * Class ApiServiceProviderRegistration
 *
 * @package Gambio\Api\Application\Bootstrapper
 */
class ApiServiceProviderRegistration implements Bootstrapper
{
    private const CACHE_NAMESPACE = 'application';
    private const CACHE_KEY       = 'api_service_provider_registration';
    
    private const API_COMPONENT_SERVICE_PROVIDERS = [
        RequestAuthenticationServiceServiceProvider::class,
        ApiMiddlewareServiceProvider::class,
        ApiErrorHandlerServiceProvider::class,
    ];
    
    
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $environment  = $this->getEnvironment($application);
        $cacheFactory = $this->getCacheFactory($application);
        $cache        = $cacheFactory->createCacheFor(self::CACHE_NAMESPACE);
        
        // api core service provider registration
        foreach (self::API_COMPONENT_SERVICE_PROVIDERS as $componentServiceProvider) {
            $application->registerProvider($componentServiceProvider);
        }
        
        // use cache if available and we are in a non development environment
        if ($cache->has(self::CACHE_KEY) && !$environment->isDev()) {
            $serviceProviders = $cache->get(self::CACHE_KEY, []);
            $this->registerServiceProvider($serviceProviders, $application);
            
            return;
        }
        
        // loads and registers the service providers
        $path             = $this->getPath($application);
        $serviceProviders = $this->getServiceProviders($path);
        $this->registerServiceProvider($serviceProviders, $application);
        
        // caches service providers if we are in a non development environment
        if (!$environment->isDev()) {
            $cache->set(self::CACHE_KEY, $serviceProviders);
        }
    }
    
    
    /**
     * Loads and returns service providers from the `GambioApi/Modules` directory.
     *
     * @param Path $path
     *
     * @return array
     */
    private function getServiceProviders(Path $path): array
    {
        $modulesDirectory = dirname(__DIR__, 2) . '/Modules';
        $loader           = new ServiceProviderLoader($path);
        
        $moduleServiceProvider    = $loader->provideModulesServiceProviderSuffix($modulesDirectory);
        $submoduleServiceProvider = $loader->provideSubmodulesServiceProviderSuffix($modulesDirectory);
        
        return array_merge($moduleServiceProvider, $submoduleServiceProvider);
    }
    
    
    /**
     * Registers the given list of service providers to the application.
     *
     * @param array       $serviceProviders
     * @param Application $application
     */
    private function registerServiceProvider(array $serviceProviders, Application $application): void
    {
        foreach ($serviceProviders as $serviceProvider) {
            $serviceProvider = str_replace('GambioApi', 'Gambio\\Api', $serviceProvider);
            $application->registerProvider($serviceProvider);
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
            throw new RuntimeException('CacheFactory must be registered before this bootstrapper ca be executed');
        }
        
        try {
            return $application->get(CacheFactory::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $type    = get_class($e);
            $message = 'Could not get ' . CacheFactory::class . " from DI-Container ($type): {$e->getMessage()}";
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
            throw new RuntimeException('Environment must be registered before this bootstrapper ca be executed');
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
     * @param Application $application
     *
     * @return Path
     */
    private function getPath(Application $application): Path
    {
        if (!$application->has(Path::class)) {
            throw new RuntimeException('Environment must be registered before this bootstrapper ca be executed');
        }
        
        try {
            return $application->get(Path::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $type    = get_class($e);
            $message = 'Could not get ' . Path::class . " from DI-Container ($type): {$e->getMessage()}";
            throw new RuntimeException($message);
        }
    }
}