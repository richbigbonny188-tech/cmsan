<?php
/*--------------------------------------------------------------
   UpdateCustomerAction.php 2022-03-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Customer\App\Actions;

use DateTimeImmutable;
use Exception;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Api\Modules\Customer\App\CustomerApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateCustomerAction
 *
 * @package Gambio\Api\Modules\Customer\App\Actions
 */
class UpdateCustomerAction
{
    private CustomerReadService         $readService;
    private CustomerWriteService        $writeService;
    private CustomerApiRequestValidator $validator;
    private CustomerFactory             $factory;
    
    
    /**
     * @param CustomerReadService         $readService
     * @param CustomerWriteService        $writeService
     * @param CustomerApiRequestValidator $validator
     * @param CustomerFactory             $factory
     */
    public function __construct(
        CustomerReadService         $readService,
        CustomerWriteService        $writeService,
        CustomerApiRequestValidator $validator,
        CustomerFactory             $factory
    ) {
        $this->readService  = $readService;
        $this->writeService = $writeService;
        $this->validator    = $validator;
        $this->factory      = $factory;
    }
    
    
    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $errors = $this->validator->validateUpdateRequest($parsedBody = $request->getParsedBody());
        
        if (empty($errors) === false) {
            
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        try {
            $customers = [];
            
            foreach ($parsedBody as ['id' => $id, 'customerGroup' => $customerGroup, 'isFavorite' => $isFavorite, 'contactInformation' => $contactInformation, 'personalInformation' => $personalInformation, 'businessInformation' => $businessInformation, 'credit' => $credit,]) {
                
                $customers[] = $customer = $this->readService->getCustomerById((int)$id);
                
                $customer->changeCustomerGroup($this->factory->createCustomerGroup((int)$customerGroup));
                $customer->changeCredit($this->factory->createCredit($credit));
                $customer->changeContactInformation($this->factory->createContactInformation($contactInformation['email'],
                                                                                             $contactInformation['phoneNumber'],
                                                                                             $contactInformation['faxNumber']));
                $customer->changeBusinessInformation($this->factory->createBusinessInformation($businessInformation['companyName'],
                                                                                               $businessInformation['vatId'],
                                                                                               $businessInformation['isTradesperson']));
                $customer->changePersonalInformation($this->factory->createPersonalInformation($personalInformation['gender'],
                                                                                               $personalInformation['firstName'],
                                                                                               $personalInformation['lastName'],
                                                                                               $personalInformation['customerNumber'],
                                                                                               new DateTimeImmutable($personalInformation['dateOfBirth'])));
                $customer->changeIsFavoriteState($isFavorite);
            }
            
            $this->writeService->storeCustomers(...$customers);
            
            return $response->withStatus(204);
        } catch (CustomerDoesNotExistException $exception) {
            
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        } catch (CustomerEmailAddressMustBeUniqueException $exception) {
            
            return $response->withStatus(409)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
    }
}