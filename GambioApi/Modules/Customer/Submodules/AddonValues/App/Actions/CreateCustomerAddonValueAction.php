<?php
/*--------------------------------------------------------------
   CreateCustomerAddonValueAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\CustomerAddonValueWriteService;
use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services\Exceptions\CustomerAddonValueAlreadyExistsException;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueApiRequestParser;
use Gambio\Api\Modules\Customer\Submodules\AddonValues\App\CustomerAddonValueApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class CreateCustomerAddonValueAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\AddonValues\App\Actions
 */
class CreateCustomerAddonValueAction
{
    use CreateApiMetaDataTrait;
    
    private CustomerAddonValueWriteService        $writeService;
    private CustomerAddonValueApiRequestValidator $validator;
    private CustomerAddonValueApiRequestParser    $parser;
    
    
    /**
     * @param CustomerAddonValueWriteService        $writeService
     * @param CustomerAddonValueApiRequestValidator $validator
     * @param CustomerAddonValueApiRequestParser    $parser
     */
    public function __construct(
        CustomerAddonValueWriteService        $writeService,
        CustomerAddonValueApiRequestValidator $validator,
        CustomerAddonValueApiRequestParser    $parser
    ) {
        $this->writeService = $writeService;
        $this->validator    = $validator;
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
        $customerId = (int)$request->getAttribute('customerId');
        
        $errors = $this->validator->validateCreateRequest($request->getParsedBody());
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $creationArguments = $this->parser->parseCustomerAddonValueDataForCreation($request, $customerId, $errors);
        if (count($errors) > 0) {
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $customerAddonValueIds = $this->writeService->createMultipleCustomerAddonValues(...$creationArguments);
        } catch (CustomerAddonValueAlreadyExistsException $e) {
            return $response->withJson(['errors' => [$e->getMessage()],], 409);
        }
        
        $responseData = array_map(function (array $customerAddonValueIdData): string {
            return $customerAddonValueIdData['key'];
        },
            $customerAddonValueIds->toArray());
        
        $links   = [];
        $baseUrl = rtrim($this->parser->getResourceUrlFromRequest($request), '/');
        foreach ($customerAddonValueIds as $id) {
            $links[] = $baseUrl . '/customers/' . $id->customerId() . '/addon-values/' . $id->key();
        }
        $metaData = $this->createApiMetaData($links);
        
        return $response->withJson([
                                       'data'  => $responseData,
                                       '_meta' => $metaData,
                                   ],
                                   201);
    }
}