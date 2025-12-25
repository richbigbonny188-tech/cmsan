<?php
/*--------------------------------------------------------------
   CustomerStatisticsReader.php 2023-06-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Statistics\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Model\ValueObjects\CustomerId;
use Gambio\Admin\Modules\Customer\Submodules\Statistics\Services\Exceptions\CustomerNotFoundException;

/**
 * Class CustomerStatisticsReader
 *
 * @package Gambio\Admin\Modules\CustomerStatistics\App\Data
 */
class CustomerStatisticsReader
{
    /**
     * @var Connection
     */
    private Connection $connection;
    
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * @param CustomerId $customerId
     *
     * @return array
     *
     * @throws CustomerNotFoundException
     * @throws DBALException
     */
    public function getCustomerStatistics(CustomerId $customerId): array
    {
        $query = <<<SQL
        SELECT
               ROUND(SUM(`value`), 2) AS order_total,
               COUNT(`orders_id`) AS order_count,
               (SELECT CAST( `customers_id` AS UNSIGNED ) from `customers` WHERE customers_id = :customer_id) AS customer_exists
        FROM `orders_total`
        WHERE
              `orders_id` IN (
                  SELECT `orders_id`
                  FROM `orders`
                  WHERE
                        customers_id = :customer_id AND
                        orders_status != '99' -- 99 is a canceled order
                  )
              AND `class` = "ot_total"
        SQL;
        
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':customer_id', $customerId->value());
        
        try {
            $result = $stmt->executeQuery()->fetchAssociative();
            
            if (!$result || $result['customer_exists'] === null) {
    
                throw CustomerNotFoundException::customerNotFound($customerId);
            }
            
            return [
                'order_total' => $result['order_total'] ?? '0',
                'order_count' => $result['order_count'],
            ];
        } catch (DBALException $exception) {
            throw CustomerNotFoundException::becauseOfException($exception);
        }
    }
}