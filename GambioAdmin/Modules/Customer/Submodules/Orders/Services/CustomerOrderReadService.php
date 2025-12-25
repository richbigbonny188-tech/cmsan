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

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Services;

use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\CustomerOrders;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\CustomerOrder;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\Exceptions\CustomerOrderDoesNotExist;

/**
 * Interface CustomerOrderReadService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Services
 */
interface CustomerOrderReadService
{
    /**
     * Returns all available customer orders.
     *
     * @param int $customerId
     *
     * @return CustomerOrders
     */
    public function getCustomerOrders(int $customerId): CustomerOrders;
    
    
    /**
     * Returns a specific customer order based on the given ID.
     *
     * @param int $orderId
     *
     * @return CustomerOrder
     *
     * @throws CustomerOrderDoesNotExist
     */
    public function getCustomerMemoById(int $orderId): CustomerOrder;
}