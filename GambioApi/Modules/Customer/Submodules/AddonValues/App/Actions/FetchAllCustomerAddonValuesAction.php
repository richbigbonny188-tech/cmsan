<?php
/*--------------------------------------------------------------
   FetchAllCustomerAddonValuesAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueFilterService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllCustomerAddonValuesAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions
 */
class FetchAllCustomerAddonValuesAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    private CustomerAddonValueApiRequestParser $parser;
    private CustomerAddonValueFilterService    $filterService;
    
    
    /**
     * @param CustomerAddonValueApiRequestParser $parser
     * @param CustomerAddonValueFilterService    $filterService
     */
    public function __construct(
        CustomerAddonValueApiRequestParser $parser,
        CustomerAddonValueFilterService    $filterService
    ) {
        $this->parser        = $parser;
        $this->filterService = $filterService;
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
        $customerId = (int)$request->getAttribute('customerId');
        $fields     = $this->parser->getFields($request);
        $filters    = $this->parser->getFilters($request);
        $sorting    = $this->parser->getSorting($request);
        $page       = $this->parser->getPage($request);
        $limit      = $this->parser->getPerPage($request);
        $offset     = $limit * ($page - 1);
        
        $customerAddonValues = $this->filterService->filterCustomerAddonValues($customerId,
                                                                               $filters,
                                                                               $sorting,
                                                                               $limit,
                                                                               $offset);
        $totalItems          = $this->filterService->getCustomerAddonValuesTotalCount($customerId, $filters);
        $metaData            = $this->createApiCollectionMetaData($page,
                                                                  $limit,
                                                                  $totalItems,
                                                                  $this->parser->getResourceUrlFromRequest($request),
                                                                  $request->getQueryParams());
        $responseData        = array_map(function (array $customerAddonValueData): array {
            unset($customerAddonValueData['customerId']);
        
            return $customerAddonValueData;
        }, $customerAddonValues->toArray());
    
        if (count($fields) > 0) {
            $responseData = $this->trimCollectionData($responseData, $fields);
        }
    
        return $response->withJson([
                                       'data'  => $responseData,
                                       '_meta' => $metaData,
                                   ]);
    }
}