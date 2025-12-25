<?php
/* --------------------------------------------------------------
   OrderStatusHistoryReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderStatus\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Exceptions\OrderPaidStatusNotFoundException;
use GXModules\Gambio\Afterbuy\OrderStatus\App\Exceptions\OrderStatusDataCorruptedException;

/**
 * Class OrderStatusHistoryReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderStatus\App\Data
 */
class OrderStatusHistoryReader
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    /**
     * @var array
     */
    private array $orderStatusHistoryCache = [];
    
    
    /**
     * OrderStatusHistoryReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Fetches the order status history for the given order id.
     * The entries are ordered by the field 'date_added' descending.
     *
     * There are two cases where this method might throw an exception.
     * The obvious case is if the given order id is not associated with any order, and therefore
     * has no entries in the history table. An `OrderPaidStatusNotFoundException` exception is thrown in this case.
     *
     * In the unlikely event that the database connection is faulty, an `OrderStatusDataCorruptedException` is
     * thrown, but this is an edge case which usually should never happen.
     *
     * @param OrderId $orderId
     *
     * @return array
     * @throws OrderPaidStatusNotFoundException
     * @throws OrderStatusDataCorruptedException
     * @throws Exception
     */
    public function fetchHistoryFor(OrderId $orderId): array
    {
        $oId = $orderId->orderId();
        if (array_key_exists($oId, $this->orderStatusHistoryCache)) {
            return $this->orderStatusHistoryCache[$oId];
        }
        
        $qb    = $this->connection->createQueryBuilder();
        $where = $qb->expr()->eq('orders_id', $oId);
        
        try {
            $statement = $qb->select('orders_status_id')
                ->from('orders_status_history')
                ->where($where)
                ->orderBy('date_added', 'desc')
                ->executeQuery();
        } catch (Exception $e) {
            $message = "Failed to fetch order status history data with order id '$oId' due to a database error.";
            $message .= "\nError: {$e->getMessage()}";
            throw new OrderStatusDataCorruptedException($message, 500, $e);
        }
        
        $result = $statement->fetchAllAssociative();
        if (empty($result)) {
            $message = "No order status history entries found with order id '$oId'.";
            throw new OrderPaidStatusNotFoundException($message);
        }
        $cb          = static fn(array $data): string => $data['orders_status_id'];
        $historyData = array_map($cb, $result);
        
        $this->orderStatusHistoryCache[$oId] = $historyData;
        
        return $historyData;
    }
}