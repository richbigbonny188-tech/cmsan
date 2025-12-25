<?php
/* --------------------------------------------------------------
   routes.php 2021-08-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Configuration\App\Actions\ConfigurationOverview;
use Gambio\Admin\Modules\Configuration\App\Actions\FetchConfigurations;
use Gambio\Admin\Modules\Configuration\App\Actions\StoreConfiguration;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/configurations', ConfigurationOverview::class);
    $routeCollector->get('/admin/configurations/data', FetchConfigurations::class);
    $routeCollector->post('/admin/api/configurations', StoreConfiguration::class);
};
