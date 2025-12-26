<?php
/*--------------------------------------------------------------
   CustomerOrderMapper.php 2023-09-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Orders\App\Data;

use DateTimeImmutable;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\CustomerOrders;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\Collections\ProductIds;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Model\CustomerOrder;
use Gambio\Admin\Modules\Customer\Submodules\Orders\Services\CustomerOrderFactory;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class CustomerOrderMapper
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Orders\App\Data
 */
class CustomerOrderMapper extends CustomerOrderFactory
{
    /**
     * @var TextManager
     */
    private TextManager $textManager;
    
    
    /**
     * @param TextManager $textManager
     */
    public function __construct(TextManager $textManager)
    {
        $this->textManager = $textManager;
    }
    
    
    /**
     * @param array $data
     *
     * @return CustomerOrders
     */
    public function mapCustomerOrders(array $data): CustomerOrders
    {
        $orders = array_map([$this, 'mapCustomerOrder'], $data);
        
        return $this->createCustomerOrders(...$orders);
    }
    
    
    /**
     * @param array $data
     *
     * @return CustomerOrder
     */
    public function mapCustomerOrder(array $data): CustomerOrder
    {
        $orderId         = $this->createOrderId((int)$data['orders_id']);
        $customerId      = $this->createCustomerId((int)$data['customers_id']);
        $orderDate       = new DateTimeImmutable($data['date_purchased']);
        $orderStatus     = $this->createOrderStatus($data['orders_status']);
        $totalAmount     = (float)$data['value'];
        $paymentMethod   = $this->localizePaymentMethod($data['payment_class']);
        $shippingCountry = $this->localizeShippingCountry($data['delivery_country_iso_code_2']);
        $productIds      = $this->mapProductIds($data);
        
        return CustomerOrder::create($orderId,
                                     $customerId,
                                     $productIds,
                                     $orderDate,
                                     $orderStatus,
                                     $totalAmount,
                                     $shippingCountry,
                                     $paymentMethod);
    }
    
    
    /**
     * Maps product ids.
     *
     * @param array $data
     *
     * @return ProductIds
     */
    private function mapProductIds(array $data): ProductIds
    {
        $productIdsString = $data['product_ids'] ?? null;
        if ($productIdsString === null) {
            return $this->createProductIds();
        }
        
        try {
            $productIds = array_map('intval', explode(',', $data['product_ids']));
            $productIds = array_map([$this, 'createProductId'], $productIds);
            
            return $this->createProductIds(...$productIds);
        } catch (\Throwable $t) {
            return $this->createProductIds();
        }
    }
    /**
     * @param string $paymentMethodId
     *
     * @return string
     */
    private function localizePaymentMethod(string $paymentMethodId): string
    {
        $phrase = 'MODULE_PAYMENT_' . strtoupper($paymentMethodId) . '_TEXT_TITLE';
        
        $paymentMethod = $this->textManager->getPhraseText($phrase, $paymentMethodId);
        return $phrase === $paymentMethod ? ucfirst($paymentMethodId) : $paymentMethod;
    }
    
    
    /**
     * @param string $isoCode2
     *
     * @return string
     */
    private function localizeShippingCountry(string $isoCode2): string
    {
        return $this->textManager->getPhraseText($isoCode2, 'countries');
    }
}