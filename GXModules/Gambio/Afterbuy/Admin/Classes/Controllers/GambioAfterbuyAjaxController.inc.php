<?php
/* --------------------------------------------------------------
   GambioAfterbuyAjaxController.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

use Doctrine\DBAL\Connection;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Service\AfterbuyInformationService;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderXmlApiService;
use GXModules\Gambio\Afterbuy\OrderStatus\Exceptions\AfterbuyOrderStatusPaidException;
use GXModules\Gambio\Afterbuy\OrderStatus\Model\AfterbuyOrderStatus;
use GXModules\Gambio\Afterbuy\OrderStatus\Service\AfterbuyCheckPaidStatusService;
use GXModules\Gambio\Afterbuy\OrderTracking\Exceptions\SyncTrackingLinksFailedException;
use GXModules\Gambio\Afterbuy\OrderTracking\Service\AfterbuyOrderTrackingLinkService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class GambioAfterbuyAjaxController
 *
 * @package GXModules/Gambio/Afterbuy/Admin/Classes/Controllers
 */
class GambioAfterbuyAjaxController extends AdminHttpViewController
{
    private const PHRASE_SENT_SUCCESSFULLY    = 'sent_successfully';
    private const PHRASE_SENT_UNSUCCESSFULLY  = 'sent_unsuccessfully';
    private const PHRASE_SENT_INITIAL         = 'sent_initial';
    private const PHRASE_SENT_UPDATE          = 'sent_update';
    private const PHRASE_UNKNOWN_ORDER_STATUS = 'unknown_order_status';
    private const PHRASE_UNAUTHENTICATED      = 'unauthenticated';
    private const PHRASE_MISSING_ORDER_ID     = 'missing_order_id';
    
    
    /**
     * @var AfterbuyCheckPaidStatusService|null
     */
    private ?AfterbuyCheckPaidStatusService $paidService;
    
    
    /**
     * @var AfterbuyOrderXmlApiService|null
     */
    private ?AfterbuyOrderXmlApiService $abXmlService;
    
    
    /**
     * @var AfterbuyInformationService|null
     */
    private ?AfterbuyInformationService $abInfoService;
    
    
    /**
     * @var LanguageTextManager|null
     */
    private ?LanguageTextManager $textManager;
    
    
    /**
     * @var AfterbuyOrderTrackingLinkService|null
     */
    private ?AfterbuyOrderTrackingLinkService $trackingLinkService;
    
    
    /**
     * @var AfterbuyLogger|null
     */
    private ?AfterbuyLogger $logger;
    
    
    /**
     * @return JsonHttpControllerResponse
     */
    public function actionSendOrder(): JsonHttpControllerResponse
    {
        try {
            $this->_isAdmin();
            $this->initServices();
            
            if (!$this->abInfoService->isInstalledAndEnabled()) {
                $errorMessage = 'Afterbuy module is not installed or enabled or XML-API credentials are missing';
                
                return $this->createErrorResponse($errorMessage);
            }
            
            $orderIdInt = $this->getOrderId();
            $orderId    = new OrderId($orderIdInt);
            
            $orderPaidStatus = $this->paidService->getPaidStatus($orderId);
            $message         = $this->sendToAfterbuy($orderIdInt, $orderPaidStatus);
            
            return $this->createSuccessResponse($message);
        } catch (AfterbuyException|AfterbuyOrderStatusPaidException $e) {
            return $this->createErrorResponse($e->getMessage());
        }
    }
    
    
    /**
     * Sends the order to afterbuy. Returns a translated success message.
     *
     * Only orders with status 'paid' or 'unpaid' are sent to Afterbuy. Unknown order status results in an error.
     *
     * If the order is paid and was already sent to afterbuy, only the XML-API is used to update the order
     * information, otherwise the whole order is sent to Afterbuy initially.
     *
     * If the order is unpaid but was not send to Afterbuy, the order will be sent initially.
     *
     * @param int                 $orderIdInt
     * @param AfterbuyOrderStatus $orderPaidStatus
     *
     * @return string
     * @throws AfterbuyException
     */
    protected function sendToAfterbuy(int $orderIdInt, AfterbuyOrderStatus $orderPaidStatus): string
    {
        $orderId       = new OrderId($orderIdInt);
        $isTransmitted = $this->isTransmitted($orderIdInt);
        
        $logMessage = "Start sending order with id '$orderIdInt' to afterbuy if order status is paid or unpaid";
        $logMessage .= ", current order paid status is '{$orderPaidStatus->type()}'";
        $this->logger->debug($logMessage);
        
        if ($orderPaidStatus->isPaid()) {
            if ($isTransmitted) {
                $this->abXmlService->updateOrderViaXmlApi($orderId);
                $message = $this->textManager->get_text(self::PHRASE_SENT_UPDATE);
            } else {
                $orderSender = MainFactory::create(AfterbuyOrderSender::class, $orderIdInt);
                $orderSender->processOrder();
                $message = $this->textManager->get_text(self::PHRASE_SENT_INITIAL);
            }
        } elseif (!$isTransmitted && $orderPaidStatus->isUnpaid()) {
            $orderSender = MainFactory::create(AfterbuyOrderSender::class, $orderIdInt);
            $orderSender->processOrder();
            $message = $this->textManager->get_text(self::PHRASE_SENT_INITIAL);
        } else {
            $logMessage = "Order with id '$orderIdInt' was not send to Afterbuy, because the order paid status is unknown";
            $this->logger->debug($logMessage);
            throw new AfterbuyException($this->textManager->get_text(self::PHRASE_UNKNOWN_ORDER_STATUS));
        }
        $logMessage = "Successfully sent order with id '$orderIdInt' to Afterbuy.\n$message";
        $this->logger->debug($logMessage);
        
        $this->syncTrackingLinks($orderIdInt);
        
        return $message;
    }
    
    
    /**
     * Initializes the controller services.
     *
     * @throws AfterbuyException
     */
    private function initServices(): void
    {
        try {
            $this->abXmlService        = LegacyDependencyContainer::getInstance()
                ->get(AfterbuyOrderXmlApiService::class);
            $this->paidService         = LegacyDependencyContainer::getInstance()
                ->get(AfterbuyCheckPaidStatusService::class);
            $this->trackingLinkService = LegacyDependencyContainer::getInstance()
                ->get(AfterbuyOrderTrackingLinkService::class);
            $this->abInfoService       = LegacyDependencyContainer::getInstance()
                ->get(AfterbuyInformationService::class);
            $this->textManager         = MainFactory::create(LanguageTextManager::class, 'afterbuy');
            $this->logger              = MainFactory::create(AfterbuyLogger::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            if ($this->abXmlService === null) {
                $class = AfterbuyOrderXmlApiService::class;
            } elseif ($this->paidService === null) {
                $class = AfterbuyCheckPaidStatusService::class;
            } else {
                $class = AfterbuyOrderTrackingLinkService::class;
            }
            $message = "$class is not registered in the LegacyDependencyContainer.\nError: {$e->getMessage()}";
            
            throw new AfterbuyException($message, 500, $e);
        }
    }
    
    
    /**
     * Utility method to create a success response.
     *
     * @param string $message
     *
     * @return JsonHttpControllerResponse
     */
    protected function createSuccessResponse(string $message): JsonHttpControllerResponse
    {
        $phrase = self::PHRASE_SENT_SUCCESSFULLY;
        $msg    = "{$this->textManager->get_text($phrase)}\n$message";
        
        return MainFactory::create(JsonHttpControllerResponse::class, ['message' => $msg]);
    }
    
    
    /**
     * Utility method to create an error response.
     *
     * @param string $errorMessage
     *
     * @return JsonHttpControllerResponse
     */
    protected function createErrorResponse(string $errorMessage): JsonHttpControllerResponse
    {
        $phrase  = self::PHRASE_SENT_UNSUCCESSFULLY;
        $message = "{$this->textManager->get_text($phrase)}\n$errorMessage";
        
        return MainFactory::create(JsonHttpControllerResponse::class, ['message' => $message]);
    }
    
    
    /**
     * Tries to return the order id from the http query.
     *
     * @return int
     * @throws AfterbuyException
     */
    protected function getOrderId(): int
    {
        $orderId = $this->_getQueryParameter('orderId');
        if ($orderId === null) {
            throw new AfterbuyException($this->textManager->get_text(self::PHRASE_MISSING_ORDER_ID));
        }
        
        return (int)$orderId;
    }
    
    
    /**
     * Tries to synchronize the Afterbuy tracking code with the given order.
     *
     * @param int $orderId
     */
    private function syncTrackingLinks(int $orderId): void
    {
        try {
            $this->trackingLinkService->syncTrackingLinksByOrderId(new OrderId($orderId));
        } catch (AfterbuyNotEnabledException|AfterbuyNotInstalledException $e) {
            return;
        } catch (AfterbuyResponseException $e) {
            $message = "Afterbuy response contained an error";
            $context = [
                'message' => $e->getMessage(),
                'orderId' => $orderId,
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ];
            $this->logger->warning($message, $context);
        } catch (SyncTrackingLinksFailedException $e) {
            $message = "Synchronizing tracking link with order id '$orderId' failed.";
            $context = [
                'message' => $e->getMessage(),
                'orderId' => $orderId,
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ];
            $this->logger->warning($message, $context);
        }
    }
    
    
    /**
     * Check if the customer is the admin.
     *
     * @throws AfterbuyException
     */
    protected function _isAdmin()
    {
        try {
            $this->validateCurrentAdminStatus();
            
            return true;
        } catch (LogicException $exception) {
            throw new AfterbuyException($this->textManager->get_text(self::PHRASE_UNAUTHENTICATED));
        }
    }
    
    
    /**
     * Checks if order is already transmitted to afterbuy.
     *
     * @param int $orderId
     *
     * @return bool
     * @throws AfterbuyException
     */
    protected function isTransmitted(int $orderId): bool
    {
        try {
            $connection = LegacyDependencyContainer::getInstance()->get(Connection::class);
            $qb         = $connection->createQueryBuilder();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $class   = Connection::class;
            $message = "$class is not registered in the LegacyDependencyContainer.\nError: {$e->getMessage()}";
            
            throw new AfterbuyException($message, $e->getCode(), $e);
        }
        
        $where = "orders_id = $orderId AND afterbuy_success = 1";
        try {
            $statement = $qb->select('orders_id')->from('orders')->where($where)->executeQuery();
            
            return $statement->fetchAssociative() !== false;
        } catch (\Doctrine\DBAL\Exception $e) {
            $message = "Database error when check order with id '$orderId' is transmitted to Afterbuy.\nError: {$e->getMessage()}";
            
            throw new AfterbuyException($message, $e->getCode(), $e);
        }
    }
}
