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

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Services;

use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\CustomerOrders;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\CustomerOrder;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\Exceptions\CustomerOrderDoesNotExist;

/**
 * Interface CustomerOrderRepository
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Services
 */
interface CustomerOrderRepository
{
    /**
     * Returns all available customer orders.
     *
     * @param CustomerId $customerId
     *
     * @return CustomerOrders
     */
    public function getCustomerOrders(CustomerId $customerId): CustomerOrders;
    
    
    /**
     * Returns a specific customer order based on the given ID.
     *
     * @param OrderId $orderId
     *
     * @return CustomerOrder
     *
     * @throws CustomerOrderDoesNotExist
     */
    public function getCustomerOrderById(OrderId $orderId): CustomerOrder;
}