<?php
/*--------------------------------------------------------------
   routes.php 2022-05-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Admin\Modules\StatisticsOverview\App\Action\ConfigureWidget;
use Gambio\Admin\Modules\StatisticsOverview\App\Action\GetWidgets;
use Gambio\Admin\Modules\StatisticsOverview\App\Action\Overview;
use Gambio\Core\Application\Routing\RouteCollector;

/**
 * @codeCoverageIgnore 
 */
return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/statistics/overview', Overview::class);
    $routeCollector->get('/admin/api/statistics/overview/widgets', GetWidgets::class);
    $routeCollector->post('/admin/api/statistics/overview/widgets/{id}/configure',
                          ConfigureWidget::class);
};