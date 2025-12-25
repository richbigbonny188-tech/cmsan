<?php
/* --------------------------------------------------------------
   AfterbuyOrderWriteService.php 2023-10-18
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
 * Class AfterbuyOrderWriteService
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Overloads\OrderWriteService
 */
class AfterbuyOrderWriteService extends AfterbuyOrderWriteService_parent
{
    private const AFTERBUY_ORDER_SENDER_COMMENT_START = 'Afterbuy';
    
    
    /**
     * Afterbuy overload of `OrderWriteService::updateOrderStatus`.
     * Send orders with status 'paid' or 'unpaid' to afterbuy if the module is installed and enabled.
     *
     * @param IdType      $orderId
     * @param IntType     $newOrderStatusId
     * @param StringType  $comment
     * @param BoolType    $customerNotified
     * @param IdType|null $customerId
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function updateOrderStatus(
        IdType     $orderId,
        IntType    $newOrderStatusId,
        StringType $comment,
        BoolType   $customerNotified,
        IdType     $customerId = null
    ) {
        parent::updateOrderStatus($orderId, $newOrderStatusId, $comment, $customerNotified, $customerId);
        
        $logger = AfterbuyLogger::createLogger();
        
        // omits endless loop if AfterbuyOrderSender updates order status, because we only want to send the
        // order data to afterbuy if the status was changed by something else than the AfterbuyOrderSender
        if ($this->strStartsWith($comment->asString(), self::AFTERBUY_ORDER_SENDER_COMMENT_START)) {
            $logger->debug("Afterbuy OrderWriteService overload execution stopped because it was called from AfterbuyOrderSender for order {$orderId->asInt()}");
            
            return;
        }
        
        $context  = $this->getContext($orderId, $newOrderStatusId, $comment, $customerNotified, $customerId);
        $services = $this->getServices($context);
        if (!$services) {
            $logger->debug("[EdgeCase] - Afterbuy services not found for order {$orderId->asInt()}");
            
            return;
        }
        
        [$checkPaidStatusService, $infoService] = $services;
        if (!$infoService->isInstalledAndEnabled()) {
            $logger->debug("Afterbuy not installed or enabled for order {$orderId->asInt()}");
            
            return;
        }
        
        try {
            $paidStatus = $checkPaidStatusService->getPaidStatus(new OrderId($orderId->asInt()));
            $logger->debug("Got paid status {$paidStatus->type()} for order {$orderId->asInt()}");
        } catch (AfterbuyOrderStatusPaidException $e) {
            $message = "Failed to get order paid status.\nError: {$e->getMessage()}";
            $context = $this->mergeThrowable($context, $e);
            $logger->error($message, $context);
            
            return;
        }
        
        if ($paidStatus->isPaid() || $paidStatus->isUnpaid()) {
            $afterbuyOrderSender = MainFactory::create(AfterbuyOrderSender::class, $orderId->asInt());
            try {
                $afterbuyOrderSender->processOrder();
                $logger->debug("Processed order {$orderId->asInt()} after changing order status ({$newOrderStatusId->asInt()})");
            } catch (AfterbuyException $e) {
                $message = "Failed to send order to Afterbuy.\nError: {$e->getMessage()}";
                $context = $this->mergeThrowable($context, $e);
                $logger->error($message, $context);
            } catch (Throwable $t) {
                $message = "Failed to send order to Afterbuy.\nError: {$t->getMessage()}";
                $context = $this->mergeThrowable($context, $t);
                $logger->error($message, $context);
            }
        }
    }
    
    
    /**
     * Update order status from AfterbuyOrderSender::processOrder().
     *
     * The processOrder() function MUST NOT call the updateOrderStatus() method as this might cause an uncontrolled
     * indirect recursion!
     *
     * @param IdType      $orderId
     * @param IntType     $newOrderStatusId
     * @param StringType  $comment
     * @param BoolType    $customerNotified
     * @param IdType|null $customerId
     *
     * @return void
     */
    public function updateOrderStatusAfterProcessOrder(
        IdType     $orderId,
        IntType    $newOrderStatusId,
        StringType $comment,
        BoolType   $customerNotified,
        IdType     $customerId = null
    )
    {
        parent::updateOrderStatus($orderId, $newOrderStatusId, $comment, $customerNotified, $customerId);
    }
    
    
    /**
     * Provides all necessary Afterbuy-services as array (in order to extract that array to variables).
     * Returns null and logs an error if one of the services are not available in the DI-Container.
     *
     * @param array $context
     *
     * @return array{0: AfterbuyCheckPaidStatusService, 1: AfterbuyInformationService}|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getServices(array $context): ?array
    {
        $container = LegacyDependencyContainer::getInstance();
        
        try {
            $checkPaidStatusService = $container->get(AfterbuyCheckPaidStatusService::class);
            $infoService            = $container->get(AfterbuyInformationService::class);
            
            return [$checkPaidStatusService, $infoService];
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $class   = isset($checkPaidStatusService) ? AfterbuyInformationService::class : AfterbuyCheckPaidStatusService::class;
            $message = "$class is not registered in the LegacyDependencyContainer.\nError: {$e->getMessage()}";
            $context = $this->mergeThrowable($context, $e);
            
            $logger = AfterbuyLogger::createLogger();
            $logger->error($message, $context);
            
            return null;
        }
    }
    
    
    /**
     * Returns the arguments as serialized array, used for logging.
     *
     * @param IdType      $orderId
     * @param IntType     $newOrderStatusId
     * @param StringType  $comment
     * @param BoolType    $customerNotified
     * @param IdType|null $customerId
     *
     * @return array
     */
    private function getContext(
        IdType     $orderId,
        IntType    $newOrderStatusId,
        StringType $comment,
        BoolType   $customerNotified,
        IdType     $customerId = null
    ): array {
        return [
            'orderId'          => $orderId->asInt(),
            'newOrderStatusId' => $newOrderStatusId->asInt(),
            'comment'          => $comment->asString(),
            'customerNotified' => $customerNotified->asBool(),
            'customerId'       => $customerId ? $customerId->asInt() : null,
        ];
    }
    
    
    /**
     * Merges data from the Throwable into the context array and returns
     * a new copy containing error information.
     *
     * @param array     $context
     * @param Throwable $t
     *
     * @return array
     */
    private function mergeThrowable(array $context, Throwable $t): array
    {
        $context['exception']     = [
            'message' => $t->getMessage(),
            'file'    => $t->getFile(),
            'line'    => $t->getLine(),
        ];
        $context['exceptionType'] = get_class($t);
        
        return $context;
    }
    
    
    /**
     * Checks if string $haystack starts with the string of $needle.
     *
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    private function strStartsWith(string $haystack, string $needle): bool
    {
        return 0 === strncmp($haystack, $needle, strlen($needle));
    }
}