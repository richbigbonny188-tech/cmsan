<?php
/*--------------------------------------------------------------
   CustomerDefaultAddressWriteService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddressIds;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\CustomerAddressId;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\LocationInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Model\ValueObjects\PersonalInformation;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressRepository;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressWriteService as CustomerDefaultAddressWriteServiceInterface;

/**
 * Class CustomerDefaultAddressWriteService
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App
 */
class CustomerDefaultAddressWriteService implements CustomerDefaultAddressWriteServiceInterface
{
    private CustomerDefaultAddressRepository $repository;
    private CustomerAddressFactory           $factory;
    
    
    /**
     * @param \Gambio\Admin\Modules\Customer\Submodules\Address\App\CustomerDefaultAddressRepository $repository
     * @param CustomerAddressFactory                                                                 $factory
     */
    public function __construct(
        CustomerDefaultAddressRepository $repository,
        CustomerAddressFactory           $factory
    ) {
        $this->repository = $repository;
        $this->factory    = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomerAddress(
        int                 $customerId,
        PersonalInformation $personalInformation,
        LocationInformation $locationInformation
    ): CustomerAddressId {
        
        $customerId = $this->factory->createCustomerId($customerId);
        
        return $this->repository->createCustomerAddress($customerId, $personalInformation, $locationInformation);
    }
    
    
    /**
     * @inheritDoc
     */
    public function createCustomerAddresses(array ...$creationArguments): CustomerAddressIds
    {
        $creationArguments = array_map(function (array $args): array {
            $args[0] = $this->factory->createCustomerId($args[0]);
            return $args;
        }, $creationArguments);
        
        return $this->repository->createCustomerAddresses(...$creationArguments);
    }
    
    
    /**
     * @inheritDoc
     */
    public function storeCustomerAddresses(CustomerAddress ...$customerAddresses): void
    {
        $this->repository->storeCustomerAddresses(...$customerAddresses);
    }
}