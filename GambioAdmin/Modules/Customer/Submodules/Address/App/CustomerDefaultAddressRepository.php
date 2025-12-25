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

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App;

use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressMapper;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressReader;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressWriter;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddressIds;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressRepository as CustomerDefaultAddressRepositoryInterface;

/**
 * Class CustomerDefaultAddressRepository
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App
 */
class CustomerDefaultAddressRepository implements CustomerDefaultAddressRepositoryInterface
{
    private CustomerAddressReader $reader;
    private CustomerAddressMapper $mapper;
    private CustomerAddressWriter $writer;
    
    
    /**
     * @param CustomerAddressReader $reader
     * @param CustomerAddressWriter $writer
     * @param CustomerAddressMapper $mapper
     */
    public function __construct(
        CustomerAddressReader $reader,
        CustomerAddressWriter $writer,
        CustomerAddressMapper $mapper
    ) {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->mapper = $mapper;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getDefaultShippingAddress(CustomerId $customerId): CustomerAddress
    {
        return $this->mapper->mapCustomerAddress($this->reader->getDefaultAddress($customerId), true, true);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultPaymentAddress(CustomerId $customerId): CustomerAddress
    {
        return $this->mapper->mapCustomerAddress($this->reader->getDefaultAddress($customerId), true, true);
    }

    /**
     * @inheritDoc
     */
    public function createCustomerAddress(CustomerId $customerId, PersonalInformation $personalInformation, LocationInformation $locationInformation): CustomerAddressId
    {
        return $this->mapper->createCustomerAddressId($this->writer->createCustomerAddress($customerId, $personalInformation, $locationInformation));
    }

    /**
     * @inheritDoc
     */
    public function createCustomerAddresses(array ...$creationArguments): CustomerAddressIds
    {
        $ids = $this->writer->createCustomerAddresses(...$creationArguments);
        $ids = array_map([$this->mapper, 'createCustomerAddressId'], $ids);
        
        return $this->mapper->createCustomerAddressIds(...$ids);
    }

    /**
     * @inheritDoc
     */
    public function storeCustomerAddresses(CustomerAddress ...$customerAddresses): void
    {
        $this->writer->storeCustomerAddresses(true, ...$customerAddresses);
    }
}