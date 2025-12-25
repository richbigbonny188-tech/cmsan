<?php
/* --------------------------------------------------------------
 ModuleRegistration.php 2020-10-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\Bootstrapper;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\Modules\Module;
use Gambio\Core\Application\Routing\RouteCollector;
use Gambio\Core\GXModules\Services\GXModulesComponentsService;
use RuntimeException;
use Throwable;
use function Gambio\Core\Logging\logger;

/**
 * Class ModuleRegistration
 *
 * @package Gambio\Core\Application\Bootstrapper
 */
class ModuleRegistration implements Bootstrapper
{
    private const LOGGER_NAMESPACE = 'module-registration';
    
    
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $componentsService = $this->getGXModulesComponentsService($application);
        $routeCollector    = $this->getRouteCollector($application);
        if (!$componentsService || !$routeCollector) {
            throw new RuntimeException('Module registration bootstrapper must be executed with available '
                                       . 'GXModules components service and route collector.');
        }
        
        $this->registerAutoloader($componentsService);
        $this->registerServiceProvider($application, $componentsService);
        $this->registerRoutes($routeCollector, $componentsService);
        
        foreach ($componentsService->getGX4Modules() as $gx4Module) {
            try {
                $module = $gx4Module->createClass();
                $this->registerModule($module, $application);
            } catch (Throwable $throwable) {
                $this->handleError($throwable, $gx4Module->className());
            }
        }
    }
    
    
    /**
     * Registers autoloader of external modules.
     *
     * @param GXModulesComponentsService $componentsService
     */
    private function registerAutoloader(GXModulesComponentsService $componentsService): void
    {
        foreach ($componentsService->getAutoloaderFiles() as $autoloaderPath) {
            /** @noinspection PhpIncludeInspection */
            require_once $autoloaderPath->filePath();
        }
    }
    
    
    /**
     * Registers service providers of external modules.
     *
     * @param Application                $application
     * @param GXModulesComponentsService $componentsService
     */
    private function registerServiceProvider(
        Application $application,
        GXModulesComponentsService $componentsService
    ): void {
        foreach ($componentsService->getServiceProviders() as $serviceProvider) {
            $application->registerProvider($serviceProvider->className());
        }
    }
    
    
    /**
     * Registers the routes of external modules.
     *
     * @param RouteCollector             $routeCollector
     * @param GXModulesComponentsService $componentsService
     */
    private function registerRoutes(RouteCollector $routeCollector, GXModulesComponentsService $componentsService): void
    {
        foreach ($componentsService->getRoutes() as $routeFile) {
            /** @noinspection PhpIncludeInspection */
            $callback = include $routeFile->filePath();
            $callback($routeCollector);
        }
    }
    
    
    /**
     * Module registration.
     *
     * This method takes the module definitions and registers the custom functionality for
     * the application.
     *
     * @param Module      $module
     * @param Application $application
     */
    private function registerModule(Module $module, Application $application): void
    {
        $dependencies = $module->dependsOn();
        if ($dependencies) {
            $missing = [];
            foreach ($dependencies as $dependency) {
                if (!$application->has($dependency)) {
                    $missing[] = $dependency;
                }
            }
            
            if (!empty($missing)) {
                $this->logMissingDependencies($module, $missing);
                
                return;
            }
        }
        
        $this->registerEventListener($module, $application);
    }
    
    
    /**
     * Registers the module's event listener.
     *
     * @param Module      $module
     * @param Application $application
     */
    private function registerEventListener(Module $module, Application $application): void
    {
        foreach ($module->eventListeners() ?? [] as $event => $listeners) {
            foreach ($listeners as $listener) {
                $application->attachEventListener($event, $listener);
            }
        }
    }
    
    
    /**
     * Handles module registration errors.
     *
     * @param Throwable $throwable
     * @param string    $moduleClassName
     */
    private function handleError(Throwable $throwable, string $moduleClassName): void
    {
        $msg     = "Failed to load module ({$moduleClassName})";
        $context = [
            'classname' => $moduleClassName,
            'message'   => $throwable->getMessage()
        ];
        
        $logger = logger(static::LOGGER_NAMESPACE);
        $logger->error($msg, $context);
    }
    
    
    private function logMissingDependencies(Module $module, array $missingDependencies): void
    {
        $moduleClass = get_class($module);
        $namespace   = explode('\\', $moduleClass);
        array_shift($namespace);
        $vendor = array_shift($namespace);
        $module = array_shift($namespace);
        
        $message = "Failed to register module '{$moduleClass}' due to missing dependencies.";
        $message .= " (Vendor: {$vendor}, Module: {$module})";
        
        $logger = logger(static::LOGGER_NAMESPACE);
        $logger->warning($message, ['missing' => $missingDependencies]);
    }
    
    
    /**
     * @param Application $application
     *
     * @return RouteCollector|null
     */
    private function getRouteCollector(Application $application): ?RouteCollector
    {
        return $application->has(RouteCollector::class) ? $application->get(RouteCollector::class) : null;
    }
    
    
    /**
     * Returns the GX modules components service, if available.
     *
     * @param Application $application
     *
     * @return GXModulesComponentsService|null
     */
    private function getGXModulesComponentsService(Application $application): ?GXModulesComponentsService
    {
        return $application->has(GXModulesComponentsService::class) ? $application->get(GXModulesComponentsService::class) : null;
    }
}