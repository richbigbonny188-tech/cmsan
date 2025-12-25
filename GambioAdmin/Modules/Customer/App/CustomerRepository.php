<?php
/*--------------------------------------------------------------
   CustomerRepository.php 2022-10-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\App;

use Exception;
use Gambio\Admin\Modules\Customer\App\Data\CustomerMapper;
use Gambio\Admin\Modules\Customer\App\Data\CustomerReader;
use Gambio\Admin\Modules\Customer\App\Data\CustomerWriter;
use Gambio\Admin\Modules\Customer\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Model\Events\CustomerCreated;
use Gambio\Admin\Modules\Customer\Model\Events\CustomerDeleted;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerFilters;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSearch;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSorting;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\BusinessInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\ContactInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerCredit;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Services\CustomerRepository as CustomerRepositoryInterface;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Core\Event\Abstracts\AbstractEventDispatchingRepository;
use Gambio\Core\Filter\Pagination;
use Psr\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * Class CustomerRepository
 *
 * @package Modules\Customer\App
 */
class CustomerRepository extends AbstractEventDispatchingRepository implements CustomerRepositoryInterface
{
    private CustomerMapper $mapper;
    private CustomerReader $reader;
    private CustomerWriter $writer;
    
    
    /**
     * @param CustomerMapper           $mapper
     * @param CustomerReader           $reader
     * @param CustomerWriter           $writer
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        CustomerMapper           $mapper,
        CustomerReader           $reader,
        CustomerWriter           $writer,
        EventDispatcherInterface $dispatcher
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
        $this->writer = $writer;
        $this->setEventDispatcher($dispatcher);
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterCustomers(
        CustomerFilters $filters,
        CustomerSorting $sorting,
        Pagination      $pagination
    ): Customers {
        return $this->mapper->mapCustomers(...$this->reader->getFilteredCustomers($filters, $sorting, $pagination));
    }
    
    
    /**
     * @inheritDoc
     */
    public function searchCustomers(CustomerSearch  $searchTerm,
                                    CustomerSorting $sorting,
                                    Pagination      $pagination
    ): Customers {
        
        return $this->mapper->mapCustomers(...$this->reader->searchCustomers($searchTerm, $sorting, $pagination));
    }
    
    /**
     * @inheritDoc
     */
    public function getSearchedCustomerTotalCount(CustomerSearch $searchTerm): int
    {
        return $this->reader->getSearchedCustomerTotalCount($searchTerm);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomersTotalCount(CustomerFilters $filters): int
    {
        return $this->reader->getCustomerTotalCount($filters);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomers(): Customers
    {
        return $this->mapper->mapCustomers(...$this->reader->getCustomers(false));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getGuestAccounts(): Customers
    {
        return $this->mapper->mapCustomers(...$this->reader->getCustomers(true));
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getCustomerById(CustomerId $id): Customer
    {
        return $this->mapper->mapCustomer($this->reader->getCustomerById($id));
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomer(
        CustomerGroup       $customerGroup,
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        CustomerCredit      $credit,
        bool                $isFavorite = false
    ): CustomerId {
        
        $customerId = $this->writer->createCustomer($customerGroup,
                                                    $personalInformation,
                                                    $businessInformation,
                                                    $contactInformation,
                                                    $credit,
                                                    $isFavorite,
                                                    false);
        $customerId = $this->mapper->createCustomerId($customerId);
        
        $this->dispatchEvent(CustomerCreated::create($customerId));
        
        return $customerId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleCustomers(array ...$creationArguments): CustomerIds
    {
        foreach ($creationArguments as $index => $args) {
            $errorMessage = '%s creation argument must implement %s; Index: ' . $index;
            $argInterface = [
                CustomerGroup::class,
                PersonalInformation::class,
                BusinessInformation::class,
                ContactInformation::class,
                CustomerCredit::class,
            ];
            
            Assert::isInstanceOf($args[0], $argInterface[0], sprintf($errorMessage, 'First', $argInterface[0]));
            Assert::isInstanceOf($args[1], $argInterface[1], sprintf($errorMessage, 'Second', $argInterface[1]));
            Assert::isInstanceOf($args[2], $argInterface[2], sprintf($errorMessage, 'Third', $argInterface[2]));
            Assert::isInstanceOf($args[3], $argInterface[3], sprintf($errorMessage, 'Fourth', $argInterface[3]));
            Assert::isInstanceOf($args[4], $argInterface[4], sprintf($errorMessage, 'Fifth', $argInterface[4]));
            Assert::boolean($args[5], 'Sixth creation argument must be a boolean.');
            Assert::boolean($args[6], 'Seventh creation argument must be a boolean.');
        }
        
        $customerIds = $this->writer->createMultipleCustomers(...$creationArguments);
        $customerIds = array_map([$this->mapper, 'createCustomerId'], $customerIds);
        
        array_map([$this, 'dispatchEvent'], array_map([CustomerCreated::class, 'create'], $customerIds));
        
        return $this->mapper->createCustomerIds(...$customerIds);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createGuestAccount(
        CustomerGroup       $customerGroup,
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        CustomerCredit      $credit,
        bool                $isFavorite = false
    ): CustomerId {
        
        $customerId = $this->writer->createCustomer($customerGroup,
                                                    $personalInformation,
                                                    $businessInformation,
                                                    $contactInformation,
                                                    $credit,
                                                    $isFavorite,
                                                    true);
        $customerId = $this->mapper->createCustomerId($customerId);
        
        $this->dispatchEvent(CustomerCreated::create($customerId));
        
        return $customerId;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createMultipleGuestAccounts(array ...$creationArguments): CustomerIds
    {
        $customerIds = $this->writer->createMultipleCustomers(...$creationArguments);
        $customerIds = array_map([$this->mapper, 'createCustomerId'], $customerIds);
        
        array_map([$this, 'dispatchEvent'], array_map([CustomerCreated::class, 'create'], $customerIds));
        
        return $this->mapper->createCustomerIds(...$customerIds);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomers(Customer ...$customers): void
    {
        $this->writer->storeCustomers(...$customers);
        array_map([$this, 'dispatchEntityEvents'], $customers);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomers(CustomerId ...$ids): void
    {
        $this->writer->deleteCustomers(...$ids);
        array_map([$this, 'dispatchEvent'], array_map([CustomerDeleted::class, 'create'], $ids));
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteOutdatedGuestAccounts(): void
    {
        $ids = $this->writer->deleteOutdatedGuestAccounts();
        $ids = array_map([$this->mapper, 'createCustomerId'], $ids);
        array_map([$this, 'dispatchEvent'], array_map([CustomerDeleted::class, 'create'], $ids));
    }
    
    
    /**
     * @inheritDoc
     */
    public function validateEmailAddress(string $email): bool
    {
        if ($this->reader->emailAddressIsAlreadyTaken($email)) {
            
            throw CustomerEmailAddressMustBeUniqueException::fromEmailAddress($email);
        }
        
        // can throw an EmailAddressIsInvalidException
        $this->mapper->createContactInformation($email);
        
        return true;
    }
}