<?php
/* --------------------------------------------------------------
   AfterbuyOrderInvoiceReader.php 2023-10-18
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

/**
 * Class AfterbuyOrderInvoiceReader
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder
 */
class AfterbuyOrderInvoiceReader
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * AfterbuyOrderInvoiceReader constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Fetches invoice data for the given order.
     * Returns an empty array if no invoice data is available.
     *
     * @param int $orderId
     *
     * @return array
     */
    public function fetchInvoiceData(int $orderId): array
    {
        $qb    = $this->connection->createQueryBuilder();
        $where = $qb->expr()->eq('order_id', $orderId);
        
        try {
            $statement = $qb->select('*')->from('invoices')->where($where)->executeQuery();
        } catch (Exception $e) {
            return [];
        }
        
        $result = $statement->fetchAssociative();
        if ($result === false) {
            return [];
        }
        
        return $result;
    }
}