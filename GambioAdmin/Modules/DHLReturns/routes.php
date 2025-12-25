<?php
/* --------------------------------------------------------------
   routes.php 2021-08-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\DHLReturns;

use Gambio\Admin\Modules\DHLReturns\App\Actions\ActionLabelList;
use Gambio\Admin\Modules\DHLReturns\App\Actions\ActionMakeLabel;
use Gambio\Admin\Modules\DHLReturns\App\Actions\ActionOrderData;
use Gambio\Admin\Modules\DHLReturns\App\Actions\ActionOverview;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/dhlreturns[/order/{orderid}]', ActionOverview::class);
    $routeCollector->get('/admin/dhlreturns/orderdata/{orderid}', ActionOrderData::class);
    $routeCollector->post('/admin/dhlreturns/makelabel', ActionMakeLabel::class);
    $routeCollector->get('/admin/dhlreturns/labellist/{orderid}', ActionLabelList::class);
};
