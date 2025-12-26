<?php
/*--------------------------------------------------------------
   FetchSpecificCustomerAction.php 2022-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Customer\App\Actions;

use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoReadService;
use Gambio\Api\Application\Responses\ApiMetaData;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Customer\App\CustomerApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchSpecificCustomerAction
 *
 * @package Gambio\Api\Modules\Customer\App\Actions
 */
class FetchSpecificCustomerAction
{
    use CreateApiMetaDataTrait;
    
    private CustomerReadService      $customerReadService;
    private CustomerMemoReadService  $customerMemoReadService;
    private CustomerApiRequestParser $parser;
    
    
    /**
     * @param CustomerReadService      $customerReadService
     * @param CustomerMemoReadService  $customerMemoReadService
     * @param CustomerApiRequestParser $parser
     */
    public function __construct(
        CustomerReadService      $customerReadService,
        CustomerMemoReadService  $customerMemoReadService,
        CustomerApiRequestParser $parser
    ) {
        $this->customerReadService     = $customerReadService;
        $this->customerMemoReadService = $customerMemoReadService;
        $this->parser                  = $parser;
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
            $customerId = (int)$request->getAttribute('customerId');
            $customer   = $this->customerReadService->getCustomerById($customerId);
            
            return $response->withJson([
                                           'data'  => $customer->toArray(CustomerApiRequestParser::DEFAULT_DATE_FORMAT),
                                           '_meta' => $this->createMemoLinksForCustomer($request),
                                       ]);
        } catch (CustomerDoesNotExistException $e) {
            return $response->withStatus(404);
        }
    }
    
    
    /**
     * @param Request $request
     *
     * @return ApiMetaData
     */
    private function createMemoLinksForCustomer(Request $request): ApiMetaData
    {
        $baseUrl = rtrim($this->parser->getResourceUrlFromRequest($request), '/');
        $links   = [
            'memos'        => $baseUrl . '/memos',
            'addon-values' => $baseUrl . '/addon-values',
        ];
        
        return $this->createApiMetaData($links);
    }
}