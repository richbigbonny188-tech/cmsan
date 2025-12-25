<?php
/* --------------------------------------------------------------
 ProductOptionsVpeTransitionBootstrapper.php 2021-11-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Bootstrapper;

use Gambio\Admin\Modules\Deprecated\Vpe\VpeServiceProvider;
use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Application\Routing\RouteCollector;
use RuntimeException;

/**
 * Class ProductOptionsVpeTransitionBootstrapper
 *
 * @package    Gambio\Admin\Application\Bootstrapper
 * @deprecated will be removed with the release of 4.7!
 */
class ProductOptionsVpeTransitionBootstrapper implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $routesFile = dirname(__DIR__, 2) . '/Modules/Deprecated/Vpe/routes.php';
        if (is_file($routesFile)) {
            $routeCollector = $this->getRouteCollector($application);
            
            $application->registerProvider(VpeServiceProvider::class);
            $routesCallback = require $routesFile;
            $routesCallback($routeCollector);
        }
    }
    
    
    /**
     * Returns the route collector from the DI-Container.
     *
     * @param Application $application
     *
     * @return RouteCollector
     */
    private function getRouteCollector(Application $application): RouteCollector
    {
        if (!$application->has(RouteCollector::class)) {
            throw new RuntimeException(RouteCollector::class . ' must be registered first!');
        }
        
        return $application->get(RouteCollector::class);
    }
}