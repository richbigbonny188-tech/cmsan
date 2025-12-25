<?php
/* --------------------------------------------------------------
   CustomerReader.inc.php 2022-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('CustomerReaderInterface');

/**
 * Class CustomerReader
 *
 * This class is used for reading customer data from the database
 *
 * @category   System
 * @package    Customer
 * @implements CustomerReaderInterface
 */
class CustomerReader extends AbstractDataPaginator implements CustomerReaderInterface
{
    
    
    /**
     * Customer factory.
     * @var AbstractCustomerFactory
     */
    protected $customerFactory;
    
    /**
     * Customer address repository.
     * @var CustomerAddressRepositoryInterface
     */
    protected $customerAddressRepository;
    
    /**
     * String helper.
     * @var StringHelperInterface
     */
    protected $stringHelper;
    
    /**
     * Is customer a guest?
     * @var bool
     */
    protected $isGuest;
    
    
    /**
     * Constructor of the class CustomerReader.
     *
     * CrossCuttingLoader dependencies:
     * - StringHelper
     *
     * @param AbstractCustomerFactory            $customerFactory           Customer factory.
     * @param CustomerAddressRepositoryInterface $customerAddressRepository Customer address repository.
     * @param CI_DB_query_builder                $dbQueryBuilder            Query builder.
     */
    public function __construct(
        AbstractCustomerFactory $customerFactory,
        CustomerAddressRepositoryInterface $customerAddressRepository,
        CI_DB_query_builder $dbQueryBuilder
    ) {
        parent::__construct($dbQueryBuilder);
        $this->customerFactory           = $customerFactory;
        $this->customerAddressRepository = $customerAddressRepository;
        $this->stringHelperService       = StaticCrossCuttingLoader::getObject('StringHelper');
    }
    
    
    /**
     * Applies the class default sorting
     */
    protected function _applyDefaultSorting()
    {
        $this->db->order_by('customers.customers_id', 'asc');
    }
    
    
    /**
     * return the child class Field Map array.
     *
     * @return array.
     */
    
