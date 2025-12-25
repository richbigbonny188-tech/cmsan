<?php
/*--------------------------------------------------------------
   routes.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Actions\Json\CreateCustomerMemoAction;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Actions\Json\DeleteCustomerMemoAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->post('/admin/api/customer/memos', CreateCustomerMemoAction::class);
    $routeCollector->delete('/admin/api/customer/memos/{customerMemoId:[0-9]+}', DeleteCustomerMemoAction::class);
};
