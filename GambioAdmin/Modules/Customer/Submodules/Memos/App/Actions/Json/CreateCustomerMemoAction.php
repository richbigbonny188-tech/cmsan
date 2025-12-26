<?php
/*--------------------------------------------------------------
   CreateCustomerMemoAction.php 2022-09-14
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
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class CreateCustomerMemoAction
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App\Actions\Json
 * @codeCoverageIgnore
 */
class CreateCustomerMemoAction
{
    private CustomerMemoWriteService $writeService;
    private UserPreferences          $userPreferences;
    
    
    /**
     * @param CustomerMemoWriteService $writeService
     * @param UserPreferences          $userPreferences
     */
    public function __construct(CustomerMemoWriteService $writeService, UserPreferences $userPreferences)
    {
        $this->writeService    = $writeService;
        $this->userPreferences = $userPreferences;
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
            $body       = $request->getParsedBody();
            $customerId = (int)$body['customerId'];
            $creatorId  = $this->userPreferences->userId();
            $content    = $body['content'];
            
            if ($customerId <= 0) {
                return $response->withJson(['error' => 'Invalid customer ID given. Got: ' . $body['customerId']], 400);
            }
            
            $this->writeService->createCustomerMemo($customerId, $creatorId, $content);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withJson(['error' => $exception->getMessage()], 422);
        }
    }
}