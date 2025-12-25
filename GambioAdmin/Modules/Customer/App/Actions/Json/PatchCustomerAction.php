<?php
/*--------------------------------------------------------------
   PatchCustomerAction.php 2022-07-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App\Actions\Json;

use DateTimeImmutable;
use Exception;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class PatchCustomerAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class PatchCustomerAction
{
    private CustomerReadService  $readService;
    private CustomerWriteService $writeService;
    private CustomerFactory      $factory;
    
    
    /**
     * @param CustomerReadService  $readService
     * @param CustomerWriteService $writeService
     * @param CustomerFactory      $factory
     */
    public function __construct(
        CustomerReadService  $readService,
        CustomerWriteService $writeService,
        CustomerFactory      $factory
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
                                                                'errors' => 'Invalid customer ID given. Got: '
                                                                            . $body['customerId'],
                                                            ]);
            }
            
            $customer = $this->readService->getCustomerById($customerId);
            $this->patchCustomerGroup($body, $customer);
            $this->patchIsFavoriteState($body, $customer);
            $this->patchPersonalInformation($body, $customer);
            $this->patchContactInformation($body, $customer);
            $this->patchBusinessInformation($body, $customer);
            $this->patchCredit($body, $customer);
            
            $this->writeService->storeCustomers($customer);
        } catch (CustomerEmailAddressMustBeUniqueException $exception) {
            return $response->withStatus(422)->withJson(['error' => $exception->getMessage()]);
        } catch (Exception $exception) {
            return $response->withStatus(409)->withJson(['error' => $exception->getMessage()]);
        }
        
        return $response->withStatus(204);
    }
    
    
    /**
     * @param array    $body
     * @param Customer $customer
     *
     * @return void
     */
    private function patchCustomerGroup(array $body, Customer $customer): void
    {
        if (isset($body['customerGroup']) === false) {
            return;
        }
        
        $customerGroup = $this->factory->createCustomerGroup($body['customerGroup']);
        $customer->changeCustomerGroup($customerGroup);
    }
    
    
    /**
     * @param array    $body
     * @param Customer $customer
     *
     * @return void
     */
    private function patchIsFavoriteState(array $body, Customer $customer): void
    {
        if (isset($body['isFavorite']) === false) {
            return;
        }
        
        $isFavorite = $body['isFavorite'];
        $customer->changeIsFavoriteState($isFavorite);
    }
    
    
    /**
     * @param array    $body
     * @param Customer $customer
     *
     * @return void
     *
     * @throws Exception
     */
    private function patchPersonalInformation(array $body, Customer $customer): void
    {
        if (isset($body['personalInformation']) === false) {
            return;
        }
        
        $personalInformation = $this->factory->createPersonalInformation($body['personalInformation']['gender'],
                                                                         $body['personalInformation']['firstName'],
                                                                         $body['personalInformation']['lastName'],
                                                                         $body['personalInformation']['customerNumber'],
                                                                         new DateTimeImmutable($body['personalInformation']['dateOfBirth']));
        $customer->changePersonalInformation($personalInformation);
    }
    
    
    /**
     * @param array    $body
     * @param Customer $customer
     *
     * @return void
     *
     * @throws Exception
     */
    private function patchContactInformation(array $body, Customer $customer): void
    {
        if (isset($body['contactInformation']) === false) {
            return;
        }
        
        $contactInformation = $this->factory->createContactInformation($body['contactInformation']['email'],
                                                                       $body['contactInformation']['phoneNumber'],
                                                                       $body['contactInformation']['faxNumber']);
        $customer->changeContactInformation($contactInformation);
    }
    
    
    /**
     * @param array    $body
     * @param Customer $customer
     *
     * @return void
     *
     * @throws Exception
     */
    private function patchBusinessInformation(array $body, Customer $customer): void
    {
        if (isset($body['businessInformation']) === false) {
            return;
        }
        
        $businessInformation = $this->factory->createBusinessInformation($body['businessInformation']['companyName'],
                                                                         $body['businessInformation']['vatId'],
                                                                         $body['businessInformation']['isTradesperson'],
                                                                         $body['businessInformation']['isValidVatId']);
        $customer->changeBusinessInformation($businessInformation);
    }
    
    
    /**
     * @param array    $body
     * @param Customer $customer
     *
     * @return void
     *
     * @throws Exception
     */
    private function patchCredit(array $body, Customer $customer): void
    {
        if (isset($body['credit']) === false) {
            return;
        }
        
        $credit = (float)$body['credit'];
        $credit = $this->factory->createCredit($credit);
        $customer->changeCredit($credit);
    }
}