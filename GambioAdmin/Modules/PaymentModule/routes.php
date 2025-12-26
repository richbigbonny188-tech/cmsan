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

use Gambio\Admin\Modules\PaymentModule\App\Actions\GetDisallowedPaymentMethodsAction;
use Gambio\Admin\Modules\PaymentModule\App\Actions\SetDisallowedPaymentMethodsAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->post('/admin/api/set-disallowed-payment-modules', SetDisallowedPaymentMethodsAction::class);
    $routeCollector->get('/admin/api/customers/{customerId:[0-9]+}/payment-modules', GetDisallowedPaymentMethodsAction::class);
};
