<?php
/*--------------------------------------------------------------
   routes.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\AddOptionValuesAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\CreateProductVariantsAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\DeleteAllProductVariantsAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\DeleteSpecificProductVariantAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\FetchAllProductVariantsAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\FetchSpecificProductVariantAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\GenerateProductVariantsAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\PatchProductVariantsAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\RemoveOptionAction;
use Gambio\Api\Modules\Product\Submodules\Variant\App\Actions\UpdateProductVariantsAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    
    $routeCollector->get('/api.php/v3/products/{productId:[0-9]+}/variants', FetchAllProductVariantsAction::class);
    $routeCollector->post('/api.php/v3/products/{productId:[0-9]+}/variants', CreateProductVariantsAction::class);
    $routeCollector->put('/api.php/v3/products/{productId:[0-9]+}/variants', UpdateProductVariantsAction::class);
    $routeCollector->patch('/api.php/v3/products/{productId:[0-9]+}/variants', PatchProductVariantsAction::class);
    $routeCollector->delete('/api.php/v3/products/{productId:[0-9]+}/variants', DeleteAllProductVariantsAction::class);
    
    $routeCollector->get('/api.php/v3/products/{productId:[0-9]+}/variants/{variantId:[0-9\-|]+}',
                         FetchSpecificProductVariantAction::class);
    $routeCollector->delete('/api.php/v3/products/{productId:[0-9]+}/variants/{variantIds:[0-9\-,|]+}',
                            DeleteSpecificProductVariantAction::class);
    
    $routeCollector->post('/api.php/v3/products/{productId:[0-9]+}/variants/_generate',
                          GenerateProductVariantsAction::class);
    $routeCollector->post('/api.php/v3/products/{productId:[0-9]+}/variants/_add-options',
                          AddOptionValuesAction::class);
    $routeCollector->post('/api.php/v3/products/{productId:[0-9]+}/variants/_remove-options',
                          RemoveOptionAction::class);
};