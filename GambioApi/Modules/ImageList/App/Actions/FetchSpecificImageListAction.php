<?php
/*--------------------------------------------------------------
   FetchSpecificImageListAction.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList\App\Actions;

use Exception;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchSpecificImageListAction
 *
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class FetchSpecificImageListAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var ImageListReadServiceInterface
     */
    private $service;
    
    
    /**
     * FetchSpecificImageListAction constructor.
     *
     * @param ImageListReadServiceInterface $service
     */
    public function __construct(ImageListReadServiceInterface $service)
    {
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
            $imageListId = (int)$request->getAttribute('imageListId');
            $imageList   = $this->service->getImageListById($imageListId)->toArray();
            
            return $response->withJson([
                                           'data'  => $imageList,
                                           '_meta' => $this->createApiMetaData(),
                                       ]);
        } catch (Exception $exception) {
            return $response->withStatus(404);
        }
    }
}