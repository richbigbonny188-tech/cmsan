<?php
/* --------------------------------------------------------------
   AfterbuyCronjobTask.inc.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Doctrine\DBAL\Connection;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderExportService;
use GXModules\Gambio\Afterbuy\OrderExport\Service\AfterbuyOrderXmlApiService;
use GXModules\Gambio\Afterbuy\OrderStatus\Service\AfterbuyCheckPaidStatusService;
use GXModules\Gambio\Afterbuy\OrderTracking\Exceptions\SyncTrackingLinksFailedException;
use GXModules\Gambio\Afterbuy\OrderTracking\Service\AfterbuyOrderTrackingLinkService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class AfterbuyCronjobTask
 *
 * @package GXModules\Gambio\Afterbuy\Admin\Classes
 */
class AfterbuyCronjobTask extends AbstractCronjobTask
{
    /**
     *
     */
    protected const MAX_ORDERS_PER_RUN = 20;
    
    
    /**
     * @param float $cronjobStartAsMicrotime
     *
     * @return Closure
     */
    public function getCallback($cronjobStartAsMicrotime): Closure
    {
        return function () {
            $this->logInfo('AfterbuyCronjobTask::getCallback() called');
            
            $this->logger->lastRun();
            
            if (!$this->moduleIsInstalledAndActive()) {
                return true;
            }
            
            if ($this->dependencies->getDependencies()['active']) {
                $trackingLinkService = LegacyDependencyContainer::getInstance()
                    ->get(AfterbuyOrderTrackingLinkService::class);
                $orderExportService  = LegacyDependencyContainer::getInstance()->get(AfterbuyOrderExportService::class);
                
                $this->sendOrders($trackingLinkService);
                
                $lastTrackingSyncTime = $orderExportService->getLastTrackingSyncTime();
                $this->syncTrackingLinks($trackingLinkService, $lastTrackingSyncTime);
                $orderExportService->updateLastTrackingSyncTime();
            }
            
            $this->logger->log(['CronjobTask finished' => date('c')]);
            $this->logger->lastSuccess();
            
            return true;
        };
    }
    
    
    /**
     * @return void
     */
    private function sendOrders(): void
    {
        try {
            $paidService           = LegacyDependencyContainer::getInstance()
                ->get(AfterbuyCheckPaidStatusService::class);
            $orderExportXmlService = LegacyDependencyContainer::getInstance()->get(AfterbuyOrderXmlApiService::class);
            $orderExportService    = LegacyDependencyContainer::getInstance()->get(AfterbuyOrderExportService::class);
            $connection            = LegacyDependencyContainer::getInstance()->get(Connection::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            $message = "Sending orders to Afterbuy failed.\nError: {$e->getMessage()}";
            $this->logError($message);
            
            return;
        }
        
        /** @var GambioAfterbuyConfigurationStorage $configurationStorage */
        $configurationStorage = $this->dependencies->getDependencies()['ConfigurationStorage'];
        
        $updatedPaidOrderIds     = [];
        $transmittedPaidOrderIds = [];
        $unpaidOrderIds          = [];
        $unprocessedOrderIds     = [];
        $errors                  = [];
        
        $orders = $this->getOrders($configurationStorage, $connection, $orderExportService);
        foreach ($orders as $order) {
            $orderIdString   = $order['orders_id'];
            $orderIdInt      = (int)$orderIdString;
            $orderId         = new OrderId($orderIdInt);
            $afterbuySuccess = $order['afterbuy_success'];
            $isTransmitted   = $afterbuySuccess === '1';
            
            try {
                $orderPaid = $paidService->getPaidStatus($orderId);
                if ($orderPaid->isPaid()) {
                    if ($isTransmitted) {
                        $orderExportXmlService->updateOrderViaXmlApi($orderId);
                        $updatedPaidOrderIds[] = $orderIdString;
                    } else {
                        $orderSender = MainFactory::create(AfterbuyOrderSender::class, $orderIdString);
                        $orderSender->processOrder();
                        $transmittedPaidOrderIds[] = $orderIdString;
                    }
                } elseif (!$isTransmitted && $orderPaid->isUnpaid()) {
                    $orderSender = MainFactory::create(AfterbuyOrderSender::class, $orderIdString);
                    $orderSender->processOrder();
                    $unpaidOrderIds[] = $orderIdString;
                } else {
                    $unprocessedOrderIds[] = $orderIdString;
                }
            } catch (Exception $e) {
                $errors[$orderIdInt] = $e->getMessage();
            }
        }
        $this->logSummary($updatedPaidOrderIds,
                          $transmittedPaidOrderIds,
                          $unpaidOrderIds,
                          $unprocessedOrderIds,
                          $errors);
    }
    
    
    /**
     * @param array $updatedPaidOrderIds
     * @param array $transmittedPaidOrderIds
     * @param array $unpaidOrderIds
     * @param array $unprocessedOrderIds
     * @param array $errors
     *
     * @return void
     */
    private function logSummary(
        array $updatedPaidOrderIds,
        array $transmittedPaidOrderIds,
        array $unpaidOrderIds,
        array $unprocessedOrderIds,
        array $errors
    ): void {
        $resultLog = "Cronjob summary:\n";
        if (!empty($updatedPaidOrderIds)) {
            $resultLog .= sprintf("Processed paid order ids: %s.\n", implode(', ', $updatedPaidOrderIds));
        }
        if (!empty($transmittedPaidOrderIds)) {
            $resultLog .= sprintf("Updated paid order ids: %s.\n", implode(', ', $transmittedPaidOrderIds));
        }
        if (!empty($unpaidOrderIds)) {
            $resultLog .= sprintf("Processed unpaid order ids: %s.", implode(', ', $unpaidOrderIds));
        }
        if (!empty($unprocessedOrderIds)) {
            $resultLog .= sprintf("Unprocessed order ids: %s.", implode(', ', $unprocessedOrderIds));
        }
        
        foreach ($errors as $errorOrderId => $errorMessage) {
            $resultLog .= sprintf("Error: %d - %s\n", $errorOrderId, $errorMessage);
        }
        $this->logInfo($resultLog);
    }
    
    
    /**
     * @param GambioAfterbuyConfigurationStorage $configurationStorage
     * @param Connection                         $connection
     * @param AfterbuyOrderExportService         $orderExportService
     *
     * @return array<array{orders_id: string, afterbuy_success: string}>
     * @throws Exception
     */
    private function getOrders(
        GambioAfterbuyConfigurationStorage $configurationStorage,
        Connection                         $connection,
        AfterbuyOrderExportService         $orderExportService
    ): array {
        $lastSyncTime      = $orderExportService->getLastTrackingSyncTime();
        $lastSyncTimestamp = (new \DateTime($lastSyncTime))->format('Y-m-d H:i:s');
        $maxOrderPerRun    = (int)$configurationStorage->get('max_orders_per_run');
        $maxOrderPerRun    = $maxOrderPerRun > 0 ? : static::MAX_ORDERS_PER_RUN;
        
        $qb = $connection->createQueryBuilder();
        
        $where = "afterbuy_success = 0 OR last_modified > {$qb->createNamedParameter($lastSyncTimestamp)}";
        try {
            $this->logNotice("Syncing orders modified since {$lastSyncTimestamp}");
            $query     = $qb->select('orders_id, afterbuy_success')
                ->from('orders')
                ->where($where)
                ->orderBy('orders_id', 'desc')
                ->setMaxResults($maxOrderPerRun);
            $statement = $query->executeQuery();
        } catch (\Doctrine\DBAL\Exception $e) {
            $message = "Database error while fetching orders to be send to Afterbuy.\nError: {$e->getMessage()}";
            $this->logNotice($message);
            
            return [];
        }
        $result      = $statement->fetchAllAssociative();
        $resultCount = count($result);
        $this->logInfo("Cron endpoint sending up to $maxOrderPerRun orders, found $resultCount orders.");
        
        return $result;
    }
    
    
    /**
     * @return void
     */
    public function sendOrdersBackup(): void
    {
        /** @var GambioAfterbuyConfigurationStorage $configurationStorage */
        $configurationStorage = $this->dependencies->getDependencies()['ConfigurationStorage'];
        $allAsPaid            = $configurationStorage->get('order_status_paid') === '-1';
        $responseData         = [];
        $headers              = [];
        $maxOrderPerRun       = (int)$configurationStorage->get('max_orders_per_run');
        $maxOrderPerRun       = $maxOrderPerRun > 0 ? : static::MAX_ORDERS_PER_RUN;
        
        $this->logInfo('Cron endpoint sending up to ' . $maxOrderPerRun . ' orders');
        $db                = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $queuedOrdersQuery = $db->select('orders_id')
            ->where('afterbuy_success', '0')
            ->order_by('orders_id DESC')
            ->limit($maxOrderPerRun)
            ->get('orders');
        $queuedOrdersIds   = [];
        foreach ($queuedOrdersQuery->result() as $queuedOrder) {
            $queuedOrdersIds[] = $queuedOrder->orders_id;
        }
        sort($queuedOrdersIds);
        $responseData['processedOrders'] = [];
        $responseData['unpaidOrders']    = [];
        $responseData['errors']          = [];
        foreach ($queuedOrdersIds as $orderId) {
            $afterbuyOrderSender = MainFactory::create('AfterbuyOrderSender', $orderId);
            if ($allAsPaid || $afterbuyOrderSender->orderIsPaid()) {
                try {
                    $afterbuyOrderSender->processOrder();
                    $responseData['processedOrders'][] = $orderId;
                } catch (AfterbuyException $e) {
                    $responseData['errors'][$orderId] = $e->getMessage();
                }
            } else {
                $responseData['unpaidOrders'][] = $orderId;
            }
        }
        
        $resultLog = sprintf("Cron job summary:\nProcessed orders: %s\nUnpaid orders (skipped): %s",
                             implode(', ', $responseData['processedOrders']),
                             implode(', ', $responseData['unpaidOrders']));
        if (!empty($responseData['errors'])) {
            $resultLog .= sprintf("\nOrders with errors in transmission:\n");
            foreach ($responseData['errors'] as $errorOrder => $errorMessage) {
                $resultLog .= sprintf("%d - %s\n", $errorOrder, $errorMessage);
            }
        }
        $this->logInfo($resultLog);
    }
    
    
    /**
     * @param AfterbuyOrderTrackingLinkService $syncService
     * @param string                           $lastTrackingSyncTime
     *
     * @return void
     */
    protected function syncTrackingLinks(
        AfterbuyOrderTrackingLinkService $syncService,
        string                           $lastTrackingSyncTime
    ): void {
        try {
            $syncService->syncTrackingLinks($lastTrackingSyncTime);
        } catch (AfterbuyNotEnabledException|AfterbuyNotInstalledException $e) {
            $message = $this->createMessage('Afterbuy module is not installed or not enabled', $e);
            $this->logInfo($message);
        } catch (AfterbuyResponseException $e) {
            $message = $this->createMessage('Afterbuy API response is invalid', $e);
            $this->logNotice($message);
        } catch (SyncTrackingLinksFailedException $e) {
            $message = $this->createMessage('Tracking link synchronization failed', $e);
            $this->logError($message);
        }
    }
    
    
    /**
     * @param string    $message
     * @param Throwable $t
     *
     * @return string
     */
    protected function createMessage(string $message, Throwable $t): string
    {
        $errorCount = 1;
        $message    = "$message:\nError $errorCount: {$t->getMessage()}";
        
        while ($previous = $t->getPrevious()) {
            $errorCount++;
            $message .= "\nError $errorCount: {$previous->getMessage()}";
        }
        
        return $message;
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function logInfo(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'info']);
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function logError(string $message): void
    {
        $this->logger->logError(['message' => $message, 'level' => 'error']);
    }
    
    
    /**
     * @param string $message
     *
     * @return void
     */
    protected function logNotice(string $message): void
    {
        $this->logger->log(['message' => $message, 'level' => 'notice']);
    }
    
    
    /**
     * @return bool
     */
    protected function moduleIsInstalledAndActive(): bool
    {
        $configurationFinder = $this->dependencies->getDependencies()['ConfigurationFinder'];
        $installedConfig     = (bool)$configurationFinder->get('gm_configuration/MODULE_CENTER_GAMBIOAFTERBUY_INSTALLED');
        $activeConfig        = (bool)$configurationFinder->get('modules/gambio/afterbuy/active');
        
        return $installedConfig && $activeConfig;
    }
}