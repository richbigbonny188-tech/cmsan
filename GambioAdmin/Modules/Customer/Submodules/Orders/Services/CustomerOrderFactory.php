<?php
/*--------------------------------------------------------------
   CustomerOrderFactory.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Services;

use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\CustomerOrders;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\ProductIds;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\CustomerOrder;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\OrderStatus;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\ProductId;

/**
 * Class CustomerOrderFactory
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Services
 */
class CustomerOrderFactory
{
    /**
     * @param int $customerId
     *
     * @return CustomerId
     */
    public function createCustomerId(int $customerId): CustomerId
    {
        return CustomerId::create($customerId);
    }
    
    
    /**
     * @param int $orderId
     *
     * @return OrderId
     */
    public function createOrderId(int $orderId): OrderId
    {
        return OrderId::create($orderId);
    }
    
    
    /**
     * @param int $productId
     *
     * @return ProductId
     */
    public function createProductId(int $productId): ProductId
    {
        return ProductId::create($productId);
    }
    
    
    /**
     * @param ProductId ...$productIds
     *
     * @return ProductIds
     */
    public function createProductIds(ProductId ...$productIds): ProductIds
    {
        return ProductIds::create(...$productIds);
    }
    
    
    /**
     * @param CustomerOrder ...$customerOrders
     *
     * @return CustomerOrders
     */
    public function createCustomerOrders(CustomerOrder ...$customerOrders): CustomerOrders
    {
        return CustomerOrders::create(...$customerOrders);
    }
    
    
    /**
     * @param array $orderStatus
     *
     * @return OrderStatus
     */
    public function createOrderStatus(array $orderStatus): OrderStatus
    {
        return OrderStatus::create($orderStatus);
    }
}