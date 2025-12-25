<?php
/*--------------------------------------------------------------
   ChangeCustomerGroupAction.php 2023-11-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Customer\App\AdminAccessUserRepository;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class ChangeCustomerGroupAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class ChangeCustomerGroupAction
{
    private const CUSTOMER_GROUP_ADMIN_ID = 0;
    
    
    /**
     * @param CustomerReadService       $readService
     * @param CustomerWriteService      $writeService
     * @param CustomerFactory           $factory
     * @param AdminAccessUserRepository $adminAccessRepository
     */
    public function __construct(
        private CustomerReadService       $readService,
        private CustomerWriteService      $writeService,
        private CustomerFactory           $factory,
        private AdminAccessUserRepository $adminAccessRepository
    ) {
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
            $customers                    = [];
            $customersToRemoveAdminAccess = [];
            
            foreach ($request->getParsedBody() as ['id' => $id, 'groupId' => $groupId]) {
                $customer = $this->readService->getCustomerById((int)$id);
                
                // if the customer group is still the same, skip
                if ($customer->customerGroup() === (int)$groupId) {
                    continue;
                }
                
                // if the old customer group was Admin, we add the customer to the delete admin access array
                if ($customer->customerGroup() === self::CUSTOMER_GROUP_ADMIN_ID) {
                    $customersToRemoveAdminAccess[] = $customer;
                }
                
                $customers[]   = $customer;
                $customerGroup = $this->factory->createCustomerGroup((int)$groupId);
                
                $customer->changeCustomerGroup($customerGroup);
            }
            
            if (count($customersToRemoveAdminAccess)) {
                $this->adminAccessRepository->deleteAdminAccessByCustomers(...$customersToRemoveAdminAccess);
            }
            
            $this->writeService->storeCustomers(...$customers);
            
            return $response->withStatus(204);
        } catch (Exception $exception) {
            return $response->withJson(409)->withJson(['errors' => [$exception->getMessage()]]);
        }
    }
}