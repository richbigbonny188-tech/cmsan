<?php
/* --------------------------------------------------------------
   routes.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Country\App\Actions\FetchActiveCountriesAction;
use Gambio\Admin\Modules\Country\App\Actions\FetchAllCountriesAction;
use Gambio\Core\Application\Routing\RouteCollector;

/**
 * @codeCoverageIgnore
 */
return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/api/country', FetchAllCountriesAction::class);
    $routeCollector->get('/admin/api/country/active', FetchActiveCountriesAction::class);
};
