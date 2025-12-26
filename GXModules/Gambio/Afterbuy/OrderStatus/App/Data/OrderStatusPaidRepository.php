<?php
/* --------------------------------------------------------------
   OrderStatusPaidRepository.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderStatus\App\Data;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\AfterbuyLogger;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Exceptions\OrderPaidStatusNotFoundException;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Exceptions\OrderStatusDataCorruptedException;
use GXModules\Gambio\Afterbuy\OrderStatus\Exceptions\AfterbuyOrderStatusPaidException;
use GXModules\Gambio\Afterbuy\OrderStatus\Model\AfterbuyOrderStatus;

/**
 * Class OrderStatusPaidRepository
 *
 * @package GXModules\Gambio\Afterbuy\OrderStatus\App\Data
 */
class OrderStatusPaidRepository
{
    /**
     * @var OrderStatusPaidReader
     */
    private OrderStatusPaidReader $paidReader;
    
    
    /**
     * @var OrderStatusHistoryReader
     */
    private OrderStatusHistoryReader $historyReader;
    
    
    /**
     * @var AfterbuyLogger
     */
    private AfterbuyLogger $logger;
    
    
    /**
     * OrderStatusPaidRepository constructor.
     *
     * @param OrderStatusPaidReader    $paidReader
     * @param OrderStatusHistoryReader $historyReader
     * @param AfterbuyLogger           $logger
     */
    public function __construct(
        OrderStatusPaidReader    $paidReader,
        OrderStatusHistoryReader $historyReader,
        AfterbuyLogger           $logger
    ) {
        $this->paidReader    = $paidReader;
        $this->historyReader = $historyReader;
        $this->logger        = $logger;
    }
    
    
    /**
     * Returns the afterbuy paid status.
     *
     * The paid status contains information about the Afterbuy SetPay flag, more precise it
     * has accessor methods `::isXYZ` to determine the actual Afterbuy SetPay value.
     *
     * @param OrderId $orderId
     *
     * @return AfterbuyOrderStatus
     * @throws AfterbuyOrderStatusPaidException
     * @see AfterbuyPaidStatusBackup
     */
    public function getAfterbuyPaidStatus(OrderId $orderId): AfterbuyOrderStatus
    {
        try {
            $paidStatus = $this->paidReader->getPaidOrderStatus();
            if (in_array('-1', $paidStatus, true) && count($paidStatus) === 1) {
                return $this->getStatus($orderId, $paidStatus, []);
            }
            
            $unpaidStatus  = $this->paidReader->getUnpaidOrderStatus();
            $unknownStatus = in_array('-1', $unpaidStatus, true)
                             && count($unpaidStatus) === 1 ? [] : $this->paidReader->getUnknownOrderStatus();
            
            return $this->getStatus($orderId, $paidStatus, $unpaidStatus);
        } catch (OrderPaidStatusNotFoundException $e) {
            $message = "Order status history not found. {$e->getMessage()}";
            throw new AfterbuyOrderStatusPaidException($message);
        } catch (OrderStatusDataCorruptedException $e) {
            $paidStatus    = $paidStatus ?? null;
            $unpaidStatus  = $unpaidStatus ?? null;
            $unknownStatus = $unknownStatus ?? null;
            
            $this->handleCorruptedDataError($paidStatus, $unpaidStatus, $unknownStatus, $e);
        }
    }
    
    
    /**
     * Returns an instance of `AfterbuyOrderStatus`, containing information if the order
     * is marked as 'paid', 'unpaid' or 'unknown'. These infos are accessible via the `::isXYZ` methods.
     *
     * @param OrderId $orderId
     * @param array   $paidStatus
     * @param array   $unpaidStatus
     *
     * @return AfterbuyOrderStatus
     * @throws OrderPaidStatusNotFoundException
     * @throws OrderStatusDataCorruptedException
     */
    private function getStatus(
        OrderId $orderId,
        array   $paidStatus,
        array   $unpaidStatus
    ): AfterbuyOrderStatus {
        if (in_array('-1', $paidStatus, true) && count($paidStatus) === 1) {
            return AfterbuyOrderStatus::createPaid();
        }
        
        $historyData = $this->historyReader->fetchHistoryFor($orderId);
        foreach ($paidStatus as $paidStatusId) {
            if (in_array($paidStatusId, $historyData, true)) {
                return AfterbuyOrderStatus::createPaid();
            }
        }
        
        if (in_array('-1', $unpaidStatus, true) && count($unpaidStatus) === 1) {
            return AfterbuyOrderStatus::createUnpaid();
        }
        foreach ($unpaidStatus as $unpaidStatusId) {
            if (in_array($unpaidStatusId, $historyData, true)) {
                return AfterbuyOrderStatus::createUnpaid();
            }
        }
        
        return AfterbuyOrderStatus::createUnknown();
    }
    
    
    /**
     * Handles the exception for corrupted order status history data.
     *
     * This method collects as much information as possible and throws an `AfterbuyPaidException`.
     * Additionally, an error is logged, containing all the details.
     *
     * @param array|null                        $paidStatus
     * @param array|null                        $unpaidStatus
     * @param array|null                        $unknownStatus
     * @param OrderStatusDataCorruptedException $e
     *
     * @throws AfterbuyOrderStatusPaidException
     */
    private function handleCorruptedDataError(
        ?array                            $paidStatus,
        ?array                            $unpaidStatus,
        ?array                            $unknownStatus,
        OrderStatusDataCorruptedException $e
    ): void {
        $message = "Can not get Afterbuy paid status due to an internal database error\nError: {$e->getMessage()}";
        $context = [
            'paidStatus'    => $paidStatus ?? null,
            'unpaidStatus'  => $unpaidStatus ?? null,
            'unknownStatus' => $unknownStatus ?? null,
            'exception'     => [
                $e->getMessage(),
                $e->getCode(),
                $e->getFile(),
                $e->getLine(),
                $e->getTrace(),
                $e->getPrevious(),
            ],
        ];
        $this->logger->error($message, $context);
        
        throw new AfterbuyOrderStatusPaidException($message, $e->getCode(), $e);
    }
}