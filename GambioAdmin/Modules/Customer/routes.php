<?php
/* --------------------------------------------------------------
   routes.php 2022-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Customer\App\Actions\Json\ChangeCustomerGroupAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\ChangePasswordAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\CreateCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\CustomerConfigAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\CustomerProfileAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\CustomersOverviewAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\DeleteCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\DeleteOutdatedGuestAccountsAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\FetchProductNameAndImageAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\NextCustomerNumberAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\PatchCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\PatchUserConfigurationAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\RegistrationAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\SearchCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\SetDisallowedPaymentAndShippingMethodsAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\SetLogAdminActivitiesStatusAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\UpdateCustomerAction;
use Gambio\Admin\Modules\Customer\App\Actions\Json\ValidateEmailAddressAction;
use Gambio\Admin\Modules\Customer\App\Actions\Vue\OverviewAction;
use Gambio\Admin\Modules\Customer\App\Actions\Vue\ProfileAction;
use Gambio\Core\Application\Routing\RouteCollector;

/**
 * @codeCoverageIgnore
 */
return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/customers', OverviewAction::class);
    $routeCollector->get('/admin/customers/{customer:[0-9]+}', ProfileAction::class);
    $routeCollector->get('/admin/api/customers/search', SearchCustomerAction::class);
    $routeCollector->get('/admin/api/customer-configurations', CustomerConfigAction::class);
    
    $routeCollector->post('/admin/api/customer-products', FetchProductNameAndImageAction::class);
    $routeCollector->get('/admin/api/customers-overview', CustomersOverviewAction::class);
    $routeCollector->post('/admin/api/customer-profile', CustomerProfileAction::class);
    $routeCollector->post('/admin/api/create-customer', CreateCustomerAction::class);
    $routeCollector->post('/admin/api/delete-customer', DeleteCustomerAction::class);
    $routeCollector->post('/admin/api/delete-guests', DeleteOutdatedGuestAccountsAction::class);
    $routeCollector->post('/admin/api/update-customer', UpdateCustomerAction::class);
    $routeCollector->patch('/admin/api/customer', PatchCustomerAction::class);
    $routeCollector->post('/admin/api/change-customer-password', ChangePasswordAction::class);
    $routeCollector->post('/admin/api/change-customer-disallowed-payment-and-shipping-modules', SetDisallowedPaymentAndShippingMethodsAction::class);
    $routeCollector->patch('/admin/api/change-customers-group', ChangeCustomerGroupAction::class);
    $routeCollector->post('/admin/api/validate-email', ValidateEmailAddressAction::class);
    $routeCollector->post('/admin/api/customer-register', RegistrationAction::class);
    $routeCollector->patch('/admin/api/customer-user-configuration', PatchUserConfigurationAction::class);
    $routeCollector->patch('/admin/api/log-admin-activities', SetLogAdminActivitiesStatusAction::class);
    $routeCollector->get('/admin/api/next-customer-number', NextCustomerNumberAction::class);
};
