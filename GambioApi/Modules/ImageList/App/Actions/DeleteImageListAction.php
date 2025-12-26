<?php
/*--------------------------------------------------------------
   DeleteImageListAction.php 2021-06-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList\App\Actions;

use Exception;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteImageListAction
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class DeleteImageListAction
{
    /**
     * @var ImageListWriteServiceInterface
     */
    private $service;
    
    
    /**
     * DeleteImageListAction constructor.
     *
     * @param ImageListWriteServiceInterface $service
     */
    public function __construct(
        ImageListWriteServiceInterface $service
    ) {
        $this->service = $service;
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
        try {
    
            if (($imageListIds = $request->getAttribute('imageListIds')) !== 0) {
        
                $imageListIds = array_map('intval', explode(',', $imageListIds));
                
                $this->service->deleteImageLists(...$imageListIds);
            }
    
            return $response->withStatus(204);
        } catch (Exception $exception) {
            
            $statusCode = $exception instanceof OperationHasNotBeenPermittedException ? 409 : 422;
    
            return $response->withStatus($statusCode)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}