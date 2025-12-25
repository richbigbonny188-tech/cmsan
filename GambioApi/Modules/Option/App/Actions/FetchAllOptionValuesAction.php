<?php
/* --------------------------------------------------------------
   FetchAllOptionValuesAction.php 2020-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Gambio\Admin\Modules\Option\Services\Exceptions\OptionDoesNotExistException;
use Gambio\Admin\Modules\Option\Services\OptionReadService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Api\Modules\Option\App\OptionApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllOptionValuesAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class FetchAllOptionValuesAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    /**
     * @var OptionApiRequestParser
     */
    private $requestParser;
    
    /**
     * @var OptionReadService
     */
    private $service;
    
    
    /**
     * FetchAllOptionValuesAction constructor.
     *
     * @param OptionApiRequestParser $requestParser
     * @param OptionReadService      $service
     */
    public function __construct(OptionApiRequestParser $requestParser, OptionReadService $service)
    {
        $this->requestParser = $requestParser;
        $this->service       = $service;
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
        $fields = $this->requestParser->getFields($request);
        
        try {
            $optionId = (int)$request->getAttribute('optionId');
            $option   = $this->service->getOptionById($optionId);
        } catch (OptionDoesNotExistException $e) {
            return $response->withStatus(404);
        }
        
        $responseData = $option->values()->toArray();
        if (count($fields) > 0) {
            $responseData = $this->trimCollectionData($responseData, $fields);
        }
        
        return $response->withJson([
                                       'data'  => $responseData,
                                       '_meta' => [],
                                   ]);
    }
}