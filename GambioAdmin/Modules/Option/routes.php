<?php
/* --------------------------------------------------------------
   routes.php 2023-04-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Option\App\Actions\Json\CreateOptionsAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\CreateOptionValuesAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\DeleteOptionsAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\DeleteOptionValuesAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\FetchAllOptionsAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\FetchAllOptionValuesAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\FetchSpecificOptionAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\UpdateOptionsAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\UpdateOptionsSortingOrderAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\UpdateOptionValuesAction;
use Gambio\Admin\Modules\Option\App\Actions\Json\UpdateOptionValuesSortingOrderAction;
use Gambio\Admin\Modules\Option\App\Actions\Vue\IndexAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/options', IndexAction::class);
    $routeCollector->get('/admin/api/options', FetchAllOptionsAction::class);
    $routeCollector->get('/admin/api/options/{optionId:[0-9]+}', FetchSpecificOptionAction::class);
    $routeCollector->get('/admin/api/options/{optionId:[0-9]+}/values', FetchAllOptionValuesAction::class);
    $routeCollector->delete('/admin/api/options/{optionIds:[0-9\s,]+}', DeleteOptionsAction::class);
    $routeCollector->delete('/admin/api/options/{optionId:[0-9]+}/values/{optionValueIds:[0-9\s,]+}', DeleteOptionValuesAction::class);
    $routeCollector->patch('/admin/api/options/{optionId:[0-9]+}/values', UpdateOptionValuesSortingOrderAction::class);
    $routeCollector->post('/admin/api/options/{optionId:[0-9]+}/values', CreateOptionValuesAction::class);
    $routeCollector->put('/admin/api/options/{optionId:[0-9]+}/values', UpdateOptionValuesAction::class);
    $routeCollector->put('/admin/api/options', UpdateOptionsAction::class);
    $routeCollector->post('/admin/api/options', CreateOptionsAction::class);
    $routeCollector->patch('/admin/api/options', UpdateOptionsSortingOrderAction::class);
};
