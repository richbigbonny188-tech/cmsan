<?php
/*--------------------------------------------------------------
   CreateImageListAction.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ImageList\App\Actions;

use Exception;
use Gambio\Admin\Modules\ImageList\Services\ImageListFactory;
use Gambio\Admin\Modules\ImageList\Services\ImageListWriteService as ImageListWriteServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestParser;
use Gambio\Api\Modules\ImageList\App\ImageListApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class CreateImageListAction
 *
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class CreateImageListAction
{
    use CreateApiMetaDataTrait;
    
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
     * @var ImageListApiRequestParser
     */
    private $parser;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * CreateImageListAction constructor.
     *
     * @param ImageListWriteServiceInterface $service
     * @param ImageListApiRequestValidator   $validator
     * @param ImageListApiRequestParser      $parser
     * @param ImageListFactory               $factory
     * @param Url                            $url
     */
    public function __construct(
        ImageListWriteServiceInterface $service,
        ImageListApiRequestValidator   $validator,
        ImageListApiRequestParser      $parser,
        ImageListFactory               $factory,
        Url                            $url
    ) {
        $this->service   = $service;
        $this->validator = $validator;
        $this->parser    = $parser;
        $this->factory   = $factory;
        $this->url       = $url;
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
        $errors = $this->validator->validateCreateImageListRequest($parsedBody = $request->getParsedBody());
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            $listNames    = array_map([$this->factory, "createImageListName"], array_column($parsedBody, 'name'));
            $imageListIds = $this->service->createMultipleImageLists(...$listNames);
            $links        = [];
            foreach ($imageListIds as $id) {
                $links[] = $this->url->restApiV3() . '/image-lists/' . $id->value();
            }
            
            return $response->withStatus(201)->withJson([
                                                            'data'  => $imageListIds->toArray(),
                                                            '_meta' => $this->createApiMetaData($links),
                                                        ]);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
    
}