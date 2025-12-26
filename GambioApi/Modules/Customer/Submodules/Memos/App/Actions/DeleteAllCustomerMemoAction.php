<?php
/*--------------------------------------------------------------
   DeleteAllCustomerMemoAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteAllCustomerMemoAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions
 */
class DeleteAllCustomerMemoAction
{
    private CustomerMemoWriteService $writeService;
    
    
    /**
     * @param CustomerMemoWriteService $writeService
     */
    public function __construct(CustomerMemoWriteService $writeService)
    {
        $this->writeService = $writeService;
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
            
            $this->writeService->deleteCustomerMemosByCustomerIds($customerId);
    
            return $response->withStatus(204);
        } catch (Exception $exception) {
    
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}