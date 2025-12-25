<?php
/*--------------------------------------------------------------
   CustomerOrder.php 2022-09-13
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\Model;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\ProductIds;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\ValueObjects\OrderStatus;
use Gambio\Admin\Modules\CustomerOrders\Services\Exceptions\LanguageCodeDoesNotExistException;

/**
 * Class CustomerOrder
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\Model
 */
class CustomerOrder
{
    private OrderId           $orderId;
    private CustomerId        $customerId;
    private ProductIds        $productIds;
    private DateTimeImmutable $orderDate;
    private OrderStatus       $orderStatus;
    private string            $shippingCountry;
    private string            $paymentMethod;
    private float             $totalAmount;
    
    
    /**
     */
    private function __construct(
        OrderId           $orderId,
        CustomerId        $customerId,
        ProductIds        $productIds,
        DateTimeImmutable $orderDate,
        OrderStatus       $orderStatus,
        float             $totalAmount,
        string            $shippingCountry,
        string            $paymentMethod
    ) {
        $this->orderId         = $orderId;
        $this->customerId      = $customerId;
        $this->productIds      = $productIds;
        $this->orderDate       = $orderDate;
        $this->orderStatus     = $orderStatus;
        $this->totalAmount     = $totalAmount;
        $this->shippingCountry = $shippingCountry;
        $this->paymentMethod   = $paymentMethod;
    }
    
    
    /**
     * @param OrderId           $orderId
     * @param CustomerId        $customerId
     * @param ProductIds        $productIds
     * @param DateTimeImmutable $orderDate
     * @param OrderStatus       $orderStatus
     * @param float             $totalAmount
     * @param string            $shippingCountry
     * @param string            $paymentMethod
     *
     * @return CustomerOrder
     */
    public static function create(
        OrderId           $orderId,
        CustomerId        $customerId,
        ProductIds        $productIds,
        DateTimeImmutable $orderDate,
        OrderStatus       $orderStatus,
        float             $totalAmount,
        string            $shippingCountry,
        string            $paymentMethod
    ): CustomerOrder {
    
        return new self($orderId,
                        $customerId,
                        $productIds,
                        $orderDate,
                        $orderStatus,
                        $totalAmount,
                        $shippingCountry,
                        $paymentMethod);
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return array
     */
    public function toArray(string $datetimeFormat = 'Y-m-d H:i:s'): array
    {
        return [
            'orderId'         => $this->orderId(),
            'customerId'      => $this->customerId(),
            'productIds'      => $this->productIds()->toArray(),
            'orderDate'       => $this->orderDate($datetimeFormat),
            'shippingCountry' => $this->shippingCountry(),
            'paymentMethod'   => $this->paymentMethod(),
            'totalAmount'     => $this->totalAmount(),
            'orderStatus'     => $this->orderStatus->toArray(),
        ];
    }
    
    
    /**
     * @return int
     */
    public function orderId(): int
    {
        return $this->orderId->value();
    }
    
    
    /**
     * @return int
     */
    public function customerId(): int
    {
        return $this->customerId->value();
    }
    
    
    /**
     * @return ProductIds
     */
    public function productIds(): ProductIds
    {
        return $this->productIds;
    }
    
    
    /**
     * @param string $datetimeFormat
     *
     * @return string
     */
    public function orderDate(string $datetimeFormat = 'Y-m-d H:i:s'): string
    {
        return $this->orderDate->format($datetimeFormat);
    }
    
    
    /**
     * @return float
     */
    public function totalAmount(): float
    {
        return $this->totalAmount;
    }
    
    
    /**
     * @return string
     */
    public function shippingCountry(): string
    {
        return $this->shippingCountry;
    }
    
    
    /**
     * @return string
     */
    public function paymentMethod(): string
    {
        return $this->paymentMethod;
    }
}