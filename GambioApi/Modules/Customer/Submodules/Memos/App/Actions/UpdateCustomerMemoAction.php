<?php
/*--------------------------------------------------------------
   UpdateCustomerMemoAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\CustomerMemo;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoReadService;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoWriteService;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\CustomerMemoApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;
use Webmozart\Assert\Assert;

/**
 * Class UpdateCustomerMemoAction
 *
 * @package Gambio\Api\Modules\ImageList\App\Actions
 */
class UpdateCustomerMemoAction
{
    private CustomerMemoReadService         $readService;
    private CustomerMemoWriteService        $writeService;
    private CustomerMemoApiRequestValidator $validator;
    
    
    /**
     * @param CustomerMemoReadService         $readService
     * @param CustomerMemoWriteService        $writeService
     * @param CustomerMemoApiRequestValidator $validator
     */
    public function __construct(
        CustomerMemoReadService         $readService,
        CustomerMemoWriteService        $writeService,
        CustomerMemoApiRequestValidator $validator
    ) {
        $this->readService  = $readService;
        $this->writeService = $writeService;
        $this->validator    = $validator;
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
        $errors = $this->validator->validateUpdateRequest($parsedBody = $request->getParsedBody());
        
        if (empty($errors) === false) {
    
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $customerId = (int)$request->getAttribute('customerId');
        
        try {
    
            $memos = [];
    
            foreach ($parsedBody as ['id' => $id, 'content' => $content]) {
        
                $memos[] = $memo = $this->readService->getCustomerMemoById((int)$id);
                
                $this->validateMemoBelongsToCustomer($memo, $customerId);
                
                $memo->changeContent($content);
            }
            
            $this->writeService->storeCustomerMemos(...$memos);
    
            return $response->withStatus(204);
        } catch (Exception $exception) {
    
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
    
    
    /**
     * @param CustomerMemo $memo
     * @param int          $customerId
     *
     * @return void
     */
    private function validateMemoBelongsToCustomer(CustomerMemo $memo, int $customerId): void
    {
        Assert::same($memo->customerId(),
                     $customerId,
                     'Expected the customer memo to belong to customer id %2$s. Got: %s');
    }
}