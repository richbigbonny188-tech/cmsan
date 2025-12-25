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

use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\CreateCustomerAddonValueAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\DeleteAllCustomerAddonValuesAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\DeleteMultipleCustomerAddonValuesAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\FetchAllCustomerAddonValuesAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\FetchSpecificCustomerAddonValueAction;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions\UpdateCustomerAddonValueAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/api.php/v3/customers/{customerId:[0-9]+}/addon-values',
                         FetchAllCustomerAddonValuesAction::class);
    $routeCollector->get('/api.php/v3/customers/{customerId:[0-9]+}/addon-values/{key}',
                         FetchSpecificCustomerAddonValueAction::class);
    $routeCollector->delete('/api.php/v3/customers/{customerId:[0-9]+}/addon-values',
                            DeleteAllCustomerAddonValuesAction::class);
    $routeCollector->delete('/api.php/v3/customers/{customerId:[0-9]+}/addon-values/{keys}',
                            DeleteMultipleCustomerAddonValuesAction::class);
    $routeCollector->post('/api.php/v3/customers/{customerId:[0-9]+}/addon-values',
                          CreateCustomerAddonValueAction::class);
    $routeCollector->put('/api.php/v3/customers/{customerId:[0-9]+}/addon-values',
                         UpdateCustomerAddonValueAction::class);
};
