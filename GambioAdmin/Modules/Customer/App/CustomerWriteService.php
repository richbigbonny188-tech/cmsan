<?php
/*--------------------------------------------------------------
   CustomerWriteService.php 2022-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Gambio\Admin\Modules\Customer\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\BusinessInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\ContactInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Services\CustomerFactory;
use Gambio\Admin\Modules\Customer\Services\CustomerRepository;
use Gambio\Admin\Modules\Customer\Services\CustomerWriteService as CustomerWriteServiceInterface;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class CustomerWriteService
 *
 * @package Gambio\Admin\Modules\Customer\App
 */
class CustomerWriteService implements CustomerWriteServiceInterface
{
    private CustomerFactory    $factory;
    private CustomerRepository $repository;
    private int                $defaultCustomerGroup;
    
    
    /**
     * @param CustomerFactory      $factory
     * @param CustomerRepository   $repository
     * @param ConfigurationService $configurationService
     */
    public function __construct(
        CustomerFactory      $factory,
        CustomerRepository   $repository,
        ConfigurationService $configurationService
    ) {
        $this->factory              = $factory;
        $this->repository           = $repository;
        $this->defaultCustomerGroup = $this->defaultCustomerGroup($configurationService);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomer(
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        float               $credit,
        bool                $isFavorite = false,
        ?int                $customerGroup = null
    ): CustomerId {
        
        $customerGroup = $this->factory->createCustomerGroup($customerGroup ?? $this->defaultCustomerGroup);
        $credit        = $this->factory->createCredit($credit);
        
        return $this->repository->createCustomer($customerGroup,
                                                 $personalInformation,
                                                 $businessInformation,
                                                 $contactInformation,
                                                 $credit,
                                                 $isFavorite);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleCustomers(array ...$creationArguments): CustomerIds
    {
        $creationArguments = $this->parseCreationArguments(...$creationArguments);
        
        return $this->repository->createMultipleCustomers(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createGuestAccount(
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        float               $credit,
        bool                $isFavorite = false,
        ?int                $customerGroup = null
    ): CustomerId {
        
        $customerGroup = $this->factory->createCustomerGroup($customerGroup ?? $this->defaultCustomerGroup);
        $credit        = $this->factory->createCredit($credit);
        
        return $this->repository->createGuestAccount($customerGroup,
                                                     $personalInformation,
                                                     $businessInformation,
                                                     $contactInformation,
                                                     $credit,
                                                     $isFavorite);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleGuestAccounts(array ...$creationArguments): CustomerIds
    {
        $creationArguments = $this->parseCreationArguments(...$creationArguments);
        
        return $this->repository->createMultipleGuestAccounts(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomers(Customer ...$customers): void
    {
        $this->repository->storeCustomers(...$customers);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomers(int ...$ids): void
    {
        $this->repository->deleteCustomers(...array_map([$this->factory, 'createCustomerId'], $ids));
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteOutdatedGuestAccounts(): void
    {
        $this->repository->deleteOutdatedGuestAccounts();
    }
    
    
    /**
     * @param array ...$creationArguments
     *
     * @return array
     */
    private function parseCreationArguments(array ...$creationArguments): array
    {
        $result = [];
        
        foreach ($creationArguments as [$personalInformation, $businessInformation, $contactInformation, $credit, $isFavorite, $customerGroup, $isGuestAccount]) {
            $credit        = $this->factory->createCredit($credit);
            $customerGroup = $this->factory->createCustomerGroup($customerGroup ?? $this->defaultCustomerGroup);
            $result[]      = [
                $customerGroup,
                $personalInformation,
                $businessInformation,
                $contactInformation,
                $credit,
                $isFavorite,
                $isGuestAccount,
            ];
        }
        
        return $result;
    }
    
    
    /**
     * @param ConfigurationService $configurationService
     *
     * @return int
     */
    private function defaultCustomerGroup(ConfigurationService $configurationService): int
    {
        $value = $configurationService->find('configuration/DEFAULT_CUSTOMERS_STATUS_ID');
        
        return $value !== null ? (int)$value->value() : 2;
    }
}