<?php
/*--------------------------------------------------------------
   SearchCustomerAction.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use DateTimeInterface;
use Exception;
use Gambio\Admin\Modules\Customer\App\CustomerAdditionalInformationRepository;
use Gambio\Admin\Modules\Customer\Services\CustomerSearchService as CustomerSearchServiceInterface;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class SearchCustomerAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class SearchCustomerAction
{
    use CreateApiMetaDataTrait;
    
    private CustomerSearchServiceInterface          $service;
    private string                                  $datetimeFormat;
    private CustomerAdditionalInformationRepository $additionalInformationRepository;
    
    
    /**
     * @param CustomerSearchServiceInterface $service
     */
    public function __construct(
        CustomerSearchServiceInterface          $service,
        CustomerAdditionalInformationRepository $additionalInformationRepository
    ) {
        $this->service                         = $service;
        $this->datetimeFormat                  = DateTimeInterface::ATOM;
        $this->additionalInformationRepository = $additionalInformationRepository;
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
        try {
            $searchTerm = $this->getSearchTerm($request);
            $sorting    = $this->getSorting($request);
            $page       = $this->getPage($request);
            $limit      = $this->getPerPage($request);
            $offset     = $limit * ($page - 1);
            
            $customers  = $this->service->searchCustomers($searchTerm, $sorting, $limit, $offset);
            $totalItems = $this->service->getSearchedCustomerTotalCount($searchTerm);
            $metaData   = $this->createApiCollectionMetaData($page,
                                                             $limit,
                                                             $totalItems,
                                                             $this->getResourceUrlFromRequest($request),
                                                             $request->getQueryParams());
            
            return $response->withJson([
                                           'data'  => [
                                               'customers'             => $customers->toArray($this->datetimeFormat),
                                               'additionalInformation' => $this->additionalInformationRepository->getAdditionalInformation($this->datetimeFormat,
                                                   ...
                                                                                                                                           array_column($customers->toArray(),
                                                                                                                                                        'id')),
                                           ],
                                           '_meta' => $metaData,
                                       ]);
        } catch (Exception $exception) {
            
            return $response->withStatus(409)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
    
    
    /**
     * @param Request $request
     *
     * @return string
     */
    private function getSearchTerm(Request $request): string
    {
        return $request->getQueryParam('term', '');
    }
    
    
    /**
     * @param Request $request
     *
     * @return string|null
     */
    private function getSorting(Request $request): ?string
    {
        return $request->getQueryParam('sort');
    }
    
    
    /**
     * @param Request $request
     *
     * @return int
     */
    private function getPage(Request $request): int
    {
        return (int)$request->getQueryParam('page', 1);
    }
    
    
    /**
     * @param Request $request
     *
     * @return int
     */
    private function getPerPage(Request $request): int
    {
        return (int)$request->getQueryParam('per-page', 25);
    }
    
    
    /**
     * @param Request $request
     *
     * @return string
     */
    private function getResourceUrlFromRequest(Request $request): string
    {
        return $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getPath();
    }
}