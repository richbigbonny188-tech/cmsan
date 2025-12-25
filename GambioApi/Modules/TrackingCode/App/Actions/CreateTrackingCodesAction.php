<?php
/* --------------------------------------------------------------
   CreateTrackingCodesAction.php 2022-03-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\TrackingCode\App\Actions;

use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\CreationOfTrackingCodesFailedException;
use Gambio\Admin\Modules\TrackingCode\Services\TrackingCodeWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\TrackingCode\App\TrackingCodeApiRequestParser;
use Gambio\Api\Modules\TrackingCode\App\TrackingCodeApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Gambio\Core\Application\ValueObjects\Url;

/**
 * Class CreateTrackingCodesAction
 *
 * @package Gambio\Api\Modules\TrackingCode\App\Actions
 */
class CreateTrackingCodesAction
{
    use CreateApiMetaDataTrait;
    
    /**
     * @var TrackingCodeApiRequestParser
     */
    private $requestParser;
    
    /**
     * @var TrackingCodeApiRequestValidator
     */
    private $requestValidator;
    
    /**
     * @var TrackingCodeWriteService
     */
    private $service;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * CreateTrackingCodesAction constructor.
     *
     * @param TrackingCodeApiRequestParser    $requestParser
     * @param TrackingCodeApiRequestValidator $requestValidator
     * @param TrackingCodeWriteService        $service
     * @param Url                             $url
     */
    public function __construct(
        TrackingCodeApiRequestParser    $requestParser,
        TrackingCodeApiRequestValidator $requestValidator,
        TrackingCodeWriteService        $service,
        Url                             $url
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
     * @throws CreationOfTrackingCodesFailedException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $errors = $this->requestValidator->validatePostRequestBody($request->getParsedBody());
        if (count($errors) > 0) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->requestParser->parseTrackingCodeDataForCreation($request, $errors);
        if (count($errors) > 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        $ids = $this->service->createMultipleTrackingCodes(...$creationArguments);
        
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