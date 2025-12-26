<?php
/*--------------------------------------------------------------
   FetchAllCurrenciesAction.php 2022-06-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Currency\App\Actions;

use Exception;
use Gambio\Admin\Modules\Currency\Services\CurrencyFilterService as CurrencyFilterServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Application\Responses\ResponseDataTrimmerTrait;
use Gambio\Api\Modules\Currency\App\CurrencyApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchAllCurrenciesAction
 *
 * @package Gambio\Api\Modules\Currency\App\Actions
 */
class FetchAllCurrenciesAction
{
    use CreateApiMetaDataTrait;
    use ResponseDataTrimmerTrait;
    
    /**
     * @var CurrencyFilterServiceInterface
     */
    private $service;
    
    /**
     * @var CurrencyApiRequestParser
     */
    private $parser;
    
    
    /**
     * @param CurrencyFilterServiceInterface $service
     * @param CurrencyApiRequestParser       $parser
     */
    public function __construct(CurrencyFilterServiceInterface $service, CurrencyApiRequestParser $parser)
    {
        $this->service = $service;
        $this->parser  = $parser;
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
    
    
        try {
            $currencies = $this->service->filterCurrencies($filters, $sorting, $limit, $offset);
            $totalItems = $this->service->getCurrenciesTotalCount($filters);
        } catch (Exception $exception) {
        
            return $response->withStatus(422)->withJson(['error' => [[$exception->getMessage()]]]);
        }
        
        $metaData   = $this->createApiCollectionMetaData($page,
                                                         $limit,
                                                         $totalItems,
                                                         $this->parser->getResourceUrlFromRequest($request),
                                                         $request->getQueryParams());
        
        $responseData = $currencies->toArray();
        
        if (count($fields) > 0) {
            
            $responseData = $this->trimCollectionData($responseData, $fields);
        }
        
        return $response->withJson(['data' => $responseData, '_meta' => $metaData,]);
    }
}