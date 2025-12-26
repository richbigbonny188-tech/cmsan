<?php
/* --------------------------------------------------------------
 GambioAdminBootstrapper.php 2023-05-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application;

use Doctrine\DBAL\Exception\DriverException;
use Gambio\Admin\Application\Bootstrapper\AdminErrorHandlerRegistration;
use Gambio\Admin\Application\Bootstrapper\AdminServiceProviderRegistration;
use Gambio\Admin\Application\Bootstrapper\LoadConfiguration;
use Gambio\Admin\Application\Bootstrapper\MiddlewareRegistration;
use Gambio\Admin\Application\Bootstrapper\ModulesMiddlewareRegistration;
use Gambio\Admin\Application\Bootstrapper\ProductOptionsVpeTransitionBootstrapper;
use Gambio\Admin\Application\Bootstrapper\RouteRegistration;
use Gambio\Admin\Application\Bootstrapper\TokenServiceRegistration;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\Bootstrapper\CoreBootstrapper;
use Gambio\Core\ErrorHandling\SentryBootstrapper;

/**
 * Class GambioAdminBootstrapper
 * @package Gambio\Admin\Application
 */
class GambioAdminBootstrapper extends CoreBootstrapper implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        (new LoadConfiguration())->boot($application);
        $this->setSessionParameter($application);
        $this->startSession($application);
        $this->loadEnvironment($application);
        $this->loadServerInformation($application);
        (new SentryBootstrapper)->boot($application);
        $this->registerEventDispatcher($application);
        
        $this->registerCoreServiceProvider($application);
        try {
            $this->initDefaultServerConfiguration($application);
        } catch (DriverException $exception) {
            $this->handleDbConnectionError($exception);
        }
        $this->loadUserPreferencesFromSession($application);
        
        (new AdminServiceProviderRegistration())->boot($application);
        (new TokenServiceRegistration())->boot($application);
        
        $this->registerSlimFramework($application);
        (new AdminErrorHandlerRegistration())->boot($application);
        (new MiddlewareRegistration())->boot($application);
        (new RouteRegistration())->boot($application);
    
        (new ProductOptionsVpeTransitionBootstrapper())->boot($application);
        
        $this->registerModules($application);
        (new ModulesMiddlewareRegistration())->boot($application);
    }
    
    
    /**
     * sets 503 http response code, expiration date of 5 minutes and terminates application
     *
     * @param DriverException $exception
     *
     * @return void
     */
    private function handleDbConnectionError(DriverException $exception): void
    {
        http_response_code(503);
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + (60 * 5)));
        die('Database error! ' . get_class($exception));
    }
}
