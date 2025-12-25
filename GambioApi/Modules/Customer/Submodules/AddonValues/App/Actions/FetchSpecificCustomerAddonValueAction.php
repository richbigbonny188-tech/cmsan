<?php
/*--------------------------------------------------------------
   FetchSpecificCustomerAddonValueAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueReadService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueDoesNotExistException;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueApiRequestParser;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class FetchSpecificCustomerAddonValueAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions
 */
class FetchSpecificCustomerAddonValueAction
{
    use CreateApiMetaDataTrait;
    
    private CustomerAddonValueReadService      $readService;
    private CustomerAddonValueApiRequestParser $parser;
    
    
    /**
     * @param CustomerAddonValueReadService      $readService
     * @param CustomerAddonValueApiRequestParser $parser
     */
    public function __construct(CustomerAddonValueReadService $readService, CustomerAddonValueApiRequestParser $parser)
    {
        $this->readService = $readService;
        $this->parser      = $parser;
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
            $customerId    = (int)$request->getAttribute('customerId');
            $addonValueKey = $request->getAttribute('key');
            $addonValue    = $this->readService->getCustomerAddonValue($customerId, $addonValueKey);
            $responseData  = $addonValue->toArray();
            unset($responseData['customerId']);
    
            $baseUrl = rtrim($this->parser->getResourceUrlFromRequest($request), '/');
            $links   = [
                'customer' => $baseUrl . '/customer/' . $customerId,
            ];
    
            return $response->withJson([
                                           'data'  => $responseData,
                                           '_meta' => $this->createApiMetaData($links),
                                       ]);
        } catch (CustomerAddonValueDoesNotExistException $exception) {
            return $response->withStatus(404);
        }
    }
}