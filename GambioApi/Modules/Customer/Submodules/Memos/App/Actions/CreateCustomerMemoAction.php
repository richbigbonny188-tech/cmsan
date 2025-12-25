<?php
/*--------------------------------------------------------------
   CreateCustomerMemoAction.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

namespace Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Services\CustomerMemoWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\CustomerMemoApiRequestParser;
use Gambio\Api\Modules\Customer\Submodules\Memos\App\CustomerMemoApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class CreateCustomerMemoAction
 *
 * @package Gambio\Api\Modules\Customer\Submodules\Memos\App\Actions
 */
class CreateCustomerMemoAction
{
    use CreateApiMetaDataTrait;
    
    private CustomerMemoWriteService        $writeService;
    private CustomerMemoApiRequestValidator $validator;
    private CustomerMemoApiRequestParser    $parser;
    
    
    /**
     * @param CustomerMemoWriteService        $writeService
     * @param CustomerMemoApiRequestValidator $validator
     * @param CustomerMemoApiRequestParser    $parser
     */
    public function __construct(
        CustomerMemoWriteService        $writeService,
        CustomerMemoApiRequestValidator $validator,
        CustomerMemoApiRequestParser    $parser
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
        $errors = $this->validator->validateCreateRequest($request->getParsedBody());
        
        if (empty($errors) === false) {
            
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            $customerId        = (int)$request->getAttribute('customerId');
            $creationArguments = $this->parser->parseCustomerMemoDataForCreation($request, $customerId, $errors);
            
            if (count($errors) > 0) {
                
                return $response->withStatus(422)->withJson(['errors' => $errors]);
            }
            
            $customerMemoIds = $this->writeService->createMultipleCustomerMemos(...$creationArguments);
            
            $links   = [];
            $baseUrl = rtrim($this->parser->getResourceUrlFromRequest($request), '/');
            foreach ($customerMemoIds as $id) {
                
                $links[] = $baseUrl . '/' . $id->value();
            }
            
            $metaData = $this->createApiMetaData($links);
            
            return $response->withStatus(201)->withJson([
                                                            'data'  => $customerMemoIds->toArray(),
                                                            '_meta' => $metaData,
                                                        ]);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}