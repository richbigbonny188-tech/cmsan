<?php
/* --------------------------------------------------------------
   ModulesMiddlewareRegistration.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Application\Bootstrapper;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\GXModules\Services\GXModulesComponentsService;
use Slim\App as SlimApp;

/**
 * Class ModulesMiddlewareRegistration
 *
 * @package Gambio\Admin\Application\Bootstrapper
 */
class ModulesMiddlewareRegistration implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $componentsService = $this->getGXModulesComponentsService($application);
        $slim              = $this->getSlim($application);
        
        if ($componentsService === null || $slim === null) {
            return;
        }
        
        foreach ($componentsService->getGX4Modules() as $gx4Module) {
            $module = $gx4Module->createClass();
            foreach ($module->adminMiddleware() as $middleware) {
                $slim->addMiddleware($middleware);
            }
        }
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
    
    
    /**
     * Returns the slim app, if available.
     *
     * @param Application $application
     *
     * @return SlimApp|null
     */
    private function getSlim(Application $application): ?SlimApp
    {
        return $application->has(SlimApp::class) ? $application->get(SlimApp::class) : null;
    }
}