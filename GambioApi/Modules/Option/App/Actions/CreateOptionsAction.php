<?php
/* --------------------------------------------------------------
   CreateOptionsAction.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Gambio\Admin\Modules\Option\Services\OptionWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Option\App\OptionApiRequestParser;
use Gambio\Api\Modules\Option\App\OptionApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class CreateOptionsAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class CreateOptionsAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var OptionApiRequestParser
     */
    private $requestParser;
    
    /**
     * @var OptionApiRequestValidator
     */
    private $requestValidator;
    
    /**
     * @var OptionWriteService
     */
    private $service;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * CreateOptionsAction constructor.
     *
     * @param OptionApiRequestParser    $requestParser
     * @param OptionApiRequestValidator $requestValidator
     * @param OptionWriteService        $service
     * @param Url                       $url
     */
    public function __construct(
        OptionApiRequestParser    $requestParser,
        OptionApiRequestValidator $requestValidator,
        OptionWriteService        $service,
        Url                       $url
    ) {
        $this->requestParser    = $requestParser;
        $this->requestValidator = $requestValidator;
        $this->service          = $service;
        $this->url              = $url;
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
        $errors = $this->requestValidator->validateOptionPostRequestBody($request->getParsedBody());
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->requestParser->parseOptionDataForCreation($request, $errors);
        if (count($errors) > 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        $ids = $this->service->createMultipleOptions(...$creationArguments);
        
        $links = [];
        foreach ($ids as $id) {
            $links[] = $this->url->restApiV3() . '/options/' . $id->value();
        }
        
        $metaData = $this->createApiMetaData($links);
        
        return $response->withJson([
                                       'data'  => $ids->toArray(),
                                       '_meta' => $metaData,
                                   ],
                                   201);
    }
}