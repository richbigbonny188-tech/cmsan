<?php
/*--------------------------------------------------------------
   UpdateCustomerAction.php 2022-05-20
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
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerReadService;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class UpdateCustomerAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class UpdateCustomerAction
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
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        try {
            $body = $request->getParsedBody();
            [
                $customerId,
                $personalInformation,
                $businessInformation,
                $contactInformation,
                $credit,
                $isFavorite,
                $customerGroup,
            ] = $this->parseRequestBody($body);
            
            if ($customerId <= 0) {
                return $response->withStatus(400)->withJson([
                                                                'errors' => 'Invalid customer ID given. Got: '
                                                                            . $body['customerId'],
                                                            ]);
            }
            
            $customer = $this->readService->getCustomerById($customerId);
            $customer->changeCustomerGroup($this->factory->createCustomerGroup($customerGroup));
            $customer->changePersonalInformation($personalInformation);
            $customer->changeBusinessInformation($businessInformation);
            $customer->changeContactInformation($contactInformation);
            $customer->changeCredit($this->factory->createCredit($credit));
            $customer->changeIsFavoriteState($isFavorite);
            
            $this->writeService->storeCustomers($customer);
        } catch (CustomerEmailAddressMustBeUniqueException $exception) {
            return $response->withStatus(422)->withJson(['error' => $exception->getMessage()]);
        } catch (Exception $exception) {
            return $response->withStatus(409)->withJson(['error' => $exception->getMessage()]);
        }
        
        return $response->withStatus(204);
    }
    
    
    /**
     * @param array $body
     *
     * @return array
     *
     * @throws Exception
     */
    private function parseRequestBody(array $body): array
    {
        $customerId          = (int)$body['customerId'];
        $customerGroup       = $this->factory->createCustomerGroup($body['customerGroup']);
        $isFavorite          = $body['isFavorite'];
        $personalInformation = $this->factory->createPersonalInformation($body['personalInformation']['gender'],
                                                                         $body['personalInformation']['firstName'],
                                                                         $body['personalInformation']['lastName'],
                                                                         $body['personalInformation']['customerNumber'],
                                                                         new DateTimeImmutable($body['personalInformation']['dateOfBirth']));
        $contactInformation  = $this->factory->createContactInformation($body['contactInformation']['email'],
                                                                        $body['contactInformation']['phoneNumber'],
                                                                        $body['contactInformation']['faxNumber']);
        $businessInformation = $this->factory->createBusinessInformation($body['businessInformation']['companyName'],
                                                                         $body['businessInformation']['vatId'],
                                                                         $body['businessInformation']['isTradesperson'],
                                                                         $body['businessInformation']['isValidVatId']);
        $credit              = $body['credit'];
        
        return [
            $customerId,
            $personalInformation,
            $businessInformation,
            $contactInformation,
            $credit,
            $isFavorite,
            $customerGroup,
        ];
    }
}