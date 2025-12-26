<?php
/*--------------------------------------------------------------
   FetchSpecificImageListAction.php 2021-08-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Actions\Json;

use Gambio\Admin\Modules\ImageList\Services\Exceptions\ImageListDoesNotExistException;
use Gambio\Admin\Modules\ImageList\Services\ImageListReadService as ImageListReadServiceInterface;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchSpecificImageListAction
 * @package Gambio\Admin\Modules\ImageList\App\Actions\Json
 */
class FetchSpecificImageListAction extends AbstractAction
{
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
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        try {
            $imageListId = (int)$request->getAttribute('imageListId');
            $imageList   = $this->service->getImageListById($imageListId);
        
            return $response->withJson(['data' => $imageList->toArray()]);
        } catch (ImageListDoesNotExistException $exception) {
            return $response->withStatus(404);
        }
    }
}