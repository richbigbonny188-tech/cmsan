<?php
/*--------------------------------------------------------------
   CustomerOrderReadService.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\App;

use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\CustomerOrders;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\CustomerOrder;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderFactory;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderReadService as CustomerOrderReadServiceInterface;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderRepository as CustomerOrderRepositoryInterface;

/**
 * Class CustomerOrderReadService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\App
 */
class CustomerOrderReadService implements CustomerOrderReadServiceInterface
{
    private CustomerOrderFactory             $factory;
    private CustomerOrderRepositoryInterface $repository;
    
    
    /**
     * @param CustomerOrderFactory             $factory
     * @param CustomerOrderRepositoryInterface $repository
     */
    public function __construct(
        CustomerOrderFactory             $factory,
        CustomerOrderRepositoryInterface $repository
    ) {
        $this->factory    = $factory;
        $this->repository = $repository;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerOrders(int $customerId): CustomerOrders
    {
        $customerId = $this->factory->createCustomerId($customerId);
        
        return $this->repository->getCustomerOrders($customerId);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerMemoById(int $orderId): CustomerOrder
    {
        $orderId = $this->factory->createOrderId($orderId);
        
        return $this->repository->getCustomerOrderById($orderId);
    }
}