<?php
/* --------------------------------------------------------------
   routes.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Actions\Html\PurchasedProductsIndexAction;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Actions\Json\FetchAllSoldProductsAction;
use Gambio\Core\Application\Routing\RouteCollector;

/**
 * @codeCoverageIgnore
 */
return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/api/statistics/products/purchased', FetchAllSoldProductsAction::class);
    $routeCollector->get('/admin/statistics/products/purchased', PurchasedProductsIndexAction::class);
};