<?php
/*--------------------------------------------------------------
   CustomerAddressWriteService.php 2022-10-24
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
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\CreationOfCustomerAddressFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\DeletionOfCustomerAddressFailedException;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\Exceptions\StorageOfCustomerAddressFailedException;

/**
 * Interface CustomerAddressWriteService
 *
 * @package Gambio\Admin\Modules\CustomerAddress\Services
 */
interface CustomerAddressWriteService
{
    /**
     * Creates a new customer address and returns its ID.
     *
     * @param int                 $customerId
     * @param PersonalInformation $personalInformation
     * @param LocationInformation $locationInformation
     *
     * @return CustomerAddressId
     * @throws CreationOfCustomerAddressFailedException
     */
    public function createCustomerAddress(
        int $customerId,
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
     * @param int $addressId
     *
     * @return void
     * @throws DeletionOfCustomerAddressFailedException
     */
    public function deleteCustomerAddress(int $addressId): void;
}