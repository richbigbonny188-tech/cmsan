<?php

/* --------------------------------------------------------------
 routes.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\Admin\Modules\RedirectRules\RedirectRulesAdminController;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/redirect-rules', RedirectRulesAdminController::class . ':showOverview');
    $routeCollector->get('/admin/redirect-rules/get-rules', RedirectRulesAdminController::class . ':getRules');
    $routeCollector->post('/admin/redirect-rules/add-rule', RedirectRulesAdminController::class . ':addRule');
    $routeCollector->post('/admin/redirect-rules/delete-rule', RedirectRulesAdminController::class . ':deleteRule');
    $routeCollector->post('/admin/redirect-rules/enable-rule', RedirectRulesAdminController::class . ':enableRule');
    $routeCollector->post('/admin/redirect-rules/disable-rule', RedirectRulesAdminController::class . ':disableRule');
    $routeCollector->post('/admin/redirect-rules/update-rule', RedirectRulesAdminController::class . ':updateRule');
    $routeCollector->get('/admin/redirect-rules/get-configuration', RedirectRulesAdminController::class . ':getConfig');
    $routeCollector->post('/admin/redirect-rules/save-configuration',
                          RedirectRulesAdminController::class . ':setConfig');
};
