<?php
/*--------------------------------------------------------------
   DeleteMultipleCustomerMemosAction.php 2022-09-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteMultipleCustomerMemosAction
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App\Actions\Json
 * @codeCoverageIgnore
 */
class DeleteCustomerMemoAction
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
            $customerMemoId = (int)$request->getAttribute('customerMemoId');
            
            $this->writeService->deleteCustomerMemosByMemoIds($customerMemoId);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            
            return $response->withStatus(409)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}