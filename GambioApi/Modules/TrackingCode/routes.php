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

use Gambio\Api\Modules\TrackingCode\App\Actions\CreateTrackingCodesAction;
use Gambio\Api\Modules\TrackingCode\App\Actions\DeleteTrackingCodesAction;
use Gambio\Api\Modules\TrackingCode\App\Actions\FetchAllTrackingCodesAction;
use Gambio\Api\Modules\TrackingCode\App\Actions\FetchSpecificTrackingCodeAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/api.php/v3/tracking-codes', FetchAllTrackingCodesAction::class);
    $routeCollector->post('/api.php/v3/tracking-codes', CreateTrackingCodesAction::class);
    $routeCollector->delete('/api.php/v3/tracking-codes/{ids:[0-9,]+}', DeleteTrackingCodesAction::class);
    $routeCollector->get('/api.php/v3/tracking-codes/{id:[0-9]+}', FetchSpecificTrackingCodeAction::class);
};
