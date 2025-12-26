<?php
/*--------------------------------------------------------------
   DeleteMultipleCustomerMemosAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoReadService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoWriteService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\Exceptions\CustomerMemoDoesNotExistException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Webmozart\Assert\Assert;

/**
 * Class DeleteMultipleCustomerMemosAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions
 */
class DeleteMultipleCustomerMemosAction
{
    private CustomerMemoWriteService $writeService;
    private CustomerMemoReadService  $readService;
    private int                      $customerId;
    
    
    /**
     * @param CustomerMemoWriteService $writeService
     * @param CustomerMemoReadService  $readService
     */
    public function __construct(
        CustomerMemoWriteService $writeService,
        CustomerMemoReadService  $readService
    ) {
        $this->writeService = $writeService;
        $this->readService  = $readService;
    }
    
    
    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            
            $this->customerId = (int)$request->getAttribute('customerId');
            
            if (empty($memoIds = $request->getAttribute('memoIds')) === false) {
                
                $memoIds = array_map('intval', explode(',', $memoIds));
                array_map([$this, 'validateMemoBelongsToCustomer'], $memoIds);
                
                $this->writeService->deleteCustomerMemosByMemoIds(...$memoIds);
            }
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            
            return $response->withStatus(409)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
    
    
    /**
     * @param int $memoId
     *
     * @return void
     */
    private function validateMemoBelongsToCustomer(int $memoId): void
    {
        try {
            
            $memo = $this->readService->getCustomerMemoById($memoId);
            Assert::same($memo->customerId(),
                         $this->customerId,
                         'Expected the customer memo to belong to customer id %2$s. Got: %s');
        } catch (CustomerMemoDoesNotExistException $exception) {
            unset($exception);
        }
    }
}