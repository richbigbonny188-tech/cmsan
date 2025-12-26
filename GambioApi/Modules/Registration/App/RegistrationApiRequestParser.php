<?php
/*--------------------------------------------------------------
   RegistrationApiRequestParser.php 2022-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Api\Modules\Registration\App;

use DateTimeImmutable;
use Exception;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RegistrationApiRequestParser
 *
 * @package Gambio\Api\Modules\Customer\App
 */
class RegistrationApiRequestParser
{
    public const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';
    private CustomerFactory        $customerFactory;
    private CustomerAddressFactory $customerAddressFactory;
    
    
    /**
     * @param CustomerFactory        $customerFactory
     * @param CustomerAddressFactory $customerAddressFactory
     */
    public function __construct(
        CustomerFactory        $customerFactory,
        CustomerAddressFactory $customerAddressFactory
    ) {
        $this->customerFactory        = $customerFactory;
        $this->customerAddressFactory = $customerAddressFactory;
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getLimit(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('limit', 100);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getOffset(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('offset', 0);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getPage(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('page', 1);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return int
     */
    public function getPerPage(ServerRequestInterface $request): int
    {
        return (int)$request->getQueryParam('per-page', 25);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFields(ServerRequestInterface $request): array
    {
        $fields = $request->getQueryParam('fields');
        
        return ($fields === null) ? [] : explode(',', $fields);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    public function getFilters(ServerRequestInterface $request): array
    {
        return $request->getQueryParam('filter', []);
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function getSorting(ServerRequestInterface $request): ?string
    {
        return $request->getQueryParam('sort');
    }
    
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getResourceUrlFromRequest(ServerRequestInterface $request): string
    {
        return $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getPath();
    }
    
    
    /**
     * @param array $request
     * @param                        $errors
     *
     * @return array
     */
    public function parseCustomerDataForRegistration(array $requestBody, &$errors = []): array
    {
        $creationArguments = ['guests' => [], 'normal' => []];
        
        foreach ($requestBody as $index => $documentData) {
            try {
                $personalInformation = $this->customerFactory->createPersonalInformation($documentData['personalInformation']['gender'],
                                                                                         $documentData['personalInformation']['firstName'],
                                                                                         $documentData['personalInformation']['lastName'],
                                                                                         $documentData['personalInformation']['customerNumber'],
                                                                                         new DateTimeImmutable($documentData['personalInformation']['dateOfBirth']));
                
                $businessInformation = $this->customerFactory->createBusinessInformation($company = $documentData['businessInformation']['companyName'],
                                                                                         $documentData['businessInformation']['vatId'],
                                                                                         $documentData['businessInformation']['isTradesperson']);
                
                $contactInformation = $this->customerFactory->createContactInformation($documentData['contactInformation']['email'],
                                                                                       $documentData['contactInformation']['phoneNumber'],
                                                                                       $documentData['contactInformation']['faxNumber']);
                $isGuestAccount     = isset($documentData['password']) === false
                                       || $documentData['password'] === null || (int)$documentData['customerGroup'] === 1;
                
                $customerInformation = [
                    $personalInformation,
                    $businessInformation,
                    $contactInformation,
                    $documentData['credit'],
                    $documentData['isFavorite'],
                    $documentData['customerGroup'],
                    $isGuestAccount,
                ];
                
                $addressPersonalInformation = $this->customerAddressFactory->createPersonalInformation($documentData['personalInformation']['gender'],
                                                                                                       $documentData['personalInformation']['firstName'],
                                                                                                       $documentData['personalInformation']['lastName'],
                                                                                                       $company);
                $addressLocationInformation = $this->customerAddressFactory->createLocationInformation($documentData['locationInformation']['streetName'],
                                                                                                       $documentData['locationInformation']['houseNumber'],
                                                                                                       $documentData['locationInformation']['postcode'],
                                                                                                       $documentData['locationInformation']['city'],
                                                                                                       $documentData['locationInformation']['country']['name'],
                                                                                                       $documentData['locationInformation']['country']['isoCode2'],
                                                                                                       $documentData['locationInformation']['additionalInformation'],
                                                                                                       $documentData['locationInformation']['suburb'],
                                                                                                       $documentData['locationInformation']['stateId'],
                                                                                                       $documentData['locationInformation']['state']);
                $addressInformation         = [$addressPersonalInformation, $addressLocationInformation];
                
                $creationArguments[$isGuestAccount ? 'guests' : 'normal'][] = [
                    'customer' => $customerInformation,
                    'address'  => $addressInformation,
                    'password' => $documentData['password'] ?? null,
                ];
            } catch (Exception $exception) {
                $errors[$index][] = $exception->getMessage();
            }
        }
        
        return $creationArguments;
    }
}