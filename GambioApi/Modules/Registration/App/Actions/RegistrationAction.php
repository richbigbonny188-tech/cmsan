<?php
/*--------------------------------------------------------------
   RegistrationAction.php 2022-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Registration\App\Actions;

use Exception;
use Gambio\Admin\Modules\Customer\Services\CustomerPasswordWriteService as CustomerPasswordWriteServiceInterface;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfCustomerFailedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfMainAdminNotPermittedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressRepository;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressWriteService;
use Gambio\Api\Application\Responses\CreateApiMetaDataTrait;
use Gambio\Api\Modules\Registration\App\RegistrationApiRequestParser;
use Gambio\Api\Modules\Registration\App\RegistrationApiRequestValidator;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class RegistrationAction
 *
 * @package Gambio\Api\Modules\Registration\App\Actions
 */
class RegistrationAction
{
    use CreateApiMetaDataTrait;
    
    private CustomerWriteService                  $writeService;
    private RegistrationApiRequestValidator       $validator;
    private RegistrationApiRequestParser          $parser;
    private CustomerDefaultAddressWriteService    $addressWriteService;
    private CustomerPasswordWriteServiceInterface $passwordWriteService;
    private CustomerAddressRepository             $customerAddressRepository;
    
    
    /**
     * @param CustomerWriteService                  $writeService
     * @param CustomerDefaultAddressWriteService    $addressWriteService
     * @param CustomerPasswordWriteServiceInterface $passwordWriteService
     * @param RegistrationApiRequestValidator       $validator
     * @param RegistrationApiRequestParser          $parser
     */
    public function __construct(
        CustomerWriteService                  $writeService,
        CustomerDefaultAddressWriteService    $addressWriteService,
        CustomerPasswordWriteServiceInterface $passwordWriteService,
        RegistrationApiRequestValidator       $validator,
        RegistrationApiRequestParser          $parser,
        CustomerAddressRepository             $customerAddressRepository
    ) {
        $this->writeService              = $writeService;
        $this->addressWriteService       = $addressWriteService;
        $this->passwordWriteService      = $passwordWriteService;
        $this->validator                 = $validator;
        $this->parser                    = $parser;
        $this->customerAddressRepository = $customerAddressRepository;
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
        $requestBody = $request->getParsedBody();
        $errors = $this->validator->validateRegistrationRequest($requestBody);
        
        if (empty($errors) === false) {
            return $response->withStatus(400)->withJson(['errors' => $errors]);
        }
        
        $parsedBody = [];
        // Gets the zone_entry_id for the address_book table for each customer, as we do not provide an endpoint
        // which returns the zones with their respective IDs.
        foreach ($requestBody as $body) {
            $body['locationInformation']['stateId'] = $this->customerAddressRepository->getStateId(
                $body['locationInformation']['country']['isoCode2'] ?? '',
                $body['locationInformation']['state'] ?? ''
            );
    
            $parsedBody[] = $body;
        }
        
        $creationArguments = $this->parser->parseCustomerDataForRegistration($parsedBody, $errors);
        
        if (count($errors) > 0) {
            
            return $response->withStatus(422)->withJson(['errors' => $errors]);
        }
        
        try {
            $normalAccountArguments = $creationArguments['normal'];
            $guestAccountArguments  = $creationArguments['guests'];
            
            $ids      = $this->writeService->createMultipleCustomers(...
                array_column($normalAccountArguments, 'customer'));
            $guestIds = $this->writeService->createMultipleGuestAccounts(...
                array_column($guestAccountArguments, 'customer'));
            
            $normalAccountIds = $ids->toArray();
            
            if (count($normalAccountIds)) {
                
                $normalAccountArguments = array_map(fn(int $i) => array_merge($normalAccountArguments[$i],
                                                                              ['id' => $normalAccountIds[$i]]),
                    range(0, count($normalAccountIds) - 1));
                
                foreach ($normalAccountArguments as ['id' => $id, 'address' => $address, 'password' => $password]) {
                    
                    $this->passwordWriteService->setCustomerPassword((int)$id, $password);
                    $this->addressWriteService->createCustomerAddress(...array_merge([$id], $address));
                }
            }
            
            $guestAccountIds = $guestIds->toArray();
            
            if (count($guestAccountIds)) {
                
                $guestAccountArguments = array_map(fn(int $i) => array_merge($guestAccountArguments[$i],
                                                                             ['id' => $guestAccountIds[$i]]),
                    range(0, count($guestAccountIds) - 1));
                
                foreach ($guestAccountArguments as ['id' => $id, 'address' => $address]) {
                    
                    $this->addressWriteService->createCustomerAddress(...array_merge([$id], $address));
                }
            }
        } catch (Exception $exception) {
            
            if (isset($ids) && count($ids->toArray()) >= 1) {
                
                try {
                    $this->writeService->deleteCustomers(...$ids->toArray());
                } catch (DeletionOfCustomerFailedException|DeletionOfMainAdminNotPermittedException $e) {
                    unset($e);
                }
            }
            
            if (isset($guestIds) && count($guestIds->toArray()) >= 1) {
                
                try {
                    $this->writeService->deleteCustomers(...$guestIds->toArray());
                } catch (DeletionOfCustomerFailedException|DeletionOfMainAdminNotPermittedException $e) {
                    unset($e);
                }
            }
            
            return $response->withStatus(422)->withJson(['errors' => [[$exception->getMessage()]]]);
        }
        
        $links   = [];
        $baseUrl = $this->parser->getResourceUrlFromRequest($request);
        $baseUrl = preg_replace('/\/customers\/register\/?$/i', '', $baseUrl);
        foreach ($ids as $id) {
            $links[] = $baseUrl . '/customers/' . $id->value();
        }
        foreach ($guestIds as $id) {
            $links[] = $baseUrl . '/customers/' . $id->value();
        }
        
        $metaData = $this->createApiMetaData($links);
        
        return $response->withJson([
                                       'data'  => array_merge($ids->toArray(), $guestIds->toArray()),
                                       '_meta' => $metaData,
                                   ],
                                   201);
    }
}