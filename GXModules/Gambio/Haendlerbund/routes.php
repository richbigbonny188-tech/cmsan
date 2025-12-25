<?php
/* --------------------------------------------------------------
   routes.php 2022-03-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Gambio\Core\Application\Routing\RouteCollector;
use GXModules\Gambio\Haendlerbund\Admin\App\Actions\SaveConfiguration;
use GXModules\Gambio\Haendlerbund\Admin\App\Actions\ShowConfiguration;
use GXModules\Gambio\Haendlerbund\Admin\App\Actions\UpdateNow;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/haendlerbund/configuration', ShowConfiguration::class);
    $routeCollector->post('/admin/haendlerbund/saveConfiguration', SaveConfiguration::class);
    $routeCollector->post('/admin/haendlerbund/updateNow', UpdateNow::class);
};
