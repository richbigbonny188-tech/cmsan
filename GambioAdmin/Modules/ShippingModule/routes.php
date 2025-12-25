<?php
/* --------------------------------------------------------------
   routes.php 2022-05-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\ShippingModule\App\Actions\GetDisallowedShippingMethodsAction;
use Gambio\Admin\Modules\ShippingModule\App\Actions\SetDisallowedShippingMethodsAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->post('/admin/api/set-disallowed-shipping-modules', SetDisallowedShippingMethodsAction::class);
    $routeCollector->get('/admin/api/customers/{customerId:[0-9]+}/shipping-modules', GetDisallowedShippingMethodsAction::class);
};
