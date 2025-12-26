<?php
/*--------------------------------------------------------------
   UpdateImageListImagesSortOrderAction.php 2021-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageDoesNotExistException;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestValidator;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateImageListImagesSortOrderAction
 * @package Gambio\Admin\Modules\ImageList\App\Actions\Json
 */
class UpdateImageListImagesSortOrderAction extends AbstractAction
{
    /**
     * @var ImageListWriteServiceInterface
     */
    private $writeService;
    
    /**
     * @var ImageListReadServiceInterface
     */
    private $readService;
    
    /**
     * @var ImageListApiRequestValidator
     */
    private $validator;
    
    /**
     * @var ImageListFactory
     */
    private $factory;
    
    
    /**
     * DeleteImageListAction constructor.
     *
     * @param ImageListWriteServiceInterface $writeService
     * @param ImageListReadServiceInterface  $readService
     * @param ImageListApiRequestValidator   $validator
     * @param ImageListFactory               $factory
     */
    public function __construct(
        ImageListWriteServiceInterface $writeService,
        ImageListReadServiceInterface $readService,
        ImageListApiRequestValidator $validator,
        ImageListFactory $factory
    ) {
        $this->writeService = $writeService;
        $this->readService  = $readService;
        $this->validator    = $validator;
        $this->factory      = $factory;
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody();
        $errors     = $this->validator->validateUpdateSortOrderRequest($parsedBody);
    
        if (count($errors) !== 0) {
        
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
    
        try {
        
            $imageListId   = (int)$request->getAttribute('imageListId');
            $imageList     = $this->readService->getImageListById($imageListId);
            $updatedImages = [];
        
            foreach ($parsedBody as ['relativePath' => $relativePath, 'sortOrder' => $sortOrder]) {
            
                $relativePath    = $this->factory->createImagePath($relativePath);
                $updatedImages[] = $imageList->images()->getByLocalPath($relativePath)->withSortOrder((int)$sortOrder);
            }
        
            $imageList->changeImages(...$updatedImages);
        
            $this->writeService->storeImageLists($imageList);
        
            return $response->withStatus(204);
        
        } catch (Exception $exception) {
        
            $statusCode = $exception instanceof ImageDoesNotExistException ? 409 : 422;
        
            return $response->withStatus($statusCode)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}