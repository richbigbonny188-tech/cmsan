<?php
/*--------------------------------------------------------------
   routes.php 2022-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Api\Modules\Customer\App\Actions\CreateCustomerAction;
use Gambio\Api\Modules\Customer\App\Actions\CustomerRegistrationAction;
use Gambio\Api\Modules\Customer\App\Actions\DeleteMultipleCustomersAction;
use Gambio\Api\Modules\Customer\App\Actions\DeleteOutdatedGuestAccountsAction;
use Gambio\Api\Modules\Customer\App\Actions\FetchAllCustomersAction;
use Gambio\Api\Modules\Customer\App\Actions\FetchSpecificCustomerAction;
use Gambio\Api\Modules\Customer\App\Actions\UpdateCustomerAction;
use Gambio\Api\Modules\Customer\App\Actions\UpdateCustomerFavoriteStateAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/api.php/v3/customers', FetchAllCustomersAction::class);
    $routeCollector->get('/api.php/v3/customers/{customerId:[0-9]+}', FetchSpecificCustomerAction::class);
    $routeCollector->delete('/api.php/v3/customers/{customerIds:[0-9,]+}', DeleteMultipleCustomersAction::class);
    $routeCollector->post('/api.php/v3/customers', CreateCustomerAction::class);
    $routeCollector->put('/api.php/v3/customers', UpdateCustomerAction::class);
    $routeCollector->patch('/api.php/v3/customers', UpdateCustomerFavoriteStateAction::class);
    $routeCollector->delete('/api.php/v3/customers/guests', DeleteOutdatedGuestAccountsAction::class);
};
