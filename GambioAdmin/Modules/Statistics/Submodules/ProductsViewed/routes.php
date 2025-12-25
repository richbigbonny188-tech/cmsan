<?php
/* --------------------------------------------------------------
   routes.php 2023-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed;

use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector): void {
    $routeCollector->get('/admin/statistics/products/viewed', App\Actions\ViewedProductsStatistic::class);
};
