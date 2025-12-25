<?php
/*--------------------------------------------------------------
   CustomerDefaultAddressReadService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Address\App;

use Gambio\Admin\Modules\Customer\Submodules\Address\Model\CustomerAddress;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerAddressFactory;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressReadService as CustomerDefaultAddressReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Address\Services\CustomerDefaultAddressRepository;

/**
 * Class CustomerDefaultAddressReadService
 *
 * @package Gambio\Admin\Modules\CustomerAddress\App
 */
class CustomerDefaultAddressReadService implements CustomerDefaultAddressReadServiceInterface
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
    public function getDefaultShippingAddress(int $customerId): CustomerAddress
    {
        return $this->repository->getDefaultShippingAddress($this->factory->createCustomerId($customerId));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getDefaultPaymentAddress(int $customerId): CustomerAddress
    {
        return $this->repository->getDefaultPaymentAddress($this->factory->createCustomerId($customerId));
    }
}