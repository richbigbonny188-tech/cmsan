<?php
/*--------------------------------------------------------------------------------------------------
    routes.php 2021-09-10
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

declare(strict_types=1);

use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Vue\IndexAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\CreateProductDownloadsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\DeleteSpecificProductDownloadAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\FetchAllProductDownloadsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\FetchAllAvailableOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\FetchSpecificAvailableOptionsAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\FetchSpecificProductDownloadAction;
use Gambio\Admin\Modules\Product\Submodules\Download\App\Actions\Json\UpdateProductDownloadsAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    $routeCollector->get('/admin/products/{productId:[0-9]+}/downloads', IndexAction::class);
    $routeCollector->get('/admin/api/products/{productId:[0-9]+}/downloads',
                         FetchAllProductDownloadsAction::class);
    $routeCollector->delete('/admin/api/products/{productId:[0-9]+}/downloads/{optionIds:[0-9\,]+}',
                            DeleteSpecificProductDownloadAction::class);
    $routeCollector->get('/admin/api/products/{productId:[0-9]+}/downloads/{optionId:[0-9]+}',
                         FetchSpecificProductDownloadAction::class);
    $routeCollector->put('/admin/api/products/{productId:[0-9]+}/downloads',
                         UpdateProductDownloadsAction::class);
    $routeCollector->get('/admin/api/products/{productId:[0-9]+}/downloads/available',
                         FetchAllAvailableOptionsAction::class);
    $routeCollector->get('/admin/api/products/{productId:[0-9]+}/downloads/available/{optionId:[0-9]+}',
                         FetchSpecificAvailableOptionsAction::class);
    $routeCollector->post('/admin/api/products/{productId:[0-9]+}/downloads',
                          CreateProductDownloadsAction::class);
};