<?php
/* --------------------------------------------------------------
   AfterbuyCheckoutSuccessContentControl.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyInformationService;
use GXModules\Gambio\Afterbuy\OrderStatus\Exceptions\AfterbuyOrderStatusPaidException;
use GXModules\Gambio\Afterbuy\OrderStatus\Service\AfterbuyCheckPaidStatusService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AfterbuyCheckoutSuccessContentControl
 *
 * @package GXModules\Gambio\Afterbuy\Shop\Overloads\CheckoutSuccessContentControl
 */
class AfterbuyCheckoutSuccessContentControl extends AfterbuyCheckoutSuccessContentControl_parent
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function proceed(): void
    {
        parent::proceed();
        
        $services = $this->getServices();
        if (!$services) {
            return;
        }
        
        [$infoService, $checkPaidService] = $services;
        if (!$infoService->isInstalledAndEnabled()) {
            return;
        }
        
        $orderId = $this->getOrderId();
        if ($this->isPaidOrUnpaid($checkPaidService, $orderId)) {
            $this->callAfterbuyApi($orderId);
        }
    }
    
    
    /**
     * Provides afterbuy services.
     *
     * @return array{0: AfterbuyInformationService, 1: AfterbuyCheckPaidStatusService}|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getServices(): ?array
    {
        $container = LegacyDependencyContainer::getInstance();
        
        try {
            $infoService      = $container->get(AfterbuyInformationService::class);
            $checkPaidService = $container->get(AfterbuyCheckPaidStatusService::class);
            
            return [$infoService, $checkPaidService];
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $class   = isset($infoService) ? AfterbuyCheckPaidStatusService::class : AfterbuyInformationService::class;
            $message = "$class is not registered in LegacyDependencyContainer.\nError: {$e->getMessage()}";
            
            $logger = AfterbuyLogger::createLogger();
            $logger->error($message, [$this->getContextForThrowable($e)]);
            
            return null;
        }
    }
    
    
    /**
     * Returns the last order id.
     *
     * @return OrderId
     */
    protected function getOrderId(): OrderId
    {
        $orders_query = xtc_db_query("select orders_id, orders_status, payment_method from " . TABLE_ORDERS
                                     . " where customers_id = '" . $_SESSION['customer_id']
                                     . "' order by orders_id desc limit 1");
        $orders       = xtc_db_fetch_array($orders_query);
        $orderId      = $orders['orders_id'] ?? 0;
        
        return new OrderId((int)$orderId);
    }
    
    
    /**
     * Checks whether the order status of the order is considered paid or unpaid for Afterbuy.
     *
     * @param AfterbuyCheckPaidStatusService $checkPaidService
     * @param OrderId                        $orderId
     *
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function isPaidOrUnpaid(AfterbuyCheckPaidStatusService $checkPaidService, OrderId $orderId): bool
    {
        try {
            $paidStatus = $checkPaidService->getPaidStatus($orderId);
        } catch (AfterbuyOrderStatusPaidException $e) {
            $message = "Failed to get information if order status is paid/unpaid.\nError: {$e->getMessage()}";
            $context = [$this->getContextForThrowable($e)];
            
            $logger = AfterbuyLogger::createLogger();
            $logger->error($message, $context);
            
            return false;
        }
        
        return $paidStatus->isPaid() || $paidStatus->isUnpaid();
    }
    
    
    /**
     * Initializes the AfterbuyOrderSender and sends the order information
     * to Afterbuy immediately.
     *
     * @param OrderId $orderId
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function callAfterbuyApi(OrderId $orderId): void
    {
        $afterbuyOrderSender = MainFactory::create(AfterbuyOrderSender::class, $orderId->orderId());
        try {
            $afterbuyOrderSender->processOrder();
        } catch (AfterbuyException $e) {
            $message = "Failed to send order information to Afterbuy.\nError: {$e->getMessage()}";
            $context = [
                'orderId'   => $orderId->orderId(),
                'exception' => $this->getContextForThrowable($e),
            ];
            $logger  = AfterbuyLogger::createLogger();
            $logger->warning($message, $context);
        } catch (Throwable $t) {
            $message = "Failed to send order information to Afterbuy.\nError: {$t->getMessage()}";
            $context = [
                'orderId'       => $orderId->orderId(),
                'exceptionType' => get_class($t),
                'exception'     => $this->getContextForThrowable($t),
            ];
            $logger  = AfterbuyLogger::createLogger();
            $logger->warning($message, $context);
        }
    }
    
    
    /**
     * Creates a context array from any throwable.
     * Serializes the throwable to an array.
     *
     * @param Throwable $throwable
     *
     * @return array
     */
    private function getContextForThrowable(Throwable $throwable): array
    {
        return [
            'message' => $throwable->getMessage(),
            'code'    => $throwable->getCode(),
            'file'    => $throwable->getFile(),
            'line'    => $throwable->getLine(),
            'trace'   => $throwable->getTrace(),
        ];
    }
}