<?php
/* --------------------------------------------------------------
   routes.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Actions\GetShoppingCartAction;
use Gambio\Admin\Modules\Customer\Submodules\ShoppingCart\App\Actions\RemoveShoppingCartAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/api/customer/{customerId:[0-9]+}/cart', GetShoppingCartAction::class);
    $routeCollector->delete('/admin/api/customer/{customerId:[0-9]+}/cart', RemoveShoppingCartAction::class);
};
