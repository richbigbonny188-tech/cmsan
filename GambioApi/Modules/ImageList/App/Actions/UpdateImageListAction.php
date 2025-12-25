<?php
/*--------------------------------------------------------------
   UpdateImageListAction.php 2021-09-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList\App\Actions;

use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateImageListAction
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class UpdateImageListAction
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
        $this->factory = $factory;
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
        $errors = $this->validator->validateUpdateImageListRequest($parsedBody = $request->getParsedBody());
        
        if (empty($errors) === false) {
            
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            
            $imageLists = [];
            
            foreach ($parsedBody as ['id' => $id, 'name' => $name]) {
            
                $imageLists[] = $imageList = $this->readService->getImageListById((int)$id);
                
                $imageList->changeName($this->factory->createImageListName($name));
            }
            
            $this->writeService->storeImageLists(...$imageLists);
    
            return $response->withStatus(204);
            
        } catch (\Exception $exception) {
    
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
        
    }
}