<?php
/*--------------------------------------------------------------
   CustomerOrderRepository.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\App;

use Gambio\Admin\Modules\Customer\Submodules\Orders\App\Data\CustomerOrderMapper;
use Gambio\Admin\Modules\Customer\Submodules\Orders\App\Data\CustomerOrderReader;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\CustomerOrders;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\CustomerOrder;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderRepository as CustomerOrderRepositoryInterface;

/**
 * Class CustomerOrderRepository
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\App
 */
class CustomerOrderRepository implements CustomerOrderRepositoryInterface
{
    private CustomerOrderMapper $mapper;
    private CustomerOrderReader $reader;
    
    
    /**
     * @param CustomerOrderMapper $mapper
     * @param CustomerOrderReader $reader
     */
    public function __construct(
        CustomerOrderMapper $mapper,
        CustomerOrderReader $reader
    ) {
        $this->mapper = $mapper;
        $this->reader = $reader;
    }
    
    
    /**
     * @inheritDoc
     */
    public function getCustomerOrders(CustomerId $customerId): CustomerOrders
    {
        $data = $this->reader->getCustomerOrders($customerId);
        
        return $this->mapper->mapCustomerOrders($data);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerOrderById(OrderId $orderId): CustomerOrder
    {
        $data = $this->reader->getCustomerOrderById($orderId);
        
        return $this->mapper->mapCustomerOrder(array_shift($data));
    }
}