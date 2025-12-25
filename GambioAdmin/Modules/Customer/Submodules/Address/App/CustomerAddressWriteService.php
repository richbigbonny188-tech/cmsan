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

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App;

use Gambio\Admin\Modules\Customer\Submodules\Address\App\Data\CustomerAddressMapper;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddressIds;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressRepository as CustomerAddressRepositoryInterace;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressWriteService as CustomerAddressWriteServiceInterface;

/**
 * Class CustomerAddressWriteService
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App
 */
class CustomerAddressWriteService implements CustomerAddressWriteServiceInterface
{
    private CustomerAddressMapper             $mapper;
    private CustomerAddressRepositoryInterace $repository;
    
    
    /**
     * @param CustomerAddressMapper             $mapper
     * @param CustomerAddressRepositoryInterace $repository
     */
    public function __construct(
        CustomerAddressMapper             $mapper,
        CustomerAddressRepositoryInterace $repository
    ) {
        $this->mapper     = $mapper;
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomerAddress(
        int                 $customerId,
        PersonalInformation $personalInformation,
        LocationInformation $locationInformation
    ): CustomerAddressId {
        
        $customerId = $this->mapper->createCustomerId($customerId);
        
        return $this->repository->createCustomerAddress($customerId, $personalInformation, $locationInformation);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomerAddresses(array ...$creationArguments): CustomerAddressIds
    {
        $ids = [];
    
        foreach ($creationArguments as $arguments) {
            
            $ids[] = $this->createCustomerAddress(...$arguments);
        }
        
        return $this->mapper->createCustomerAddressIds(...$ids);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomerAddresses(CustomerAddress ...$customerAddresses): void
    {
        $this->repository->storeCustomerAddresses(...$customerAddresses);
    }
    
    
    /**
     * @inheritDoc
     */
    public function deleteCustomerAddress(int $addressId): void
    {
        $this->repository->deleteCustomerAddress($this->mapper->createCustomerAddressId($addressId));
    }
}