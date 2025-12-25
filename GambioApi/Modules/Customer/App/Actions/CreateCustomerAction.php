<?php
/*--------------------------------------------------------------
   CreateCustomerAction.php 2022-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Customer\App\Actions;

use Exception;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Customer\App\CustomerApiRequestParser;
use Gambio\Api\Modules\Customer\App\CustomerApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class CreateCustomerAction
 *
 * @package Gambio\Api\Modules\Customer\App\Actions
 */
class CreateCustomerAction
{
    use CreateApiMetaDataTrait;
    
    private CustomerWriteService        $service;
    private CustomerApiRequestValidator $validator;
    private CustomerApiRequestParser    $parser;
    
    
    /**
     * @param CustomerWriteService        $service
     * @param CustomerApiRequestValidator $validator
     * @param CustomerApiRequestParser    $parser
     */
    public function __construct(
        CustomerWriteService        $service,
        CustomerApiRequestValidator $validator,
        CustomerApiRequestParser    $parser
    ) {
        $this->service   = $service;
        $this->validator = $validator;
        $this->parser    = $parser;
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
        
        $creationArguments = $this->parser->parseCustomerDataForCreation($request, $errors);
        
        if (count($errors) > 0) {
            
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $ids      = $this->service->createMultipleCustomers(...$creationArguments['normal']);
            $guestIds = $this->service->createMultipleGuestAccounts(...$creationArguments['guests']);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
        
        $links   = [];
        $baseUrl = rtrim($this->parser->getResourceUrlFromRequest($request), '/');
        foreach ($ids as $id) {
            $links[] = $baseUrl . '/customer/' . $id->value();
        }
        foreach ($guestIds as $id) {
            $links[] = $baseUrl . '/customer/' . $id->value();
        }
        
        $metaData = $this->createApiMetaData($links);
        
        return $response->withJson([
                                       'data'  => array_merge($ids->toArray(), $guestIds->toArray()),
                                       '_meta' => $metaData,
                                   ],
                                   201);
    }
}