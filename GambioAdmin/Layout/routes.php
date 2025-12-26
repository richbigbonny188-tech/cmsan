<?php

/* --------------------------------------------------------------
 routes.php 2021-09-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\Admin\Layout\Favorites\AddFavorite;
use Gambio\Admin\Layout\Favorites\DeleteFavorites;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->post('/admin/api/favorites', AddFavorite::class);
    $routeCollector->delete('/admin/api/favorites/{menuItemId}', DeleteFavorites::class);
};
