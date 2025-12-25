<?php
/*--------------------------------------------------------------
   CustomerWriteService.php 2022-01-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services;

use Gambio\Admin\Modules\Customer\Model\Collections\CustomerIds;
use Gambio\Admin\Modules\Customer\Model\Customer;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\BusinessInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\ContactInformation;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CreationOfCustomerFailedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\CustomerEmailAddressMustBeUniqueException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfCustomerFailedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\DeletionOfMainAdminNotPermittedException;
use Gambio\Admin\Modules\Customer\Services\Exceptions\StorageOfCustomerFailedException;

/**
 * Interface CustomerWriteService
 *
 * @package Gambio\Admin\Modules\Customer\Services
 */
interface CustomerWriteService
{
    /**
     * Creates a new customer and returns its ID.
     *
     * @internal Please don't forget to create a default address, because the legacy frontend expects one for each
     *           customer.
     *
     * @param PersonalInformation $personalInformation
     * @param BusinessInformation $businessInformation
     * @param ContactInformation  $contactInformation
     * @param float               $credit
     * @param bool                $isFavorite
     * @param int|null            $customerGroup
     *
     * @return CustomerId
     *
     * @throws CreationOfCustomerFailedException
     */
    public function createCustomer(
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        float               $credit,
        bool                $isFavorite = false,
        ?int                $customerGroup = null
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
     * @param PersonalInformation $personalInformation
     * @param BusinessInformation $businessInformation
     * @param ContactInformation  $contactInformation
     * @param float               $credit
     * @param bool                $isFavorite
     * @param int|null            $customerGroup
     *
     * @return CustomerId
     *
     * @throws CreationOfCustomerFailedException
     */
    public function createGuestAccount(
        PersonalInformation $personalInformation,
        BusinessInformation $businessInformation,
        ContactInformation  $contactInformation,
        float               $credit,
        bool                $isFavorite = false,
        ?int                $customerGroup = null
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
     * @throws CustomerEmailAddressMustBeUniqueException
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
     * @throws CustomerEmailAddressMustBeUniqueException
     */
    public function storeCustomers(Customer ...$customers): void;
    
    
    /**
     * Deletes customers based on the given customer IDs.
     *
     * @param int ...$ids
     *
     * @return void
     *
     * @throws DeletionOfCustomerFailedException
     * @throws DeletionOfMainAdminNotPermittedException
     */
    public function deleteCustomers(int ...$ids): void;
    
    
    /**
     * Deletes all outdated guests customers (the ones without an active session).
     *
     * @return void
     */
    public function deleteOutdatedGuestAccounts(): void;
}