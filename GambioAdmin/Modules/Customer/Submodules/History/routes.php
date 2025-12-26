<?php
/* --------------------------------------------------------------
   routes.php 2021-09-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Customer\Submodules\History\App\Actions\JSON\FetchAllCustomerHistoryEntriesAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/api/customer/{customerId:[0-9]+}/history', FetchAllCustomerHistoryEntriesAction::class);
};
