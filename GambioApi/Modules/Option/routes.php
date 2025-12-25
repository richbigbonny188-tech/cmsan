<?php
/* --------------------------------------------------------------
 routes.php 2021-06-28
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\Api\Modules\Option\App\Actions\AddANewImageToAnOptionValueAction;
use Gambio\Api\Modules\Option\App\Actions\CreateOptionsAction;
use Gambio\Api\Modules\Option\App\Actions\CreateOptionValuesAction;
use Gambio\Api\Modules\Option\App\Actions\DeleteOptionsAction;
use Gambio\Api\Modules\Option\App\Actions\DeleteOptionValuesAction;
use Gambio\Api\Modules\Option\App\Actions\FetchAllOptionsAction;
use Gambio\Api\Modules\Option\App\Actions\FetchAllOptionValuesAction;
use Gambio\Api\Modules\Option\App\Actions\FetchSpecificOptionAction;
use Gambio\Api\Modules\Option\App\Actions\FetchSpecificOptionValueAction;
use Gambio\Api\Modules\Option\App\Actions\UpdateOptionsAction;
use Gambio\Api\Modules\Option\App\Actions\UpdateOptionsSortingOrderAction;
use Gambio\Api\Modules\Option\App\Actions\UpdateOptionValuesAction;
use Gambio\Api\Modules\Option\App\Actions\UpdateOptionValuesSortingOrderAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/api.php/v3/options', FetchAllOptionsAction::class);
    $routeCollector->post('/api.php/v3/options', CreateOptionsAction::class);
    $routeCollector->patch('/api.php/v3/options', UpdateOptionsSortingOrderAction::class);
    $routeCollector->put('/api.php/v3/options', UpdateOptionsAction::class);
    $routeCollector->delete('/api.php/v3/options/{optionIds:[0-9,]+}', DeleteOptionsAction::class);
    $routeCollector->get('/api.php/v3/options/{optionId:[0-9]+}', FetchSpecificOptionAction::class);
    $routeCollector->get('/api.php/v3/options/{optionId:[0-9]+}/values', FetchAllOptionValuesAction::class);
    $routeCollector->get('/api.php/v3/options/{optionId:[0-9]+}/values/{optionValueId:[0-9]+}',
                         FetchSpecificOptionValueAction::class);
    $routeCollector->post('/api.php/v3/options/{optionId:[0-9]+}/values', CreateOptionValuesAction::class);
    $routeCollector->put('/api.php/v3/options/{optionId:[0-9]+}/values', UpdateOptionValuesAction::class);
    $routeCollector->patch('/api.php/v3/options/{optionId:[0-9]+}/values', UpdateOptionValuesSortingOrderAction::class);
    $routeCollector->delete('/api.php/v3/options/{optionId:[0-9]+}/values/{optionValueIds:[0-9,]+}',
                            DeleteOptionValuesAction::class);
    $routeCollector->post('/api.php/v3/options/{optionId:[0-9]+}/values/{optionValueId:[0-9]+}/image/{relativePath:.+}', AddANewImageToAnOptionValueAction::class);
};
