<?php
/*--------------------------------------------------------------
   DeleteAllCustomerAddonValuesAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteAllCustomerAddonValuesAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions
 */
class DeleteAllCustomerAddonValuesAction
{
    private CustomerAddonValueWriteService $writeService;
    
    
    /**
     * @param CustomerAddonValueWriteService $writeService
     */
    public function __construct(CustomerAddonValueWriteService $writeService)
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
        $customerId = (int)$request->getAttribute('customerId');
        $this->writeService->deleteCustomerAddonValuesByCustomerIds($customerId);
        
        return $response->withStatus(204);
    }
}