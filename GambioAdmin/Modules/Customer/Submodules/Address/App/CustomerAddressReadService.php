<?php
/*--------------------------------------------------------------
   CustomerAddressReadService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\Collections\CustomerAddresses;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressReadService as CustomerAddressReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressRepository as CustomerAddressRepositoryInterface;

/**
 * Class CustomerAddressReadService
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App
 */
class CustomerAddressReadService implements CustomerAddressReadServiceInterface
{
    private CustomerAddressFactory             $factory;
    private CustomerAddressRepositoryInterface $repository;
    
    
    /**
     * @param CustomerAddressFactory             $factory
     * @param CustomerAddressRepositoryInterface $repository
     */
    public function __construct(
        CustomerAddressFactory             $factory,
        CustomerAddressRepositoryInterface $repository
    ) {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAddresses(int $customerId): CustomerAddresses
    {
        $customerId = $this->factory->createCustomerId($customerId);
        
        return $this->repository->getAddresses($customerId);
    }
}