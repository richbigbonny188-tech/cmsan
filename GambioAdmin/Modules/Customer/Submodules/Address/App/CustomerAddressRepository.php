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

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App;

use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressMapper;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressReader;
use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressWriter;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddresses;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddressIds;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressRepository as CustomerAddressRepositoryInterface;

/**
 * Class CustomerAddressRepository
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App
 */
class CustomerAddressRepository implements CustomerAddressRepositoryInterface
{
    private CustomerAddressReader $reader;
    private CustomerAddressWriter $writer;
    private CustomerAddressMapper $mapper;
    
    
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
    public function getAddresses(CustomerId $customerId): CustomerAddresses
    {
        return $this->mapper->mapCustomerAddresses($this->reader->getCustomerAddresses($customerId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomerAddress(
        CustomerId          $customerId,
        PersonalInformation $personalInformation,
        LocationInformation $locationInformation
    ): CustomerAddressId {
        
        $id = $this->writer->createCustomerAddress($customerId, $personalInformation, $locationInformation, false);
        
        return $this->mapper->createCustomerAddressId($id);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomerAddresses(array ...$creationArguments): CustomerAddressIds
    {
        $ids = [];
        
        foreach ($creationArguments as $args) {
            
            $ids[] = $this->createCustomerAddress(...$args);
        }
        
        return $this->mapper->createCustomerAddressIds(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomerAddresses(CustomerAddress ...$customerAddresses): void
    {
        $this->writer->storeCustomerAddresses(false, ...$customerAddresses);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerAddress(CustomerAddressId $addressId): void
    {
        $this->writer->deleteCustomerAddress($addressId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getStateId(string $countryIsoCode, string $stateName): int
    {
        return $this->reader->getStateId($countryIsoCode, $stateName);
    }
}