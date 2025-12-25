<?php
/* --------------------------------------------------------------
   FetchAllOptionsAction.php 2020-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Option\App\Actions;

use Gambio\Admin\Modules\Option\Services\OptionFilterService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Api\Modules\Option\App\OptionApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllOptionsAction
 *
 * @package Gambio\Api\Modules\Option\App\Actions
 */
class FetchAllOptionsAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    /**
     * @var OptionApiRequestParser
     */
    private $requestParser;
    
    /**
     * @var OptionFilterService
     */
    private $service;
    
    
    /**
     * FetchAllOptionsAction constructor.
     *
     * @param OptionApiRequestParser $requestParser
     * @param OptionFilterService    $service
     */
    public function __construct(OptionApiRequestParser $requestParser, OptionFilterService $service)
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
        $fields  = $this->requestParser->getFields($request);
        $filters = $this->requestParser->getFilters($request);
        $sorting = $this->requestParser->getSorting($request);
        $page    = $this->requestParser->getPage($request);
        $limit   = $this->requestParser->getPerPage($request);
        $offset  = $limit * ($page - 1);
        
        $options    = $this->service->filterOptions($filters, $sorting, $limit, $offset);
        $totalItems = $this->service->getOptionsTotalCount($filters);
        $metaData   = $this->createApiCollectionMetaData($page,
                                                         $limit,
                                                         $totalItems,
                                                         $this->requestParser->getResourceUrlFromRequest($request),
                                                         $request->getQueryParams());
        
        $responseData = $options->toArray();
        if (count($fields) > 0) {
            $responseData = $this->trimCollectionData($responseData, $fields);
        }
        foreach ($responseData as $index => $documentData) {
            unset($responseData[$index]['newValues']);
        }
        
        return $response->withJson([
                                       'data'  => $responseData,
                                       '_meta' => $metaData,
                                   ]);
    }
}