<?php
/* --------------------------------------------------------------
   CustomersApiController.inc.php 2022-05-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class CustomersApiV2Controller
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class CustomersApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var CustomerWriteService
     */
    protected $customerWriteService;
    
    /**
     * @var CustomerReadService
     */
    protected $customerReadService;
    
    /**
     * @var CountryService
     */
    protected $countryService;
    
    /**
     * @var AddressBookSErvice
     */
    protected $addressService;
    
    /**
     * @var CustomerJsonSerializer
     */
    protected $customerJsonSerializer;
    
    /**
     * @var AddressJsonSerializer
     */
    protected $addressJsonSerializer;
    
    
    /**
     * Initializes API Controller
     */
    protected function init()
    {
        $this->customerWriteService   = StaticGXCoreLoader::getService('CustomerWrite');
        $this->customerReadService    = StaticGXCoreLoader::getService('CustomerRead');
        $this->countryService         = StaticGXCoreLoader::getService('Country');
        $this->addressService         = StaticGXCoreLoader::getService('AddressBook');
        $this->customerJsonSerializer = MainFactory::create('CustomerJsonSerializer');
        $this->addressJsonSerializer  = MainFactory::create('AddressJsonSerializer');
        
        $this->_initializePagingAndSortingFields();
    }
    
    
    /**
     * @api        {post} /customers Create Customer
     * @apiVersion 2.3.0
     * @apiName    CreateCustomer
     * @apiGroup   Customers
     *
     * @apiDescription
     * This method enables the creation of a new customer (whether registree or a guest). Additionally
     * the user can provide new address information or just set the id of an existing one. Check the
     * examples bellow. An example script to demonstrate the creation of a new customer is located under
     * `./docs/REST/samples/customer-service/create_account.php` in the git clone, another one to demonstrate the
     * creation of a guest customer is located under `./docs/REST/samples/customer-service/create_guest_account.php`.
     */
    public function post()
    {
        if (($this->uri[1] ?? '') === 'search') {
            return $this->_search();
        }
        
        $customerJsonString = json_encode($this->request->getParsedBody());
        if (empty($customerJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Customer data were not provided.', 400);
        }
        
        if ($this->_isBulkRequest($customerJsonString)) {
            $response = $this->_createMultipleCustomers($customerJsonString);
            $this->_linkResponse($response['created']);
        } else {
            list($customer, $response) = $this->_createCustomer($customerJsonString);
            $this->_linkResponse($response);
            $this->_locateResource('customers', (string)$customer->getId());
        }
        
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 201);
    }
    
    
    /**
     * @api        {put} /customers/:id Update Customer
     * @apiVersion 2.3.0
     * @apiName    UpdateCustomer
     * @apiGroup   Customers
     *
     * @apiDescription
     * This method will update the information of an existing customer record. You will
     * need to provide all the customer information with the request (except from password
     * and customer id). Also note that you only have to include the "addressId" property.
     * An example script to demonstrate how to update the admin accounts telephone number
     * is located under `./docs/REST/samples/customer-service/update_admin_telephone.php`
     * in the git clone.
     */
    public function put()
    {
        $customerJsonString = json_encode($this->request->getParsedBody());
        if (empty($customerJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Customer data were not provided.', 400);
        }
        
        if ($this->_isBulkRequest($customerJsonString)) {
            $response = $this->_updateMultipleCustomer($customerJsonString);
            $this->_linkResponse($response['affected']);
        } else {
            if (!isset($this->uri[1]) || !is_numeric($this->uri[1] ?? null)) {
                throw new HttpApiV2Exception('Customer record ID was not provided or is invalid: '
                                             . gettype($this->uri[1] ?? null), 400);
            }
            
            $customerId = (int)$this->uri[1];
            $response   = $this->_updateCustomer($customerJsonString, $customerId);
            $this->_linkResponse($response);
        }
        
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 200);
    }
    
    
    /**
     * @api        {delete} /customers/:id Delete Customer
     * @apiVersion 2.1.0
     * @apiName    DeleteCustomer
     * @apiGroup   Customers
     *
     * @apiDescription
     * Remove a customer record from the system. This method will always return success
     * even if the customer does not exist (due to internal CustomerWriteService architecture
     * decisions, which strive to avoid unnecessary failures).
     * An example script to demonstrate how to delete a customer is located under
     * `./docs/REST/samples/customer-service/remove_account.php` in the git clone.
     */
    public function delete()
    {
        // Check if record ID was provided.
        if (!isset($this->uri[1])) {
            throw new HttpApiV2Exception('Customer record ID was not provided in the resource URL.', 400);
        }
        
        if ((int)$this->uri[1] === 1) {
            throw new HttpApiV2Exception('You are not allowed to delete the super admin!', 400);
        }
        
        if ($this->_isBulkDeleteRequest()) {
            $response = $this->_deleteMultipleCustomers(explode(',', $this->uri[1]));
        } else {
            $response = $this->_deleteCustomer($this->uri[1]);
        }
        
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 200);
    }
    
    
    /**
     * @api             {get} /customers/:id Get Customers
     * @apiVersion      2.3.0
     * @apiName         GetCustomer
     * @apiGroup        Customers
     *
     * @apiDescription
     * Get multiple or a single customer record through the GET method. This resource supports
     * the following GET parameters as described in the first section of documentation: sorting
     * minimization, search, pagination and links. Additionally you can filter customers by providing
     * the GET parameter "type=guest" or "type=registree". Sort and pagination GET parameters do not
     * apply when a single customer record is selected (e.g. api.php/v2/customers/84).
     * An example script to demonstrate how to fetch customer data is located under
     * `./docs/REST/samples/customer-service/get_admin_data.php` in the git clone
     */
    public function get()
    {
        if ($this->request->getQueryParam('changed', '') !== '' || $this->request->getQueryParam('modified', '') !== ''
            || $this->request->getQueryParam('deleted', '') !== '') {
            return $this->_changeHistory();
        }
        
        // Sub-Resource Customer addresses: api.php/v2/customers/:id/addresses
        if (isset($this->uri[2]) && $this->uri[2] === 'addresses') {
            $this->_getCustomerAddresses();
            
            return;
        }
        
        // Get Single Customer Record
        if (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            $customers      = $this->customerReadService->filterCustomers(['customers_id' => (int)$this->uri[1]],
                                                                          $this->pager,
                                                                          $this->sorters);
            $totalItemCount = 1;
            
            if (empty($customers)) {
                throw new HttpApiV2Exception('Customer record could not be found.', 404);
            }
        } // Search Customer Records
        else {
            $searchTerm = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
            if ($searchTerm !== null) {
                $searchKey = '%' . $searchTerm . '%';
                $search    = [
                    'customers_cid LIKE '           => $searchKey,
                    'customers_vat_id LIKE '        => $searchKey,
                    'customers_gender LIKE '        => $searchKey,
                    'customers_firstname LIKE '     => $searchKey,
                    'customers_lastname LIKE '      => $searchKey,
                    'customers_dob LIKE '           => $searchKey,
                    'customers_email_address LIKE ' => $searchKey,
                    'customers_telephone LIKE '     => $searchKey,
                    'customers_fax LIKE '           => $searchKey
                ];
                
                $customers      = $this->customerReadService->filterCustomers($search, $this->pager, $this->sorters);
                $totalItemCount = $this->customerReadService->getFilterCustomersCount($search);
            } // Filter customers by type ("guest" or "registree")
            else {
                if ($this->request->getQueryParam('type') !== null) {
                    $type = $this->request->getQueryParam('type');
                    
                    if ($type === 'guest') {
                        $customers      = $this->customerReadService->filterCustomers(['account_type' => '1'],
                                                                                      $this->pager,
                                                                                      $this->sorters);
                        $totalItemCount = $this->customerReadService->getFilterCustomersCount(['account_type' => '1']);
                    } else {
                        if ($type === 'registree') {
                            $customers      = $this->customerReadService->filterCustomers(['account_type' => '0'],
                                                                                          $this->pager,
                                                                                          $this->sorters);
                            $totalItemCount = $this->customerReadService->getFilterCustomersCount(['account_type' => '0']);
                        } else {
                            throw new HttpApiV2Exception('Invalid customer type filter provided, expected "guest" or "registree" and got: '
                                                         . $type, 400);
                        }
                    }
                } // Get all registered customer records without applying filters.
                else {
                    $customers      = $this->customerReadService->filterCustomers([], $this->pager, $this->sorters);
                    $totalItemCount = $this->customerReadService->getFilterCustomersCount([]);
                }
            }
        }
        
        // Prepare response data.
        $response = [];
        foreach ($customers as $customer) {
            $response[] = $this->customerJsonSerializer->serialize($customer, false);
        }
        
        $this->_sortResponse($response);
        
        $this->_setPaginationHeaderByPage($this->pager, $totalItemCount);
        
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        
        // Return single resource to client and not array.
        if (isset($this->uri[1]) && is_numeric($this->uri[1]) && count($response) > 0) {
            $response = $response[0];
        }
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * Sub-Resource Customer Addresses
     *
     * This method will return all the addresses of the required customer, providing a fast
     * way to access relations between customers and addresses.
     *
     * @throws HttpApiV2Exception
     * @see CustomersApiV2Controller::get()
     *
     */
    protected function _getCustomerAddresses()
    {
        if (!isset($this->uri[1]) && is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Invalid customer ID provided: ' . gettype($this->uri[1]), 400);
        }
        
        $customer  = $this->customerReadService->getCustomerById(new IdType((int)$this->uri[1]));
        $addresses = $this->addressService->getCustomerAddresses($customer);
        
        $response = [];
        foreach ($addresses as $address) {
            $response[] = $this->addressJsonSerializer->serialize($address, false);
        }
        
        $this->_sortResponse($response);
        $this->_paginateResponse($response);
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        $this->_writeResponse($response);
    }
    
    
    /**
     * Sub-Resource Customer Search
     *
     * This method will search all customers with a with an given search condition as json.
     *
     * @see CustomersApiV2Controller::post()
     */
    protected function _search()
    {
        $json            = json_encode($this->request->getParsedBody());
        $searchCondition = CustomerSearchCondition::createByJson(new NonEmptyStringType($json));
        
        try {
            $customers = $this->customerReadService->searchCustomers($searchCondition, $this->pager, $this->sorters);
        } catch (Exception $e) {
            throw new HttpApiV2Exception($e->getMessage(), 400, $e);
        }
        
        // Prepare response data.
        $response = [];
        foreach ($customers as $customer) {
            $response[] = $this->customerJsonSerializer->serialize($customer, false);
        }
        
        $this->_sortResponse($response);
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        
        // Return single resource to client and not array.
        if (isset($this->uri[1]) && is_numeric($this->uri[1]) && count($response) > 0) {
            $response = $response[0];
        }
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * @param $customerJsonObject
     * @param $addressBlock
     * @param $addonValuesArray
     *
     * @return Customer
     */
    protected function _createRegisteredCustomer($customerJsonObject, $addressBlock, $addonValuesArray)
    {
        /** @var AuthService $authService */
        $authService    = StaticGXCoreLoader::getService('Auth');
        $hashedPassword = $authService->getHash(new StringType($customerJsonObject->password));
        
        $customer = $this->customerWriteService->createNewRegistree(MainFactory::create('CustomerEmail',
                                                                                        $customerJsonObject->email),
                                                                    MainFactory::create('CustomerHashedPassword',
                                                                                        new NonEmptyStringType($hashedPassword)),
                                                                    new DateTime($customerJsonObject->dateOfBirth),
                                                                    MainFactory::create('CustomerVatNumber',
                                                                                        $customerJsonObject->vatNumber),
                                                                    MainFactory::create('CustomerCallNumber',
                                                                                        $customerJsonObject->telephone),
                                                                    MainFactory::create('CustomerCallNumber',
                                                                                        $customerJsonObject->fax),
                                                                    $addressBlock,
                                                                    MainFactory::create('KeyValueCollection',
                                                                                        $addonValuesArray),
                                                                    MainFactory::create('CustomerNumber',(string)$customerJsonObject->number));
        
        return $customer;
    }
    
    
    /**
     * @param $customerJsonObject
     * @param $addressBlock
     * @param $addonValuesArray
     *
     * @return Customer
     */
    protected function _createGuest($customerJsonObject, $addressBlock, $addonValuesArray)
    {
        $customer = $this->customerWriteService->createNewGuest(MainFactory::create('CustomerEmail',
                                                                                    $customerJsonObject->email),
                                                                                    new DateTime($customerJsonObject->dateOfBirth),
                                                                MainFactory::create('CustomerVatNumber',
                                                                                    $customerJsonObject->vatNumber),
                                                                MainFactory::create('CustomerCallNumber',
                                                                                    $customerJsonObject->telephone),
                                                                MainFactory::create('CustomerCallNumber',
                                                                                    $customerJsonObject->fax),
                                                                $addressBlock,
                                                                MainFactory::create('KeyValueCollection',
                                                                                    $addonValuesArray),
                                                                MainFactory::create('CustomerNumber', property_exists($customerJsonObject, 'number') ? (string)$customerJsonObject->number : ''));
        
        return $customer;
    }
    
    
    /**
     * @param $customerJsonObject
     * @param $country
     * @param $zone
     *
     * @return bool
     */
    protected function _createNewAddressBlock($customerJsonObject, $country, $zone)
    {
        $addressBlock = MainFactory::create('AddressBlock',
                                            MainFactory::create('CustomerGender', $customerJsonObject->gender),
                                            MainFactory::create('CustomerFirstname', $customerJsonObject->firstname),
                                            MainFactory::create('CustomerLastname', $customerJsonObject->lastname),
                                            MainFactory::create('CustomerCompany',
                                                                $customerJsonObject->address->company),
                                            MainFactory::create('CustomerB2BStatus',
                                                                $customerJsonObject->address->b2bStatus),
                                            MainFactory::create('CustomerStreet', $customerJsonObject->address->street),
                                            MainFactory::create('CustomerHouseNumber',
                                                                $customerJsonObject->address->houseNumber),
                                            MainFactory::create('CustomerAdditionalAddressInfo',
                                                                $customerJsonObject->address->additionalAddressInfo),
                                            MainFactory::create('CustomerSuburb', $customerJsonObject->address->suburb),
                                            MainFactory::create('CustomerPostcode',
                                                                $customerJsonObject->address->postcode),
                                            MainFactory::create('CustomerCity', $customerJsonObject->address->city),
                                            $country,
                                            $zone);
        
        return $addressBlock;
    }
    
    
    /**
     * @param $customerJsonObject
     *
     * @return AddressBlock|bool
     */
    protected function _mapAddressBlock($customerJsonObject)
    {
        $address = $this->addressService->findAddressById(new IdType((int)$customerJsonObject->addressId));
        
        $addressBlock = MainFactory::create('AddressBlock',
                                            $address->getGender(),
                                            $address->getFirstname(),
                                            $address->getLastname(),
                                            $address->getCompany(),
                                            $address->getB2BStatus(),
                                            $address->getStreet(),
                                            $address->getHouseNumber(),
                                            $address->getAdditionalAddressInfo(),
                                            $address->getSuburb(),
                                            $address->getPostcode(),
                                            $address->getCity(),
                                            $address->getCountry(),
                                            $address->getCountryZone());
        
        return $addressBlock;
    }
    
    
    /**
     * History handler for modified, changed and deleted query parameters.
     *
     * @throws HttpApiV2Exception
     */
    protected function _changeHistory()
    {
        $changed = $this->request->getQueryParam('changed');
        if ($changed !== null) {
            $modified = $changed;
            $deleted  = $changed;
        } else {
            $modified = $this->request->getQueryParam('modified');
            $deleted  = $this->request->getQueryParam('deleted');
        }
        
        // Check format of modified and deleted date
        if ($modified !== null && !preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $modified)) {
            throw new HttpApiV2Exception('Given modified date is invalid. Expected format: 2018-09-25 15:59:01', 400);
        }
        if ($deleted !== null && !preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $deleted)) {
            throw new HttpApiV2Exception('Given deleted date is invalid. Expected format: 2018-09-25 15:59:01', 400);
        }
        
        $response = ['deleted' => [], 'modified' => []];
        
        if ($modified !== null) {
            $searchCondition = CustomerSearchCondition::createByArray(['search' => ['geq' => ['customers.customers_last_modified' => $modified]]]);
            $customers       = $this->customerReadService->searchCustomers($searchCondition,
                                                                           $this->pager,
                                                                           $this->sorters);
            foreach ($customers as $customer) {
                $serialized             = $this->customerJsonSerializer->serialize($customer, false);
                $response['modified'][] = $serialized;
            }
            $this->_linkResponse($response['modified']);
        }
        
        if ($deleted !== null) {
            $deleteHistoryReadService = DeleteHistoryServiceFactory::readService();
            $dateRange                = DateRange::create(new DateTime($deleted), new DateTime('now'));
            $deletedOrders            = $deleteHistoryReadService->findDeleted($dateRange,
                                                                               DeleteHistoryScope::customers());
            /** @var DeleteHistoryReportItem $deletedOrder */
            foreach ($deletedOrders as $deletedOrder) {
                $response['deleted'][] = [
                    'id'   => $deletedOrder->deletedId(),
                    'date' => $deletedOrder->deletedAt()->format('Y-m-d H:i:s'),
                ];
            }
        }
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * Checks if the performed request was a bulk request (only for POST and PUT requests).
     *
     * @param string $jsonString The complete request body as JSON string
     *
     * @return bool
     */
    protected function _isBulkRequest($jsonString)
    {
        $json = json_decode($jsonString);
        
        if ($json === null && json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: '
                                               . $jsonString);
        }
        
        return is_array($json) && array_keys($json) === range(0, count($json) - 1);
    }
    
    
    /**
     * Checks if the performed delete request was a bulk request.
     *
     * @return bool
     */
    protected function _isBulkDeleteRequest()
    {
        return strpos($this->uri[1], ',') !== false;
    }
    
    
    /**
     * Checks if the response to a bulk request contains any errors.
     *
     * @param array $response The checked response
     *
     * @return bool
     */
    protected function _hasErrors($response)
    {
        return is_array($response) && array_key_exists('errors', $response) && !empty($response['errors']);
    }
    
    
    /**
     * Creates a single customer and returns the reponse for this post request.
     *
     * @param string $customerJsonString Request body as json string
     *
     * @return array Response
     *
     * @throws HttpApiV2Exception If customer is already registered.
     */
    protected function _createCustomer($customerJsonString)
    {
        $customerJsonObject = json_decode($customerJsonString);
        
        // Check if customer email already exists.
        if (isset($customerJsonObject->email) && (property_exists($customerJsonObject, 'type') && $customerJsonObject->type === 'registree')
            && $this->customerReadService->registreeEmailExists(new CustomerEmail($customerJsonObject->email))) {
            throw new HttpApiV2Exception('Registree email address "' . $customerJsonObject->email
                                         . '" already exists in the database.', 409);
        }
        
        $country = $this->countryService->getCountryById(new IdType($customerJsonObject->address->countryId));
        $zone    = $this->countryService->getCountryZoneById(new IdType((int)$customerJsonObject->address->zoneId));
        if (property_exists($customerJsonObject, 'addressId') && $customerJsonObject->addressId !== null) {
            $addressBlock = $this->_mapAddressBlock($customerJsonObject);
        } else {
            $addressBlock = $this->_createNewAddressBlock($customerJsonObject, $country, $zone);
        }
        
        $addonValuesArray = [];
        if (isset($customerJsonObject->addonValues)) {
            $addonValuesArray = json_decode(json_encode($customerJsonObject->addonValues), true);
        }
        
        if (property_exists($customerJsonObject, 'isGuest') && $customerJsonObject->isGuest === true) {
            $customer = $this->_createGuest($customerJsonObject, $addressBlock, $addonValuesArray);
        } else {
            $customer = $this->_createRegisteredCustomer($customerJsonObject, $addressBlock, $addonValuesArray);
        }
        
        $response = $this->customerJsonSerializer->serialize($customer, false);
        
        return [$customer, $response];
    }
    
    
    /**
     * Creates multiple customers and returns the reponse for this post request.
     *
     * @param string $customerJsonString Request body as json string
     *
     * @return array Response
     */
    protected function _createMultipleCustomers($customerJsonString)
    {
        $response      = [
            'created' => [],
            'errors'  => [],
        ];
        $customerArray = json_decode($customerJsonString, true);
        foreach ($customerArray as $customerData) {
            try {
                list($customer, $createRespone) = $this->_createCustomer(json_encode($customerData));
                $response['created'][] = $createRespone;
            } catch (Exception $exception) {
                $response['errors'][] = [
                    'errorMessage' => $exception->getMessage(),
                    'stacktrace'   => $exception->getTrace(),
                ];
            }
        }
        
        return $response;
    }
    
    
    /**
     * Updates a single user and returns the response for this put request.
     *
     * @param string $customerJsonString Request body as string.
     * @param int    $customerId         Id of the customer that should be updated.
     *
     * @return array
     *
     * @throws HttpApiV2Exception If customer does not exists.
     */
    protected function _updateCustomer($customerJsonString, $customerId)
    {
        // Fetch existing customer record.
        $customers = $this->customerReadService->filterCustomers(['customers_id' => $customerId]);
        
        if (empty($customers)) {
            throw new HttpApiV2Exception('Customer record was not found.', 404);
        }
        
        $customer = array_shift($customers);
        
        // Ensure that the customer has the correct customer id of the request url
        $customerJsonString = $this->_setJsonValue($customerJsonString, 'id', $customerId);
        
        // Apply provided values into it.
        $customer = $this->customerJsonSerializer->deserialize($customerJsonString, $customer);
        
        // Check if new email belongs to another customer.
        $db = StaticGXCoreLoader::getDatabaseQueryBuilder();
        
        $count = $db->get_where('customers',
                                [
                                    'customers_email_address' => (string)$customer->getEmail(),
                                    'customers_id <>'         => (string)$customer->getId()
                                ])->num_rows();
        
        if ($count) {
            throw new HttpApiV2Exception('Provided email address is used by another customer: '
                                         . (string)$customer->getEmail(), 409);
        }
        
        // Update record and respond to client.
        $this->customerWriteService->updateCustomer($customer);
        $response = $this->customerJsonSerializer->serialize($customer, false);
        
        return $response;
    }
    
    
    /**
     * Updates multiple customers and returns the response for this put request.
     *
     * @param $customerJsonString Request body as json.
     *
     * @return array
     */
    protected function _updateMultipleCustomer($customerJsonString)
    {
        $customerArray = json_decode($customerJsonString, true);
        $response      = [
            'affected' => [],
            'errors'   => [],
        ];
        foreach ($customerArray as $customerData) {
            try {
                if (!isset($customerData['id']) || !is_numeric($customerData['id'])) {
                    throw new HttpApiV2Exception('Customer record ID was not provided or is invalid: '
                                                 . gettype($customerData['id']), 400);
                }
                
                $customerId             = (int)$customerData['id'];
                $updateResponse         = $this->_updateCustomer(json_encode($customerData), $customerId);
                $response['affected'][] = $updateResponse;
            } catch (Exception $exception) {
                $response['errors'][] = [
                    'errorMessage' => $exception->getMessage(),
                    'stacktrace'   => $exception->getTrace(),
                ];
            }
        }
        
        return $response;
    }
    
    
    /**
     * Deletes a single customer and returns the response for this delete request.
     *
     * @param int $customerId ID of the customer, that should be deleted.
     *
     * @return array JSON Response
     *
     * @throws HttpApiV2Exception Thrown if customer id is not set or invalid
     */
    protected function _deleteCustomer($customerId)
    {
        if (!is_numeric($customerId)) {
            throw new HttpApiV2Exception('Customer record ID was not provided in the resource URL.', 400);
        }
        
        $this->customerWriteService->deleteCustomerById(new IdType((int)$customerId));
        
        return [
            'code'       => 200,
            'status'     => 'success',
            'action'     => 'delete',
            'customerId' => $customerId
        ];
    }
    
    
    /**
     * Deletes multiple customers and returns the response for this delete request.
     *
     * @param array $ids
     *
     * @return array
     */
    protected function _deleteMultipleCustomers($ids)
    {
        $response = [
            'deleted' => [],
            'errors'  => [],
        ];
        foreach ($ids as $id) {
            try {
                $this->_deleteCustomer($id);
                $response['deleted'][] = (int)$id;
            } catch (Exception $exception) {
                $response['errors'][] = [
                    'errorMessage' => $exception->getMessage(),
                    'stacktrace'   => $exception->getTrace(),
                ];
            }
        }
        
        return $response;
    }
}
