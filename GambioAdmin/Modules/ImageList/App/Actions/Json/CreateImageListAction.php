<?php
/*--------------------------------------------------------------
   CreateImageListAction.php 2022-08-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Actions\Json;

use Gambio\Admin\Modules\ImageList\Services\Exceptions\CreationOfImageListsFailedException;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestValidator;
use Gambio\Core\Application\Http\AbstractAction;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class CreateImageListAction
 *
 * @package Gambio\Admin\Modules\ImageList\App\Actions\Json
 */
class CreateImageListAction extends AbstractAction
{
    /**
     * @var ImageListWriteServiceInterface
     */
    private $service;
    
    /**
     * @var ImageListApiRequestValidator
     */
    private $validator;
    
    /**
     * @var ImageListFactory
     */
    private $factory;
    
    
    /**
     * CreateImageListAction constructor.
     *
     * @param ImageListWriteServiceInterface $service
     * @param ImageListApiRequestValidator   $validator
     * @param ImageListFactory               $factory
     */
    public function __construct(
        ImageListWriteServiceInterface $service,
        ImageListApiRequestValidator   $validator,
        ImageListFactory               $factory
    ) {
        $this->service   = $service;
        $this->validator = $validator;
        $this->factory   = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function handle(Request $request, Response $response): Response
    {
        $parsedBody = $request->getParsedBody();
        $errors     = $this->validator->validateCreateImageListRequest($parsedBody);
        
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            // use closure to avoiding "array_shift(): Argument #1 ($array) must be passed by reference, value given"
            $listNames    = array_map(function($array){return array_shift($array);}, $parsedBody);
            $listNames    = array_map([$this->factory, "createImageListName"], $listNames);
            $imageListIds = $this->service->createMultipleImageLists(...$listNames);
            
            return $response->withStatus(201)->withJson(['data' => $imageListIds->toArray()]);
        } catch (CreationOfImageListsFailedException $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}