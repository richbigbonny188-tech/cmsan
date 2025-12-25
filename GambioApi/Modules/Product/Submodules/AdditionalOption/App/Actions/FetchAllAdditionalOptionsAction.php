<?php
/*--------------------------------------------------------------
   FetchAllAdditionalOptionsAction.php 2023-06-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions;

use Gambio\Admin\Modules\Product\Services\AdditionalOptionFilterService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\AdditionalOptionApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllAdditionalOptionsAction
 *
 * @package Gambio\Api\Modules\Product\Submodules\AdditionalOption\App\Actions
 */
class FetchAllAdditionalOptionsAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    /**
     * FetchAllAdditionalOptionsAction constructor.
     *
     * @param AdditionalOptionApiRequestParser $parser
     * @param AdditionalOptionFilterService    $service
     */
    public function __construct(
        private AdditionalOptionApiRequestParser   $parser,
        private AdditionalOptionFilterService $service
    ) {
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
        $fields     = $this->parser->getFields($request);
        $filters    = $this->parser->getFilters($request);
        $productId  = (int)$request->getAttribute('productId');
        $sorting    = $this->parser->getSorting($request);
        $page       = $this->parser->getPage($request);
        $limit      = $this->parser->getPerPage($request);
        $offset     = $limit * ($page - 1);
        $options    = $this->service->filterAdditionalOptions($productId, $filters, $sorting, $limit, $offset);
        $totalCount = $this->service->getAdditionalOptionsTotalCount($productId, $filters);
        
        $metaData = $this->createApiCollectionMetaData($page,
                                                       $limit,
                                                       $totalCount,
                                                       $this->parser->getResourceUrlFromRequest($request),
                                                       $request->getQueryParams());
        
        $responseData = $options->toArray();
        
        if (count($fields) > 0) {
            $responseData = $this->trimCollectionData($responseData, $fields);
        }
        
        return $response->withJson([
                                       'data'  => $responseData,
                                       '_meta' => $metaData,
                                   ]);
    }
}