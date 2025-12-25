<?php
/* --------------------------------------------------------------
   CustomerReadService.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerReadServiceInterface');

/**
 * Class CustomerReadService
 *
 * This class provides methods for reading customer data.
 *
 * @category   System
 * @package    Customer
 * @implements CustomerReadServiceInterface
 */
class CustomerReadService implements CustomerReadServiceInterface
{
    /**
     * Customer repository.
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    
    
    /**
     * Constructor of the class CustomerService.
     *
     * @param CustomerRepositoryInterface $customerRepository Customer repository.
     */
    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
    
    
    /**
     * Finds a customer by an entered ID.
     *
     * @param IdType $customerId Customer ID.
     *
     * @return Customer Customer.
     */
    public function getCustomerById(IdType $customerId)
    {
        return $this->customerRepository->getCustomerById($customerId);
    }
    
    
    /**
     * Finds a customer by its email address. Returns null if no customer is found.
     *
     * @param CustomerEmailInterface $emailAddress
     *
     * @return Customer|null
     */
    public function findCustomerByEmail(CustomerEmailInterface $emailAddress)
    {
        $customer = $this->customerRepository->getRegistreeByEmail($emailAddress);
        
        if ($customer === null) {
            $customer = $this->customerRepository->getGuestByEmail($emailAddress);
        }
        
        return $customer;
    }
    
    
    /**
     * Checks if the email address of the registree already exists.
     *
     * @param CustomerEmailInterface $email Customer's E-Mail address.
     *
     * @return bool Does the E-Mail address already exist?
     */
    public function registreeEmailExists(CustomerEmailInterface $email)
    {
        $customer = $this->customerRepository->getRegistreeByEmail($email);
        if ($customer === null) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     * Checks if address is the default address of the customer.
     *
     * @param CustomerAddressInterface $customerAddress Customer's address.
     *
     * @return bool Is provided address the customer's default address?
     */
    public function addressIsDefaultCustomerAddress(CustomerAddressInterface $customerAddress)
    {
        $customer = $this->getCustomerById(new IdType((string)$customerAddress->getCustomerId()));
        
        return $customer->getDefaultAddress()->getId() == new IdType((string)$customerAddress->getCustomerId());
    }
    
    
    /**
     * Filters customer records and returns an array with results.
     *
     * Example:
     *        $service->filterCustomers(['customers_id' => 1]);
     *
     * @param array       $conditions Associative array containing the desired field and value.
     * @param \Pager|null $pager      (Optional) Pager object with pagination information
     * @param array       $sorters    (Optional) array of Sorter objects with data sorting information
     *
     * @return array Returns an array that contains customer objects.
     */
    public function filterCustomers(array $conditions = [], \Pager $pager = null, array $sorters = [])
    {
        return $this->customerRepository->filterCustomers($conditions, $pager, $sorters);
    }
    
    
    /**
     * Filters customer records and returns the total count.
     *
     * Example:
     *        $service->filterCustomers(['customers_id' => 1]);
     *
     * @param array $conditions Associative array containing the desired field and value.
     *
     * @return int
     */
    public function getFilterCustomersCount(array $conditions = [])
    {
        return $this->customerRepository->getFilterCustomersCount($conditions);
    }
    
    
    /**
     * Filters customer records by a given CustomerSearchCondition object and returns an array with results.
     *
     * @param \CustomerSearchCondition $condition Conditions object for search.
     * @param \Pager|null              $pager     (Optional) Pager object with pagination information
     * @param array                    $sorters   (Optional) array of Sorter objects with data sorting information
     *
     * @return array Returns an array that contains customer objects.
     */
    public function searchCustomers(CustomerSearchCondition $condition, \Pager $pager = null, array $sorters = [])
    {
        return $this->customerRepository->searchCustomers($condition, $pager, $sorters);
    }
}
