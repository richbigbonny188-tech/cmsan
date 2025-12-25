<?php
/*--------------------------------------------------------------
   AddAnExistingImageToAnImageListAction.php 2021-08-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList\App\Actions;

use Exception;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class AddAnExistingImageToAnImageListAction
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class AddAnExistingImageToAnImageListAction
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
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $errors = $this->validator->validateAddImageRequest($parsedBody = $request->getParsedBody());
    
        if (empty($errors) === false) {
        
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
    
        try {
    
            $imageListId = (int)$request->getAttribute('imageListId');
            $imageList   = $this->readService->getImageListById($imageListId);
    
            foreach ($parsedBody as ['relativePath' => $relativePath, 'sortOrder' => $sortOrder, 'titles' => $titles, 'altTitles' => $altTitles]) {
    
                $localPath = $this->factory->createImagePath($relativePath);
    
                foreach ($titles as &$title) {
        
                    ['languageCode' => $languageCode, 'text' => $text] = $title;
        
                    $title = $this->factory->createImageTitle($languageCode, $text);
                }
    
                $titles = $this->factory->createImageTitles(...$titles);
    
                foreach ($altTitles as &$altTitle) {
        
                    ['languageCode' => $languageCode, 'text' => $text] = $altTitle;
        
                    $altTitle = $this->factory->createImageAltTitle($languageCode, $text);
                }
    
                $altTitles = $this->factory->createImageAltTitles(...$altTitles);
                $newImage  = $this->factory->createNewImage($localPath, $titles, $altTitles, (int)$sortOrder);
                
                $imageList->addNewImages($newImage);
            }
            
            $this->writeService->storeImageLists($imageList);
    
            return $response->withStatus(204);
    
        } catch (Exception $exception) {
    
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}