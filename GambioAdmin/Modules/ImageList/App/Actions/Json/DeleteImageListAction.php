<?php
/*--------------------------------------------------------------
   DeleteImageListAction.php 2021-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\ImageList\Services\Exceptions\OperationHasNotBeenPermittedException;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteImageListAction
 * @package Gambio\Admin\Modules\ImageList\App\Actions\Json
 */
class DeleteImageListAction extends AbstractAction
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
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
    
            $imageListIds = $request->getAttribute('imageListIds');
            $imageListIds = array_map('intval', explode(',', $imageListIds));
            
            if (count($imageListIds)) {
            
                $this->service->deleteImageLists(...$imageListIds);
            }
        
            return $response->withStatus(204);
        } catch (Exception $exception) {
        
            $statusCode = $exception instanceof OperationHasNotBeenPermittedException ? 409 : 422;
        
            return $response->withStatus($statusCode)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}