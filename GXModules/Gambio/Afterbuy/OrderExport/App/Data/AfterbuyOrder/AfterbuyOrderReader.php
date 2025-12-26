<?php
/* --------------------------------------------------------------
   AfterbuyOrderReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\App\AfterbuyLogger;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\Model\OrderIds;

/**
 * Class AfterbuyOrderReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderReader
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    /**
     * @var AfterbuyLogger
     */
    private AfterbuyLogger $logger;
    
    /**
     * @var array
     */
    private array $dataCache = [];
    
    
    /**
     * AfterbuyOrderReader constructor.
     *
     * @param Connection     $connection
     * @param AfterbuyLogger $logger
     */
    public function __construct(Connection $connection, AfterbuyLogger $logger)
    {
        $this->connection = $connection;
        $this->logger     = $logger;
    }
    
    
    /**
     * Returns a list of order data for the given order ids.
     *
     * Internally caches the result for the given order ids in memory, so subsequent calls
     * with the same order ids return the cached value.
     *
     * @param OrderIds $orderIds
     *
     * @return array
     */
    public function getOrdersData(OrderIds $orderIds): array
    {
        $orderIdsHash = $orderIds->hash();
        if (array_key_exists($orderIdsHash, $this->dataCache)) {
            return $this->dataCache[$orderIdsHash];
        }
        
        $qb = $this->connection->createQueryBuilder();
        
        $select     = $this->getSelectFields();
        $orderIdsIn = $qb->expr()->in('o.orders_id', $orderIds->orderIdsWithComma());
        
        try {
            $result = $qb->select($select)
                ->from('orders', 'o')
                ->leftJoin('o', 'afterbuy_orders', 'ao', 'ao.order_id = o.orders_id')
                ->where($orderIdsIn)
                ->executeQuery()
                ->fetchAllAssociative();
            
            $this->dataCache[$orderIdsHash] = $result;
            
            return $this->dataCache[$orderIdsHash];
        } catch (Exception $e) {
            $message = "Database error when fetching order information.\nError: {$e->getMessage()}";
            $context = [
                'selects'  => $select,
                'orderIds' => $orderIds->orderIdsWithComma(),
            ];
            $this->logger->error($message, $context);
            
            return [];
        }
    }
    
    
    /**
     * Returns the initial order export datetime string or null if the order was not send to afterbuy yet.
     *
     * @param OrderId $orderId
     *
     * @return string|null
     * @throws Exception
     */
    public function getInitialExportDatetime(OrderId $orderId): ?string
    {
        $key = 'created_at';
        $qb  = $this->connection->createQueryBuilder();
        
        $where = $qb->expr()->eq('order_id', $orderId->orderId());
        try {
            $statement = $qb->select($key)->from('afterbuy_orders')->where($where)->executeQuery();
        } catch (Exception $e) {
            $message = "Database error while trying to get the initial Afterbuy order export timestamp\nError: {$e->getMessage()}";
            $context = [
                'orderId' => $orderId->orderId(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ];
            $this->logger->warning($message, $context);
            
            return null;
        }
        
        $result = $statement->fetchAssociative();
        if ($result === false || !is_array($result)) {
            return null;
        }
        
        return $result[$key] ?? null;
    }
    
    
    /**
     * @return array
     */
    private function getSelectFields(): array
    {
        return AfterbuyOrderReaderFields::AFTERBUY_ORDER_FIELDS;
    }
}