    protected function _getFieldMap()
    {
        return [
            'id'              => 'customers.customers_id',
            "number"          => 'customers.customers_cid',
            "gender"          => 'customers.customers_gender',
            "firstname"       => 'customers.customers_firstname',
            "lastname"        => 'customers.customers_lastname',
            "dateofbirth"     => 'customers.customers_dob',
            "vatnumber"       => 'customers.customers_vat_id',
            "vatnumberstatus" => 'customers.customers_vat_id_status',
            "telephone"       => 'customers.customers_telephone',
            "fax"             => 'customers.customers_fax',
            "email"           => 'customers.customers_email_address',
            "statusid"        => 'customers.customers_status',
            "addressid"       => 'customers.customers_default_address_id'
        ];
    }
    
    
    /**
     * Finds a customer by the given ID.
     *
     * @param IdType $id Customer's ID.
     *
     * @return Customer|null Customer or null if not found.
     */
    public function findById(IdType $id)
    {
        $filterArray = ['customers_id' => $id->asInt()];
        
        return $this->_findByFilter($filterArray);
    }
    
    
    /**
     * Finds a registree by email address.
     *
     * @param CustomerEmailInterface $email Customer's E-Mail address.
     *
     * @return Customer|null Customer or null if not found.
     */
    public function findRegistreeByEmail(CustomerEmailInterface $email)
    {
        $isGuest     = false;
        $filterArray = [
            'customers_email_address' => (string)$email,
            'account_type'            => (string)(int)$isGuest
        ];
        
        return $this->_findByFilter($filterArray);
    }
    
    
    /**
     * Finds a guest by email address.
     *
     * @param CustomerEmailInterface $email Customer's E-Mail address.
     *
     * @return Customer|null Customer or null if not found.
     */
    public function findGuestByEmail(CustomerEmailInterface $email)
    {
        $isGuest     = true;
        $filterArray = [
            'customers_email_address' => (string)$email,
            'account_type'            => (string)(int)$isGuest
        ];
        
        return $this->_findByFilter($filterArray);
    }
    
    
    /**
     * Helper method which searches for user data based on an applied filter.
     *
     * @param array $filterArray Filters.
     *
     * @return Customer|null Customer or null if not found.
     */
    protected function _findByFilter(array $filterArray)
    {
        $customerDataArray = $this->db->get_where('customers', $filterArray)->row_array();
        if (empty($customerDataArray)) {
            return null;
        }
        
        return $this->_createCustomerByArray($customerDataArray);
    }
    
    
    /**
     * Creates a customer based on the provided data.
     *
     * @param array $customerDataArray Customer data.
     *
     * @return Customer $customer Created customer.
     *
     * @todo If date of birth is null in the database then: $customerDataArray['customers_dob'] = '0000-00-00 00:00:00'
     *       and then the getDateOfBirth() will return wrong results ($customer->getDateOfBirth() >> -0001-11-30
     *       00:00:00).
     */
    protected function _createCustomerByArray(array $customerDataArray)
    {
        $customerDataArray = $this->stringHelperService->convertNullValuesToStringInArray($customerDataArray);
        
        $customer = $this->customerFactory->createCustomer();
        $customer->setId(new IdType($customerDataArray['customers_id']));
        $customer->setCustomerNumber(MainFactory::create('CustomerNumber', $customerDataArray['customers_cid']));
        $customer->setVatNumber(MainFactory::create('CustomerVatNumber', $customerDataArray['customers_vat_id']));
        $customer->setVatNumberStatus($customerDataArray['customers_vat_id_status']);
        $customer->setStatusId($customerDataArray['customers_status']);
        $customer->setGender(MainFactory::create('CustomerGender', $customerDataArray['customers_gender']));
        $customer->setFirstname(MainFactory::create('CustomerFirstname', $customerDataArray['customers_firstname']));
        $customer->setLastname(MainFactory::create('CustomerLastname', $customerDataArray['customers_lastname']));
        $customer->setDateOfBirth(MainFactory::create('CustomerDateOfBirth', $customerDataArray['customers_dob']));
        $customer->setEmail(MainFactory::create('CustomerEmail', $customerDataArray['customers_email_address']));
        
        // password is not mandatory for guests
        if ((int)$customerDataArray['account_type'] === 0
            || !empty($customerDataArray['customers_password'])) {
            $customer->setPassword(MainFactory::create('CustomerHashedPassword',
                                                       new NonEmptyStringType($customerDataArray['customers_password'])));
        }
        
        $customer->setTelephoneNumber(MainFactory::create('CustomerCallNumber',
                                                          $customerDataArray['customers_telephone']));
        $customer->setFaxNumber(MainFactory::create('CustomerCallNumber', $customerDataArray['customers_fax']));
        $customer->setGuest((boolean)(int)$customerDataArray['account_type']);
        
        $customerAddress = $this->customerAddressRepository->getById(new IdType((int)$customerDataArray['customers_default_address_id']));
        $customer->setDefaultAddress($customerAddress);
        
        return $customer;
    }
    
    
    /**
     * Filters customer records and returns an array with results.
     *
     * Example:
     *        $repository->filterCustomers('customers_id' => 1);
     *
     * @param array       $conditions Associative array containing the desired field and value.
     * @param \Pager|null $pager      (Optional) Pager object with pagination information
     * @param array       $sorters    (Optional) array of Sorter objects with data sorting information
     *
     * @return array Returns an array that contains customer objects.
     */
    public function filterCustomers(array $conditions = [], \Pager $pager = null, array $sorters = [])
    {
        $this->_applyPagination($pager);
        $this->_applySorting($sorters);
        
        if (count($conditions) > 1) // connect multiple conditions with the "OR" operator
        {
            foreach ($conditions as $field => $value) {
                $this->db->or_where($field, $value);
            }
            $results = $this->db->get('customers')->result_array();
        } else {
            $results = $this->db->get_where('customers', $conditions)->result_array();
        }
        
        $customers = [];
        
        foreach ($results as $item) {
            $customers[] = $this->_createCustomerByArray($item);
        }
        
        return $customers;
    }
    
    
    /**
     * Filters customer records and returns the total count.
     *
     * Example:
     *        $reader->filterCustomers( array('customers_id' => 1) );
     *
     * @param array $conditions Associative array containing the desired field and value.
     *
     * @return int Returns the total customers count.
     */
    public function getFilterCustomersCount(array $conditions = [])
    {
        
        if (count($conditions) > 1) // connect multiple conditions with the "OR" operator
        {
            foreach ($conditions as $field => $value) {
                $this->db->or_where($field, $value);
            }
        } else {
            $this->db->where($conditions);
        }
        
        $totalCount = $this->db->count_all_results('customers');
        
        return $totalCount;
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
        $this->_applyPagination($pager);
        $this->_applySorting($sorters);
        $results = $this->db->get_where('customers', $condition->buildSql())->result_array();
        
        $customers = [];
        foreach ($results as $item) {
            $customers[] = $this->_createCustomerByArray($item);
        }
        
        return $customers;
    }
}
