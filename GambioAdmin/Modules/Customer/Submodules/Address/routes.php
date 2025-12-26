<?php
/* --------------------------------------------------------------
   routes.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Customer\Submodules\Address\App\Actions\Json\FetchCustomerDefaultAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Actions\Json\UpdateCustomersDefaultAddress;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->post('/admin/api/customer-address/update-default', UpdateCustomersDefaultAddress::class);
    $routeCollector->get('/admin/api/customer/{customerId:[0-9]+}/default-address', FetchCustomerDefaultAddress::class);
};
