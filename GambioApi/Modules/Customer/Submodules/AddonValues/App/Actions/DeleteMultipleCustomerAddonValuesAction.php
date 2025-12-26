<?php
/*--------------------------------------------------------------
   DeleteMultipleCustomerAddonValuesAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueWriteService;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class DeleteMultipleCustomerAddonValuesAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions
 */
class DeleteMultipleCustomerAddonValuesAction
{
    private CustomerAddonValueWriteService     $writeService;
    private CustomerAddonValueApiRequestParser $parser;
    
    
    /**
     * @param CustomerAddonValueWriteService     $writeService
     * @param CustomerAddonValueApiRequestParser $parser
     */
    public function __construct(
        CustomerAddonValueWriteService     $writeService,
        CustomerAddonValueApiRequestParser $parser
    ) {
        $this->writeService = $writeService;
        $this->parser       = $parser;
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
        $addonValueIds = $this->parser->parseCustomerAddonValueIdsForDeletion($request);
        $this->writeService->deleteCustomerAddonValuesByIds(...$addonValueIds);
        
        return $response->withStatus(204);
    }
}