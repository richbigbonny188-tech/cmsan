<?php
/*--------------------------------------------------------------
   FetchAllProductVariantsAction.php 2023-06-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Api\Modules\Product\Submodules\Variant\App\Actions;

use Gambio\Admin\Modules\Product\Services\ProductVariantsFilterService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Api\Modules\Product\Submodules\Variant\App\ProductVariantApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllProductVariantsAction
 * @package Gambio\Api\Modules\ProductVariant\App\Actions
 */
class FetchAllProductVariantsAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    /**
     * @var ProductVariantApiRequestParser
     */
    private $parser;
    
    /**
     * @var ProductVariantsFilterService
     */
    private $service;
    
    
    /**
     * FetchAllProductVariantsAction constructor.
     *
     * @param ProductVariantApiRequestParser $parser
     * @param ProductVariantsFilterService   $service
     */
    public function __construct(
        ProductVariantApiRequestParser $parser,
        ProductVariantsFilterService $service
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
    
        $productId  = (int)$request->getAttribute('productId');
        $sorting    = $this->parser->getSorting($request);
        $page       = $this->parser->getPage($request);
        $limit      = $this->parser->getPerPage($request);
        $offset     = $limit * ($page - 1);
        $variants   = $this->service->filterProductVariants($productId, $filters, $sorting, $limit, $offset);
        $totalCount = $this->service->getProductVariantsTotalCount($productId, $filters);
        $metaData   = $this->createApiCollectionMetaData($page,
                                                         $limit,
                                                         $totalCount,
                                                         $this->parser->getResourceUrlFromRequest($request),
                                                         $request->getQueryParams());
        
        $responseData = $variants->toArray();
        
        if (count($fields) > 0) {
            
            $responseData = $this->trimCollectionData($responseData, $fields);
        }
        
        return $response->withJson([
                                       'data'  => $responseData,
                                       '_meta' => $metaData,
                                   ]);
    }
}