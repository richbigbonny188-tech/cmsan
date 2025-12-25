<?php
/*--------------------------------------------------------------
   DashboardStatisticsReader.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics\App\Data;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\DashboardStatistics\Model\Entities\Timespan;

class DashboardStatisticsReader
{
    private const EXCLUDED_ORDER_STATUS_IDS = [99];
    
    /**
     * @var Connection
     */
    private $connection;
    
    
    /**
     * Constructor.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    
    /**
     * Return orders for provided time span.
     *
     * @throws Exception
     */
    public function ordersByTimespan(Timespan $timespan): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        
        $ordersTableAlias    = 'o';
        $purchaseDateKeyName = "$ordersTableAlias." . '`date_purchased`';
        $orderStatusKeyName  = "$ordersTableAlias." . '`orders_status`';
        
        $selects = [
            "DATE($purchaseDateKeyName) AS orders_date",
            "COUNT($purchaseDateKeyName) AS orders_count",
        ];
        
        return $queryBuilder->select($selects)
            ->from('orders', $ordersTableAlias)
            ->andWhere("$purchaseDateKeyName BETWEEN '{$timespan->startDate()->format('Y-m-d H:i:s')}' AND '{$timespan->endDate()->format('Y-m-d H:i:s')}'")
            ->andWhere($orderStatusKeyName . ' NOT IN (' . implode(',', self::EXCLUDED_ORDER_STATUS_IDS) . ')')
            ->groupBy("orders_date")
            ->orderBy("orders_date")
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * Return sales for provided time span.
     *
     * @throws Exception
     */
    public function salesByTimespan(Timespan $timespan): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        
        $orders = $queryBuilder->select('DATE(o.`date_purchased`) as `purchased_date`, SUM(ROUND(ot.`value`/o.`currency_value`, 2)) AS `sales`')
            ->from('orders', 'o')
            ->from('orders_total', 'ot')
            ->where('o.`orders_id` = ot.`orders_id`')
            ->andWhere('o.`orders_status` NOT IN (' . implode(',', self::EXCLUDED_ORDER_STATUS_IDS) . ')')
            ->andWhere('ot.`class` = "ot_total"')
            ->andWhere("o.`date_purchased` BETWEEN '{$timespan->startDate()->format('Y-m-d H:i:s')}' AND '{$timespan->endDate()->format('Y-m-d H:i:s')}'")
            ->groupBy('purchased_date')
            ->orderBy('purchased_date')
            ->executeQuery()
            ->fetchAllAssociative();
        
        $taxes = $queryBuilder->select('DATE(o.`date_purchased`) AS `purchased_date`, SUM(ROUND(ot.`value`/o.`currency_value`, 2)) AS `taxes`')
            ->from('orders', 'o')
            ->from('orders_total', 'ot')
            ->where('o.`orders_id` = ot.`orders_id`')
            ->andWhere('o.`orders_status` NOT IN (' . implode(',', self::EXCLUDED_ORDER_STATUS_IDS) . ')')
            ->andWhere('ot.`class` = "ot_tax"')
            ->andWhere("o.`date_purchased` BETWEEN '{$timespan->startDate()->format('Y-m-d H:i:s')}' AND '{$timespan->endDate()->format('Y-m-d H:i:s')}'")
            ->groupBy('purchased_date')
            ->orderBy('purchased_date')
            ->executeQuery()
            ->fetchAllAssociative();
        
        return [
            'orders' => $orders,
            'taxes'  => $taxes,
        ];
    }
    
    
    /**
     * Return visitors for provided time span.
     *
     * @throws Exception
     */
    public function visitorsByTimespan(Timespan $timespan): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        
        return $queryBuilder->select('SUM(`gm_counter_visits_total`) AS `amount`, DATE(`gm_counter_date`) as `date`')
            ->from('gm_counter_visits')
            ->where("`gm_counter_date` BETWEEN '" . $timespan->startDate()->format('Y-m-d H:i:s') . "' AND '"
                    . $timespan->endDate()->format('Y-m-d H:i:s') . "'")
            ->groupBy('date')
            ->orderBy('date')
            ->executeQuery()
            ->fetchAllAssociative();
    }
    
    
    /**
     * Return conversion rates for provided time span.
     *
     * @throws Exception
     */
    public function conversionRatesByTimespan(Timespan $timespan): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $orders       = $queryBuilder->select('DATE(`date_purchased`) AS `purchased_date`, COUNT(*) AS `purchases`')
            ->from('orders')
            ->where('`orders_status` NOT IN (' . implode(',', self::EXCLUDED_ORDER_STATUS_IDS) . ')')
            ->andWhere("`date_purchased` BETWEEN '{$timespan->startDate()->format('Y-m-d H:i:s')}' AND '{$timespan->endDate()->format('Y-m-d H:i:s')}'")
            ->groupBy('purchased_date')
            ->orderBy('purchased_date')
            ->executeQuery()
            ->fetchAllAssociative();
        
        $queryBuilder = $this->connection->createQueryBuilder();
        
        $visitors = $queryBuilder->select('DATE(`gm_counter_date`) AS `visitor_date`, `gm_counter_visits_total` AS `visitors`')
            ->from('gm_counter_visits')
            ->andWhere("`gm_counter_date` BETWEEN '{$timespan->startDate()->format('Y-m-d H:i:s')}' AND '{$timespan->endDate()->format('Y-m-d H:i:s')}'")
            ->orderBy('visitor_date')
            ->executeQuery()
            ->fetchAllAssociative();
        
        return [
            'orders'   => $orders,
            'visitors' => $visitors,
        ];
    }
}