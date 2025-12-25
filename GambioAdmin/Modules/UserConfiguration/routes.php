<?php
/* --------------------------------------------------------------
 routes.php 2020-12-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\Admin\Modules\UserConfiguration\App\UserConfigurationController;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/api/user-configuration', UserConfigurationController::class . ':get');
    $routeCollector->post('/admin/api/user-configuration', UserConfigurationController::class . ':save');
};
