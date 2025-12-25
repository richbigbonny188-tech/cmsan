<?php
/* --------------------------------------------------------------
 ApiRouteRegistration.php 2022-09-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Api\Application\Bootstrapper;

use Gambio\Api\Application\BaseApiV3Action;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\ModuleRegistry\RouteLoader;
use Gambio\Core\Application\Routing\RouteCollector;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Cache\Services\CacheFactory;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

/**
 * Class ApiRouteRegistration
 *
 * @package Gambio\Api\Application\Bootstrapper
 */
class ApiRouteRegistration implements Bootstrapper
{
    private const CACHE_NAMESPACE = 'application_routes';
    private const CACHE_KEY       = 'api_routes';
    
    
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $application->registerShared(BaseApiV3Action::class);
        
        $environment    = $this->getEnvironment($application);
        $routeCollector = $this->getRouteCollector($application);
        
        $cacheFactory = $this->getCacheFactory($application);
        $cache        = $cacheFactory->createCacheFor(self::CACHE_NAMESPACE);
        
        $this->registerBaseAction($application);
        
        // only use cache if available and in non-dev environments
        if ($cache->has(self::CACHE_KEY) && !$environment->isDev()) {
            $routes = $cache->get(self::CACHE_KEY);
            $this->registerRoutes($routes, $routeCollector);
            
            return;
        }
        
        $routes = $this->getRoutes();
        $this->registerRoutes($routes, $routeCollector);
        
        if (!$environment->isDev()) {
            $cache->set(self::CACHE_KEY, $routes);
        }
    }
    
    
    /**
     * Registers the route files.
     *
     * It must be ensured that each element in $routes is a valid filepath, which is done
     * by using `array_filter($data, 'is_file')` in `::getRoutes`.
     *
     * @param array          $routes
     * @param RouteCollector $routeCollector
     *
     * @see ApiRouteRegistration::getRoutes()
     */
    private function registerRoutes(array $routes, RouteCollector $routeCollector): void
    {
        foreach ($routes as $route) {
            $routeCallback = include $route;
            if (is_callable($routeCallback)) {
                $routeCallback($routeCollector);
            }
        }
    }
    
    
    /**
     * Returns a list of absolute file paths for route files.
     *
     * Route files are determined by their location and filename. Files called `routes.php` in
     * Modules **and** Submodules of the `Gambio\Api` are loaded from the system.
     *
     * @return array
     */
    private function getRoutes(): array
    {
        $modulesDirectory = dirname(__DIR__, 2) . '/Modules';
        $routeLoader      = new RouteLoader();
        
        $moduleRoutes    = $routeLoader->loadModuleRoutes($modulesDirectory);
        $submoduleRoutes = $routeLoader->loadSubmoduleRoutes($modulesDirectory);
        
        return array_filter(array_merge($moduleRoutes, $submoduleRoutes), 'is_file');
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
     * Returns a slim application instance.
     *
     * @param Application $application
     *
     * @return RouteCollector
     */
    private function getRouteCollector(Application $application): RouteCollector
    {
        if ($application->has(RouteCollector::class) === false) {
            throw new RuntimeException('Route collector needs to be registered to register API routes.');
        }
        
        try {
            return $application->get(RouteCollector::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $type    = get_class($e);
            $message = 'Could not get ' . RouteCollector::class . " from DI-Container ($type): {$e->getMessage()}";
            throw new RuntimeException($message);
        }
    }
    
    /**
     * @param Application $application
     *
     * @return void
     */
    private function registerBaseAction(Application $application): void
    {
        $this->getRouteCollector($application)->get('/api.php/v3', BaseApiV3Action::class);
    }
}