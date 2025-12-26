<?php
/*--------------------------------------------------------------
   CreateCustomerAction.php 2022-05-20
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
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response;

/**
 * Class CreateCustomerAction
 *
 * @package Gambio\Admin\Modules\Customer\App\Actions\Json
 * @codeCoverageIgnore
 */
class CreateCustomerAction
{
    private CustomerWriteService $service;
    private CustomerFactory      $factory;
    
    
    /**
     * @param CustomerWriteService $service
     * @param CustomerFactory      $factory
     */
    public function __construct(CustomerWriteService $service, CustomerFactory $factory)
    {
        $this->service = $service;
        $this->factory = $factory;
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
            $body           = $request->getParsedBody();
            $creationParams = $this->parseRequestBody($body);
            
            if ($body['isGuestAccount']) {
                $this->service->createGuestAccount(...$creationParams);
            } else {
                $this->service->createCustomer(...$creationParams);
            }
        } catch (Exception $exception) {
            return $response->withStatus(422)->withJson(['error' => $exception->getMessage()]);
        }
        
        return $response->withStatus(201);
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
        $customerGroup       = $body['customerGroup'];
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
            $personalInformation,
            $businessInformation,
            $contactInformation,
            $credit,
            $isFavorite,
            $customerGroup,
        ];
    }
}