<?php
/* --------------------------------------------------------------
 RouteRegistration.php 2022-09-12
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Bootstrapper;

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
 * Class RouteRegistration
 *
 * @package Gambio\Admin\Application\Bootstrapper
 */
class RouteRegistration implements Bootstrapper
{
    private const CACHE_NAMESPACE = 'application_routes';
    private const CACHE_KEY       = 'admin_routes';
    
    
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $env            = $this->getEnvironment($application);
        $cacheFactory   = $this->getCacheFactory($application);
        $routeCollector = $this->getRouteCollector($application);
        
        $this->registerGambioAdminMenuRoutes($routeCollector);
        $cache = $cacheFactory->createCacheFor(self::CACHE_NAMESPACE);
        if ($cache->has(self::CACHE_KEY) && !$env->isDev()) {
            $routes = $cache->get(self::CACHE_KEY);
            $this->registerRoutes($routes, $routeCollector);
            
            return;
        }
        
        $routes = $this->getRoutes();
        $this->registerRoutes($routes, $routeCollector);
        
        if (!$env->isDev()) {
            $cache->set(self::CACHE_KEY, $routes);
        }
    }
    
    
    /**
     * Returns all the GambioAdmin application routes.
     *
     * There are routes for modules and submodules. Routes for modules are
     * loaded by looking into the module directory for a file called "routes.php".
     * Similar, the method checks if the module directory contains a submodule directory
     * with other route files and returns them too.
     *
     * @return array
     */
    private function getRoutes(): array
    {
        $routeLoader      = new RouteLoader();
        $modulesDirectory = dirname(__DIR__, 2) . '/Modules';
        
        $moduleRoutes    = $routeLoader->loadModuleRoutes($modulesDirectory);
        $submoduleRoutes = $routeLoader->loadSubmoduleRoutes($modulesDirectory);
        $routes          = array_merge($moduleRoutes, $submoduleRoutes);
        
        return array_filter($routes, 'is_file');
    }
    
    
    /**
     * Takes a list of absolute paths of route files and register them
     * to the application using the RouteCollector.
     *
     * @param array          $routes
     * @param RouteCollector $routeCollector
     *
     * @return void
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
     * Registers routes for the gambio admin menu system.
     *
     * @param RouteCollector $routeCollector
     */
    private function registerGambioAdminMenuRoutes(RouteCollector $routeCollector): void
    {
        $menuRoutesFile = dirname(__DIR__, 2) . '/Layout/routes.php';
        
        if (file_exists($menuRoutesFile)) {
            $routeCallback = include $menuRoutesFile;
            if (is_callable($routeCallback)) {
                $routeCallback($routeCollector);
            }
        }
    }
    
    
    /**
     * @param Application $application
     *
     * @return RouteCollector
     */
    private function getRouteCollector(Application $application): RouteCollector
    {
        if (!$application->has(RouteCollector::class)) {
            throw new RuntimeException('RouteCollector must be registered before this bootstrapper ca be executed');
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
}