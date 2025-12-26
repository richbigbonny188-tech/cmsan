<?php

/* --------------------------------------------------------------
 routes.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\Admin\Modules\ParcelService\App\ParcelServiceController;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/parcel-services', ParcelServiceController::class . ':showOverview');
    $routeCollector->get('/admin/api/parcel-services', ParcelServiceController::class . ':getParcelServices');
    $routeCollector->post('/admin/api/parcel-services', ParcelServiceController::class . ':createParcelService');
    $routeCollector->put('/admin/api/parcel-services', ParcelServiceController::class . ':updateParcelService');
    $routeCollector->delete('/admin/api/parcel-services/{id:[0-9]+}',
                            ParcelServiceController::class . ':deleteParcelService');
};