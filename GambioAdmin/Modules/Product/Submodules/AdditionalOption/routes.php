<?php
/* --------------------------------------------------------------
   routes.php 2021-09-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\CreateAdditionalOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\DeleteSpecificAdditionalOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\FetchAllAdditionalOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\FetchAllAvailableOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\FetchSpecificAdditionalOptionAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\FetchSpecificAvailableOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Json\UpdateAdditionalOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App\Actions\Vue\IndexAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/products/{productId:[0-9]+}/options', IndexAction::class);
    $routeCollector->get('/admin/api/products/{productId:[0-9]+}/options', FetchAllAdditionalOptionsAction::class);
    $routeCollector->delete('/admin/api/products/{productId:[0-9]+}/options/{optionIds:[0-9\,]+}', DeleteSpecificAdditionalOptionsAction::class);
    $routeCollector->get('/admin/api/products/{productId:[0-9]+}/options/{optionId:[0-9]+}', FetchSpecificAdditionalOptionAction::class);
    $routeCollector->put('/admin/api/products/{productId:[0-9]+}/options', UpdateAdditionalOptionsAction::class);
    $routeCollector->get('/admin/api/products/{productId:[0-9]+}/options/available', FetchAllAvailableOptionsAction::class);
    $routeCollector->get('/admin/api/products/{productId:[0-9]+}/options/available/{optionId:[0-9]+}', FetchSpecificAvailableOptionsAction::class);
    $routeCollector->post('/admin/api/products/{productId:[0-9]+}/options', CreateAdditionalOptionsAction::class);
};
