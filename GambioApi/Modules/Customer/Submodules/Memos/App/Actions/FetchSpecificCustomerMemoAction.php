<?php
/*--------------------------------------------------------------
   FetchSpecificCustomerMemoAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoReadService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\CustomerMemoDoesNotExistException;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\CustomerMemoApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchSpecificCustomerMemoAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions
 */
class FetchSpecificCustomerMemoAction
{
    use CreateApiMetaDataTrait;
    
    private CustomerMemoReadService      $readService;
    private CustomerMemoApiRequestParser $parser;
    
    
    /**
     * @param CustomerMemoReadService      $readService
     * @param CustomerMemoApiRequestParser $parser
     */
    public function __construct(
        CustomerMemoReadService $readService,
        CustomerMemoApiRequestParser $parser
    ) {
        $this->readService = $readService;
        $this->parser = $parser;
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
            $memoId     = (int)$request->getAttribute('memoId');
            $memo       = $this->readService->getCustomerMemoById($memoId);
            
            if ($memo->customerId() !== $customerId) {
                
                throw new Exception(sprintf('Memo with the ID "%s" belongs to the customer with ID "%s"',
                                            $memoId,
                                            $memo->customerId()));
            }
    
            $baseUrl  = rtrim($request->getUri()->getScheme() . '://' . $request->getUri()->getHost(), '/');
            $links    = [
                'customer' => $baseUrl . 'api.php/v3/customers/' . $customerId,
                'memos'    => $baseUrl . 'api.php/v3/customers/' . $customerId . '/memos',
            ];
            $metaData = $this->createApiMetaData($links);
            
            return $response->withJson([
                                           'data'  => $memo->toArray(CustomerMemoApiRequestParser::DEFAULT_DATE_FORMAT),
                                           '_meta' => $metaData,
                                       ]);
        } catch (CustomerMemoDoesNotExistException $exception) {
            
            return $response->withStatus(404);
        } catch (Exception $exception) {
            
            return $response->withStatus(409)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}