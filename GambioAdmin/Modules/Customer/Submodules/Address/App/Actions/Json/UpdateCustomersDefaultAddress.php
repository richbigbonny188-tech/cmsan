<?php
/* --------------------------------------------------------------
   UpdateCustomersDefaultAddress.php 2022-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App\Actions\Json;

use Exception;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressReadService;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateCustomersDefaultAddress
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App\Actions\Json
 * @codeCoverageIgnore
 */
class UpdateCustomersDefaultAddress
{
    private static array $addressAttributeMethodMap = [
        'paymentAddress'  => 'getDefaultPaymentAddress',
        'shippingAddress' => 'getDefaultShippingAddress',
    ];
    
    private CustomerDefaultAddressReadService  $readService;
    private CustomerDefaultAddressWriteService $writeService;
    private CustomerAddressFactory             $factory;
    
    
    /**
     * @param CustomerDefaultAddressReadService  $readService
     * @param CustomerDefaultAddressWriteService $writeService
     * @param CustomerAddressFactory             $factory
     */
    public function __construct(
        CustomerDefaultAddressReadService  $readService,
        CustomerDefaultAddressWriteService $writeService,
        CustomerAddressFactory             $factory
    ) {
        $this->readService  = $readService;
        $this->writeService = $writeService;
        $this->factory      = $factory;
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
            $body       = $request->getParsedBody();
            $customerId = (int)$body['customerId'];
            
            if ($customerId <= 0) {
                return $response->withStatus(400)->withJson([
                                                                'error' => 'Invalid customer ID given. Got: '
                                                                           . $body['customerId'],
                                                            ]);
            }
            
            $addresses = [];
            
            foreach (static::$addressAttributeMethodMap as $attribute => $method) {
                
                if (isset($body[$attribute]) === false) {
                    
                    continue;
                }
                
                [$personalInformation, $locationInformation] = $this->parseRequestBody($body[$attribute]);
    
                $addresses[] = $address = $this->readService->$method($customerId);
                $address->changePersonalInformation($personalInformation);
                $address->changeLocationInformation($locationInformation);
            }
            
            $this->writeService->storeCustomerAddresses(...$addresses);
            
            return $response->withStatus(201);
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['error' => $exception->getMessage()]);
        }
    }
    
    
    /**
     * @param array $body
     *
     * @return array
     */
    private function parseRequestBody(array $body): array
    {
        $personalInformation = $this->factory->createPersonalInformation($body['gender'],
                                                                         $body['firstName'],
                                                                         $body['lastName'],
                                                                         $body['companyName']);
        
        [$countryName, $countryIsoCode2] = array_values($body['country']);
        [$stateId, $stateName] = array_values($body['state']);
        $locationInformation = $this->factory->createLocationInformation($body['streetName'],
                                                                         $body['houseNumber'],
                                                                         $body['postcode'],
                                                                         $body['city'],
                                                                         $countryName,
                                                                         $countryIsoCode2,
                                                                         $body['additionalInformation'],
                                                                         $body['suburb'],
                                                                         $stateId,
                                                                         $stateName);
        
        return [$personalInformation, $locationInformation];
    }
}