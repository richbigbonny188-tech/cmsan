<?php
/*--------------------------------------------------------------
  routes.php 2021-06-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard;

use Gambio\Admin\Modules\Dashboard\App\Actions\ActionIndex;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionResourceProxy;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdateCategory;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdatePeriod;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdateSocialMediaEmbeds;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdateStep;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdateVisibility;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin[/]', ActionIndex::class);
    $routeCollector->get('/admin/dashboard/javascript/{filename}', ActionResourceProxy::class);
    $routeCollector->post('/admin/dashboard/setupwizard/visible/{status}', ActionUpdateVisibility::class);
    $routeCollector->post('/admin/dashboard/setupwizard/{step}/{status}', ActionUpdateStep::class);
    $routeCollector->post('/admin/dashboard/allowexternalcontent/{status}', ActionUpdateSocialMediaEmbeds::class);
    $routeCollector->post('/admin/dashboard/statistics/period/{period}', ActionUpdatePeriod::class);
    $routeCollector->post('/admin/dashboard/statistics/category/{category}', ActionUpdateCategory::class);
};
