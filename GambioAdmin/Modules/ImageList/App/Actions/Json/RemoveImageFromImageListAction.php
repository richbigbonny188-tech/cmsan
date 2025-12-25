<?php
/*--------------------------------------------------------------
   RemoveImageFromImageListAction.php 2021-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class RemoveImageFromImageListAction
 * @package Gambio\Admin\Modules\ImageList\App\Actions\Json
 */
class RemoveImageFromImageListAction extends AbstractAction
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
     * @var ImageListFactory
     */
    private $factory;
    
    
    /**
     * DeleteImageListAction constructor.
     *
     * @param ImageListWriteServiceInterface $writeService
     * @param ImageListReadServiceInterface  $readService
     * @param ImageListFactory               $factory
     */
    public function __construct(
        ImageListWriteServiceInterface $writeService,
        ImageListReadServiceInterface $readService,
        ImageListFactory $factory
    ) {
        $this->writeService = $writeService;
        $this->readService  = $readService;
        $this->factory      = $factory;
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
        
            $imageListId = (int)$request->getAttribute('imageListId');
            $imageList   = $this->readService->getImageListById($imageListId);
            $localPath   = $this->factory->createImagePath($request->getAttribute('relativePath'));
        
            $imageList->removeImage($localPath);
        
            $this->writeService->storeImageLists($imageList);
        
            return $response->withStatus(204);
        
        } catch (Exception $exception) {
        
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}