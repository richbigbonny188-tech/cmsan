<?php
/*--------------------------------------------------------------
   routes.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\CreateAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\DeleteAllAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\DeleteSpecificAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\FetchAllAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\FetchSpecificAdditionalOptionAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\PatchAdditionalOptionsAction;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions\UpdateAdditionalOptionsAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function(RouteCollector $routeCollector) {
    
    $routeCollector->get('/api.php/v3/products/{productId:[0-9]+}/options', FetchAllAdditionalOptionsAction::class);
    $routeCollector->post('/api.php/v3/products/{productId:[0-9]+}/options', CreateAdditionalOptionsAction::class);
    $routeCollector->put('/api.php/v3/products/{productId:[0-9]+}/options', UpdateAdditionalOptionsAction::class);
    $routeCollector->patch('/api.php/v3/products/{productId:[0-9]+}/options', PatchAdditionalOptionsAction::class);
    $routeCollector->delete('/api.php/v3/products/{productId:[0-9]+}/options', DeleteAllAdditionalOptionsAction::class);
    
    $routeCollector->get('/api.php/v3/products/{productId:[0-9]+}/options/{optionId:[0-9]+}', FetchSpecificAdditionalOptionAction::class);
    $routeCollector->delete('/api.php/v3/products/{productId:[0-9]+}/options/{optionIds:[0-9,]+}', DeleteSpecificAdditionalOptionsAction::class);
};