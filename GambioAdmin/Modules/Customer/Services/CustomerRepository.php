<?php
/*--------------------------------------------------------------
   CustomerRepository.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services;

use Gambio\Admin\Modules\Customer\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Customer\Model\Collections\Customers;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerFilters;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSearch;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSorting;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\BusinessInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\ContactInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerCredit;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerGroup;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CreationOfCustomerFailedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerDoesNotExistException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfCustomerFailedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfMainAdminNotPermittedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\EmailAddressIsInvalidException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\StorageOfCustomerFailedException;
use Gambio\Core\Filter\Pagination;

/**
 * Interface CustomerRepository
 *
 * @package Gambio\Admin\Modules\Customer\Services
 */
interface CustomerRepository
{
    /**
     * Returns a filtered and paginated collection of customers based on the given filter and sorting arguments.
     * The filters must be a map, that assigns an attribute its filtering pattern.
     * The sorting must be a comma-separated list of attributes. A `-` can be used to change the order to descending.
     *
     * @param CustomerFilters $filters
     * @param CustomerSorting $sorting
     * @param Pagination      $pagination
     *
     * @return Customers
     */
    public function filterCustomers(
        CustomerFilters $filters,
        CustomerSorting $sorting,
        Pagination      $pagination
    ): Customers;
    
    
    /**
     * Returns a paginated collection of customers based on the given search term and sorting arguments.
     * The sorting must be a comma-separated list of attributes. A `-` can be used to change the order to descending.
     *
     * @param CustomerSearch  $searchTerm
     * @param CustomerSorting $sorting
     * @param Pagination      $pagination
     *
     * @return Customers
     */
    public function searchCustomers(
        CustomerSearch  $searchTerm,
        CustomerSorting $sorting,
        Pagination      $pagination
    ): Customers;
    
    
    /**
     * Returns total count of customers based on the given search object.
     *
     * @param CustomerSearch $searchTerm
     *
     * @return int
     */
    public function getSearchedCustomerTotalCount(CustomerSearch $searchTerm): int;
    
    
    /**
     * Returns total count of customers based on the given filter arguments.
     * The filters must be a map, that assigns an attribute it's filtering pattern.
     *
     * @param CustomerFilters $filters
     *
     * @return int
     */
    public function getCustomersTotalCount(CustomerFilters $filters): int;
    
    
    /**
     * Returns all available customers.
     *
     * @return Customers
     */
    public function getCustomers(): Customers;
    
    
    /**
     * Returns all available customers that are guest accounts.
     *
     * @return Customers
     */
    public function getGuestAccounts(): Customers;
    
    
    /**
     * Returns a specific customer based on the given ID.
     *
     * @param CustomerId $id
     *
     * @return Customer
     *
     * @throws CustomerDoesNotExistException
     */
    public function getCustomerById(CustomerId $id): Customer;
    
    
    /**
     * Creates a new customer and returns its ID.
     *
     * @internal Please don't forget to create a default address, because the legacy frontend expects one for each
     *           customer.
     *
     * @param CustomerGroup       $customerGroup
     * @param PersonalInformation $personalInformation
     * @param BusinessInformation $businessInformation
     * @param ContactInformation  $contactInformation
     * @param CustomerCredit      $credit
     * @param bool                $isFavorite
     *
     * @return CustomerId
     *
     * @throws CreationOfCustomerFailedException
     * @throws CustomerEmailAddressMustBeUniqueException
     */
    public function createCustomer(
        CustomerGroup       $customerGroup,
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        CustomerCredit      $credit,
        bool                $isFavorite = false
    ): CustomerId;
    
    
    /**
     * Creates multiple customers and returns their IDs.
     *
     * @internal Please don't forget to create a default address, because the legacy frontend expects one for each
     *           customer.
     *
     * @param array ...$creationArguments
     *
     * @return CustomerIds
     *
     * @throws CreationOfCustomerFailedException
     * @throws CustomerEmailAddressMustBeUniqueException
     */
    public function createMultipleCustomers(array ...$creationArguments): CustomerIds;
    
    
    /**
     * Creates a new customer as a guest account and returns its ID.
     *
     * @internal Please don't forget to create a default address, because the legacy frontend expects one for each
     *           customer.
     *
     * @param CustomerGroup       $customerGroup
     * @param PersonalInformation $personalInformation
     * @param BusinessInformation $businessInformation
     * @param ContactInformation  $contactInformation
     * @param CustomerCredit      $credit
     * @param bool                $isFavorite
     *
     * @return CustomerId
     *
     * @throws CreationOfCustomerFailedException
     */
    public function createGuestAccount(
        CustomerGroup       $customerGroup,
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        CustomerCredit      $credit,
        bool                $isFavorite = false
    ): CustomerId;
    
    
    /**
     * Creates multiple customers as guest accounts and returns their IDs.
     *
     * @internal Please don't forget to create a default address, because the legacy frontend expects one for each
     *           customer.
     *
     * @param array ...$creationArguments
     *
     * @return CustomerIds
     *
     * @throws CreationOfCustomerFailedException
     */
    public function createMultipleGuestAccounts(array ...$creationArguments): CustomerIds;
    
    
    /**
     * Stores multiple customers.
     *
     * @param Customer ...$customers
     *
     * @return void
     *
     * @throws StorageOfCustomerFailedException
     */
    public function storeCustomers(Customer ...$customers): void;
    
    
    /**
     * Deletes customers based on the given customer IDs.
     *
     * @param CustomerId ...$ids
     *
     * @return void
     *
     * @throws DeletionOfCustomerFailedException
     * @throws DeletionOfMainAdminNotPermittedException
     */
    public function deleteCustomers(CustomerId ...$ids): void;
    
    
    /**
     * Deletes all outdated guests customers (the ones without an active session).
     *
     * @return void
     */
    public function deleteOutdatedGuestAccounts(): void;
    
    /**
     * Checks if an email address is valid in its self
     * or is already taken by another customer
     *
     * @param string $email
     *
     * @return true
     *
     * @throws EmailAddressIsInvalidException
     * @throws CustomerEmailAddressMustBeUniqueException
     */
    public function validateEmailAddress(string $email): bool;
}