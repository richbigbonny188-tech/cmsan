<?php
/* --------------------------------------------------------------
   ApiModulesMiddlewareRegistration.php 2020-10-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Application\Bootstrapper;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\GXModules\Services\GXModulesComponentsService;
use RuntimeException;
use Slim\App as SlimApp;

/**
 * Class ApiModulesMiddlewareRegistration
 *
 * @package Gambio\Api\Application\Bootstrapper
 */
class ApiModulesMiddlewareRegistration implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $componentsService = $this->getGXModulesComponentsService($application);
        $slim              = $this->getSlim($application);
        
        foreach ($componentsService->getGX4Modules() as $gx4Module) {
            $module = $gx4Module->createClass();
            foreach ($module->apiMiddleware() as $middleware) {
                $slim->addMiddleware($middleware);
            }
        }
    }
    
    
    /**
     * @param Application $application
     *
     * @return GXModulesComponentsService
     */
    private function getGXModulesComponentsService(Application $application): GXModulesComponentsService
    {
        if ($application->has(GXModulesComponentsService::class) === false) {
            throw new RuntimeException('GXModules components service needs to be registered to register API middlewares from GXModules.');
        }
        
        return $application->get(GXModulesComponentsService::class);
    }
    
    
    /**
     * @param Application $application
     *
     * @return SlimApp
     */
    private function getSlim(Application $application): SlimApp
    {
        if ($application->has(SlimApp::class) === false) {
            throw new RuntimeException('Slim app needs to be registered to register API middlewares from GXModules.');
        }
        
        return $application->get(SlimApp::class);
    }
}