<?php
/*--------------------------------------------------------------
   CustomerDefaultAddressRepository.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\Services;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddressIds;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CreationOfCustomerAddressFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CustomerAddressDoesNotExistException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\StorageOfCustomerAddressFailedException;

/**
 * Interface CustomerDefaultAddressRepository
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services
 */
interface CustomerDefaultAddressRepository
{
    /**
     * Returns the default shipping address of a customer
     *
     * @param CustomerId $customerId
     *
     * @return CustomerAddress
     * @throws CustomerAddressDoesNotExistException
     */
    public function getDefaultShippingAddress(CustomerId $customerId): CustomerAddress;
    
    
    /**
     * Returns the default payment address of a customer
     *
     * @param CustomerId $customerId
     *
     * @return CustomerAddress
     * @throws CustomerAddressDoesNotExistException
     */
    public function getDefaultPaymentAddress(CustomerId $customerId): CustomerAddress;
    
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
}