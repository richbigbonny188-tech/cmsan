<?php
/* --------------------------------------------------------------
   CreateParcelServicesAction.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\ParcelService\App\Actions;

use Gambio\Admin\Modules\ParcelService\Services\Exceptions\CreationOfParcelServicesFailedException;
use Gambio\Admin\Modules\ParcelService\Services\ParcelServiceWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\ParcelService\App\ParcelServiceApiRequestParser;
use Gambio\Api\Modules\ParcelService\App\ParcelServiceApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class CreateParcelServicesAction
 *
 * @package Gambio\Api\Modules\ParcelService\App\Actions
 */
class CreateParcelServicesAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var ParcelServiceApiRequestParser
     */
    private $requestParser;
    
    /**
     * @var ParcelServiceApiRequestValidator
     */
    private $requestValidator;
    
    /**
     * @var ParcelServiceWriteService
     */
    private $service;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * CreateParcelServicesAction constructor.
     *
     * @param ParcelServiceApiRequestParser    $requestParser
     * @param ParcelServiceApiRequestValidator $requestValidator
     * @param ParcelServiceWriteService        $service
     * @param Url                              $url
     */
    public function __construct(
        ParcelServiceApiRequestParser    $requestParser,
        ParcelServiceApiRequestValidator $requestValidator,
        ParcelServiceWriteService        $service,
        Url                              $url
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
     *
     * @throws CreationOfParcelServicesFailedException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $errors = $this->requestValidator->validatePostRequestBody($request->getParsedBody());
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->requestParser->parseParcelServiceDataForCreation($request, $errors);
        if (count($errors) > 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        $ids = $this->service->createMultipleParcelServices(...$creationArguments);
        
        $links = [];
        foreach ($ids as $id) {
            $links[] = $this->url->restApiV3() . '/parcel-services/' . $id->value();
        }
        
        $metaData = $this->createApiMetaData($links);
        
        return $response->withJson([
                                       'data'  => $ids->toArray(),
                                       '_meta' => $metaData,
                                   ],
                                   201);
    }
}