<?php
/*--------------------------------------------------------------
   UpdateCustomerFavoriteStateAction.php 2022-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Customer\App\Actions;

use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\StorageOfCustomerFailedException;
use Gambio\Api\Modules\Customer\App\CustomerApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateCustomerFavoriteStateAction
 *
 * @package Gambio\Api\Modules\Customer\App\Actions
 */
class UpdateCustomerFavoriteStateAction
{
    private CustomerReadService         $readService;
    private CustomerWriteService        $writeService;
    private CustomerApiRequestValidator $validator;
    
    
    /**
     * @param CustomerReadService         $readService
     * @param CustomerWriteService        $writeService
     * @param CustomerApiRequestValidator $validator
     */
    public function __construct(
        CustomerReadService         $readService,
        CustomerWriteService        $writeService,
        CustomerApiRequestValidator $validator
    ) {
        $this->readService  = $readService;
        $this->writeService = $writeService;
        $this->validator    = $validator;
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
        $errors = $this->validator->validatePatchRequest($parsedBody = $request->getParsedBody());
        
        if (empty($errors) === false) {
            
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            $customers = [];
            
            foreach ($parsedBody as ['id' => $id, 'isFavorite' => $favorite]) {
                
                $customers[] = $customer = $this->readService->getCustomerById((int)$id);
                $customer->changeIsFavoriteState($favorite);
            }
            
            $this->writeService->storeCustomers(...$customers);
            
            return $response->withStatus(204);
        } catch (CustomerDoesNotExistException|CustomerEmailAddressMustBeUniqueException|StorageOfCustomerFailedException $exception) {
            
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}