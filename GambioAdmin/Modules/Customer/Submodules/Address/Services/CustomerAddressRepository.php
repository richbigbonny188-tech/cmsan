<?php
/*--------------------------------------------------------------
   CustomerAddressRepository.php 2022-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddresses;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddressIds;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CreationOfCustomerAddressFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CustomerAddressDoesNotExistException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\DeletionOfCustomerAddressFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\StorageOfCustomerAddressFailedException;

/**
 * Interface CustomerAddressRepository
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services
 */
interface CustomerAddressRepository
{
    /**
     * Returns the addresses of a customer
     *
     * @param CustomerId $customerId
     *
     * @return CustomerAddresses
     * @throws CustomerAddressDoesNotExistException
     */
    public function getAddresses(CustomerId $customerId): CustomerAddresses;
    
    /**
     * Creates a new customer address and returns its ID.
     *
     * @param CustomerId                 $customerId
     * @param PersonalInformation $personalInformation
     * @param LocationInformation $locationInformation
     *
     * @return CustomerAddressId
     * @throws CreationOfCustomerAddressFailedException
     */
    public function createCustomerAddress(
        CustomerId $customerId,
        PersonalInformation $personalInformation,
        LocationInformation $locationInformation
    ): CustomerAddressId;
    
    
    /**
     * Creates multiple new customer address and returns its ID.
     *
     * @param array ...$creationArguments
     *
     * @return CustomerAddressIds
     * @throws CreationOfCustomerAddressFailedException
     */
    public function createCustomerAddresses(array ...$creationArguments): CustomerAddressIds;
    
    
    /**
     * Stores multiple customer addresses.
     *
     * @param CustomerAddress ...$customerAddresses
     *
     * @return void
     * @throws StorageOfCustomerAddressFailedException
     */
    public function storeCustomerAddresses(CustomerAddress ...$customerAddresses): void;
    
    
    /**
     * @param CustomerAddressId $addressId
     *
     * @return void
     * @throws DeletionOfCustomerAddressFailedException
     */
    public function deleteCustomerAddress(CustomerAddressId $addressId): void;
    
    
    /**
     * @param string $countryIsoCode
     * @param string $stateName
     *
     * @return int
     */
    public function getStateId(string $countryIsoCode, string $stateName): int;
}