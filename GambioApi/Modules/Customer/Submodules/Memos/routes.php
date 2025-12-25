<?php
/*--------------------------------------------------------------
   routes.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\CreateCustomerMemoAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\DeleteAllCustomerMemoAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\DeleteMultipleCustomerMemosAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\FetchAllCustomerMemosAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\FetchSpecificCustomerMemoAction;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions\UpdateCustomerMemoAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    
    $routeCollector->get('/api.php/v3/customers/{customerId:[0-9]+}/memos', FetchAllCustomerMemosAction::class);
    $routeCollector->get('/api.php/v3/customers/{customerId:[0-9]+}/memos/{memoId:[0-9]+}', FetchSpecificCustomerMemoAction::class);
    $routeCollector->delete('/api.php/v3/customers/{customerId:[0-9]+}/memos', DeleteAllCustomerMemoAction::class);
    $routeCollector->delete('/api.php/v3/customers/{customerId:[0-9]+}/memos/{memoIds:[0-9,]+}', DeleteMultipleCustomerMemosAction::class);
    $routeCollector->post('/api.php/v3/customers/{customerId:[0-9]+}/memos', CreateCustomerMemoAction::class);
    $routeCollector->put('/api.php/v3/customers/{customerId:[0-9]+}/memos', UpdateCustomerMemoAction::class);
};
