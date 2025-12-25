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

use Gambio\Api\Modules\ImageList\App\Actions\AddANewImageToAnImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\AddAnExistingImageToAnImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\CreateImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\DeleteImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\FetchAllImageListsAction;
use Gambio\Api\Modules\ImageList\App\Actions\FetchImagesFromASpecificImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\FetchSpecificImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\RemoveImageFromImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\UpdateImageListAction;
use Gambio\Api\Modules\ImageList\App\Actions\UpdateImageListImagesAction;
use Gambio\Api\Modules\ImageList\App\Actions\UpdateImageListImagesSortOrderAction;
use Gambio\Core\Application\Routing\RouteCollector;

return static function (RouteCollector $routeCollector) {
    
    $routeCollector->get('/api.php/v3/image-lists', FetchAllImageListsAction::class);
    $routeCollector->post('/api.php/v3/image-lists', CreateImageListAction::class);
    $routeCollector->put('/api.php/v3/image-lists', UpdateImageListAction::class);
    
    $routeCollector->get('/api.php/v3/image-lists/{imageListId:[0-9]+}', FetchSpecificImageListAction::class);
    
    $routeCollector->delete('/api.php/v3/image-lists/{imageListIds:[0-9,]+}', DeleteImageListAction::class);
    
    $routeCollector->get('/api.php/v3/image-lists/{imageListId:[0-9]+}/images', FetchImagesFromASpecificImageListAction::class);
    $routeCollector->post('/api.php/v3/image-lists/{imageListId:[0-9]+}/images', AddAnExistingImageToAnImageListAction::class);
    $routeCollector->patch('/api.php/v3/image-lists/{imageListId:[0-9]+}/images', UpdateImageListImagesSortOrderAction::class);
    $routeCollector->put('/api.php/v3/image-lists/{imageListId:[0-9]+}/images', UpdateImageListImagesAction::class);
    
    $routeCollector->delete('/api.php/v3/image-lists/{imageListId:[0-9]+}/images/{relativePath:.+}', RemoveImageFromImageListAction::class);
    $routeCollector->post('/api.php/v3/image-lists/{imageListId:[0-9]+}/images/{relativePath:.+}', AddANewImageToAnImageListAction::class);
};
