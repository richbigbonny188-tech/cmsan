<?php
/* --------------------------------------------------------------
 GambioApiBootstrapper.php 2023-05-30
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Api\Application;

use Gambio\Admin\Application\Bootstrapper\AdminServiceProviderRegistration;
use Gambio\Api\Application\Bootstrapper\ApiErrorHandlerRegistration;
use Gambio\Api\Application\Bootstrapper\ApiMiddlewareRegistration;
use Gambio\Api\Application\Bootstrapper\ApiModulesMiddlewareRegistration;
use Gambio\Api\Application\Bootstrapper\ApiRouteRegistration;
use Gambio\Api\Application\Bootstrapper\ApiServiceProviderRegistration;
use Gambio\Api\Application\Bootstrapper\LoadConfiguration;
use Gambio\Api\Application\Bootstrapper\LoadUserPreferences;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\Bootstrapper\CoreBootstrapper;
use Gambio\Core\ErrorHandling\SentryBootstrapper;

/**
 * Class GambioApiBootstrapper
 *
 * @package Gambio\Api\Application
 */
class GambioApiBootstrapper extends CoreBootstrapper implements Bootstrapper
{
    /**
     * Defines the current API version.
     */
    public const VERSION = '3.0.0';
    
    
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        (new LoadConfiguration())->boot($application);
        $this->loadEnvironment($application);
        $this->loadServerInformation($application);
        (new SentryBootstrapper)->boot($application);
        $this->registerEventDispatcher($application);
        
        $this->registerCoreServiceProvider($application);
        (new LoadUserPreferences())->boot($application);
        (new ApiServiceProviderRegistration())->boot($application);
        (new AdminServiceProviderRegistration())->boot($application);
        
        $this->registerSlimFramework($application);
        (new ApiErrorHandlerRegistration())->boot($application);
        (new ApiMiddlewareRegistration())->boot($application);
        (new ApiRouteRegistration())->boot($application);
        
        $this->registerModules($application);
        (new ApiModulesMiddlewareRegistration())->boot($application);
    }
}