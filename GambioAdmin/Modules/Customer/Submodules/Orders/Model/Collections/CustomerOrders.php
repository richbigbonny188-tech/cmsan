<?php
/*--------------------------------------------------------------
   CustomerOrders.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\CustomerOrder;
use IteratorAggregate;
use Traversable;

/**
 * Class CustomerOrders
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections
 */
class CustomerOrders implements IteratorAggregate
{
    /**
     * @var CustomerOrder[]
     */
    private array $orders;
    
    
    /**
     * CustomerOrders constructor.
     *
     * @param CustomerOrder[] $orders
     */
    private function __construct(array $orders)
    {
        $this->orders = $orders;
    }
    
    
    /**
     * @param CustomerOrder ...$orders
     *
     * @return CustomerOrders
     */
    public static function create(CustomerOrder ...$orders): CustomerOrders
    {
        return new self($orders);
    }
    
    
    /**
     * @return Traversable|CustomerOrder[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->orders);
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_map(fn(CustomerOrder $order): array => $order->toArray(), $this->orders);
    }
}