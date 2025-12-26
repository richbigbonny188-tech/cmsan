<?php
/* --------------------------------------------------------------
   routes.php 2021-08-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use Gambio\Admin\Modules\ImageList\App\Actions\Json\AddAnExistingImageToAnImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\CreateImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\DeleteImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\FetchAllImageListsAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\FetchSpecificImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\RemoveImageFromImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\UpdateImageListAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\UpdateImageListImagesAction;
use Gambio\Admin\Modules\ImageList\App\Actions\Json\UpdateImageListImagesSortOrderAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    
    $routeCollector->get('/admin/api/image-lists/{imageListId:[0-9]+}', FetchSpecificImageListAction::class);
    $routeCollector->delete('/admin/api/image-lists/{imageListIds:[0-9,]+}', DeleteImageListAction::class);
    $routeCollector->delete('/admin/api/image-lists/{imageListId:[0-9]+}/images/{relativePath:.+}', RemoveImageFromImageListAction::class);
    $routeCollector->patch('/admin/api/image-lists/{imageListId:[0-9]+}/images', UpdateImageListImagesSortOrderAction::class);
    $routeCollector->post('/admin/api/image-lists', CreateImageListAction::class);
    $routeCollector->put('/admin/api/image-lists', UpdateImageListAction::class);
    $routeCollector->get('/admin/api/image-lists', FetchAllImageListsAction::class);
    $routeCollector->post('/admin/api/image-lists/{imageListId:[0-9]+}/images', AddAnExistingImageToAnImageListAction::class);
    $routeCollector->put('/admin/api/image-lists/{imageListId:[0-9]+}/images', UpdateImageListImagesAction::class);
};
