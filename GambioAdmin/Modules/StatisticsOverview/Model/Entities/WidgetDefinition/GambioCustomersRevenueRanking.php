<?php
/*--------------------------------------------------------------
   GambioCustomersRanking.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\MaxEntriesOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\TimespanOptionFactory;

/**
 * Class representing a widget showing the customer's revenue ranking.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioCustomersRevenueRanking extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioCustomersRevenueRanking';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Umsatz pro Kunde',
        self::LANGUAGE_CODE_ENGLISH => 'Revenue per customer',
    ];
    
    /**
     * Factory.
     *
     * @var StatisticsOverviewFactory
     */
    private $factory;
    
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;
    
    
    /**
     * @inheritDoc
     */
    public function __construct(
        StatisticsOverviewFactory $factory,
        Connection                $connection
    ) {
        $this->factory    = $factory;
        $this->connection = $connection;
        
        parent::__construct($factory->createId(self::ID),
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForCustomers(),
                            $factory->useVisualizations()->createBarChart(),
                            $factory->useOptions()->createOptions($factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createTimespanDropdown($factory),
                                                                  $factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createMaxEntriesDropdown($factory),
                                                                  $factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createSortOrderNumber($factory),
                                                                  $factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createVisibilityCheckbox($factory)));
    }
    
    
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function data(WidgetOptions $options): WidgetData
    {
        $timespan = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        
        $customersRevenues = $this->connection->createQueryBuilder()
            ->select([
                         'orders.customers_name AS customer',
                         'SUM(orders_products.final_price) AS revenue',
                     ])
            ->from('orders', 'orders')
            ->join('orders', 'orders_products', 'orders_products', 'orders.orders_id = orders_products.orders_id')
            ->where('date_purchased BETWEEN :startDate AND :endDate')
            ->groupBy('orders.customers_name')
            ->orderBy('revenue', 'DESC')
            ->setParameters([
                                'startDate' => $timespan->startDate()->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
                                'endDate'   => $timespan->endDate()->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
                            ])
            ->setMaxResults((int)$options->getById(MaxEntriesOptionFactory::ID)->value())
            ->executeQuery()
            ->fetchAllAssociative();
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems(...
                                                                              array_map(function ($customer) {
                                                                                  return $this->factory->useData()
                                                                                      ->useMapData()
                                                                                      ->createItem($this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemTitle((string)$customer['customer']),
                                                                                                   $this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemValue((float)$customer['revenue']));
                                                                              },
                                                                                  $customersRevenues)));
    }
    
}