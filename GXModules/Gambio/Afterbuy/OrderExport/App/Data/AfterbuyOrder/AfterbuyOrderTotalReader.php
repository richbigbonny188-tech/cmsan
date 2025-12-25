<?php
/* --------------------------------------------------------------
   AfterbuyOrderTotalReader.php 2023-06-09
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
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyOrderTotalDataNotFoundException;

/**
 * Class AfterbuyOrderTotalReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderTotalReader
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    /**
     * @var array
     */
    private array $orderTotalCache = [];
    
    
    /**
     * AfterbuyOrderTotalReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Fetches order total data by the given order id.
     * In memory caches the result, so subsequent calls with the same $orderId will return the cached data.
     *
     * If everything succeeds, the returned array contains at least "ot_subtotal" and "ot_total" (if the whole
     * order was free). Most of the time, "ot_shipping" and "ot_tax" are also available. There may be some additional
     * elements, e.g. "ot_cod_fee" if the orders payment was "cash on delivery".
     *
     * @param int $orderId
     *
     * @return array
     * @throws AfterbuyOrderTotalDataNotFoundException
     * @throws Exception
     */
    public function fetchOrderTotalsByOrderId(int $orderId): array
    {
        if (array_key_exists($orderId, $this->orderTotalCache)) {
            return $this->orderTotalCache[$orderId];
        }
        
        $qb    = $this->connection->createQueryBuilder();
        $where = $qb->expr()->eq('orders_id', $orderId);
        
        try {
            $statement = $qb->select('class, value')->from('orders_total')->where($where)->executeQuery();
        } catch (Exception $e) {
            $message = "Unable to fetch order total data with order id '$orderId' due to a database error: {$e->getMessage()}";
            throw new AfterbuyOrderTotalDataNotFoundException($message);
        }
        
        $result = $statement->fetchAllAssociative();
        if (empty($result)) {
            $message = "No order total data was found with order id '$orderId'";
            throw new AfterbuyOrderTotalDataNotFoundException($message);
        }
        
        $data = [];
        foreach ($result as $dataset) {
            $data[$dataset['class']] = $dataset['value'];
        }
        $this->orderTotalCache[$orderId] = $data;
        
        return $this->orderTotalCache[$orderId];
    }
}