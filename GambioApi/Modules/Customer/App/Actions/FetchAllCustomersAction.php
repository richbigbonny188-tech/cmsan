<?php
/*--------------------------------------------------------------
   FetchAllCustomersAction.php 2022-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Customer\App\Actions;

use Gambio\Admin\Modules\Customer\Services\CustomerFilterService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Api\Modules\Customer\App\CustomerApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllCustomersAction
 *
 * @package Gambio\Api\Modules\Customer\App\Actions
 */
class FetchAllCustomersAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    private CustomerApiRequestParser $parser;
    private CustomerFilterService    $service;
    
    
    /**
     * @param CustomerApiRequestParser $parser
     * @param CustomerFilterService    $service
     */
    public function __construct(
        CustomerApiRequestParser $parser,
        CustomerFilterService    $service
    ) {
        $this->parser  = $parser;
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
        $fields  = $this->parser->getFields($request);
        $filters = $this->parser->getFilters($request);
        $sorting = $this->parser->getSorting($request);
        $page    = $this->parser->getPage($request);
        $limit   = $this->parser->getPerPage($request);
        $offset  = $limit * ($page - 1);
        
        $customers    = $this->service->filterCustomers($filters, $sorting, $limit, $offset);
        $totalItems   = $this->service->getCustomersTotalCount($filters);
        $metaData     = $this->createApiCollectionMetaData($page,
                                                           $limit,
                                                           $totalItems,
                                                           $this->parser->getResourceUrlFromRequest($request),
                                                           $request->getQueryParams());
        $responseData = $customers->toArray(CustomerApiRequestParser::DEFAULT_DATE_FORMAT);
        
        if (count($fields) > 0) {
            
            $responseData = $this->trimCollectionData($responseData, $fields);
        }
        
        return $response->withJson([
                                       'data'  => $responseData,
                                       '_meta' => $metaData,
                                   ]);
    }
}