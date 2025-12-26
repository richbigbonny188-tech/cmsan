<?php
/*--------------------------------------------------------------
   GambioRevenueTrend.php 2023-06-09
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
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\TimespanOptionFactory;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class representing a widget showing the revenue as trend.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioRevenueTrend extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioRevenueTrend';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Umsatz (Trend)',
        self::LANGUAGE_CODE_ENGLISH => 'Turnover (Trend)',
    ];
    
    /**
     * Multilingual category titles.
     */
    private const CATEGORY_TITLE = [
        self::LANGUAGE_CODE_GERMAN  => 'Umsatz',
        self::LANGUAGE_CODE_ENGLISH => 'Turnover',
    ];
    
    /**
     * Factory.
     *
     * @var StatisticsOverviewFactory
     */
    private $factory;
    
    /**
     * User's preferences.
     *
     * @var UserPreferences
     */
    private $userPreferences;
    
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
        UserPreferences           $userPreferences,
        Connection                $connection
    ) {
        $this->factory         = $factory;
        $this->userPreferences = $userPreferences;
        $this->connection      = $connection;
        
        parent::__construct($factory->createId(self::ID),
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForOrders(),
                            $factory->useVisualizations()->createAreaChart(),
                            $factory->useOptions()->createOptions($factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createTimespanDropdown($factory),
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
        $categories    = [];
        $values        = [];
        $categoryTitle = self::CATEGORY_TITLE[$this->userPreferences->languageId()
                                              === self::LANGUAGE_ID_GERMAN ? self::LANGUAGE_CODE_GERMAN : self::LANGUAGE_CODE_ENGLISH];
        $timespan      = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        $parameters    = [
            'excludedOrderIds' => implode(',', self::EXCLUDED_ORDER_STATUS_IDS),
            'startDate'        => $timespan->startDate()->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
            'endDate'          => $timespan->endDate()->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
        ];
        $revenues      = $this->connection->createQueryBuilder()
            ->select([
                         'SUM(ROUND(orders_total.value / orders.currency_value, 2)) AS sales',
                         'UNIX_TIMESTAMP(DATE(orders.date_purchased)) AS date',
                     ])
            ->from('orders')
            ->join('orders',
                   'orders_total',
                   'orders_total',
                   'orders.orders_id = orders_total.orders_id')
            ->where('orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('orders_total.class="ot_total"')
            ->andWhere('orders.date_purchased BETWEEN :startDate AND :endDate')
            ->groupBy('date')
            ->orderBy('date')
            ->setParameters($parameters)
            ->executeQuery()
            ->fetchAllAssociative();
        
        $taxesRaw = $this->connection->createQueryBuilder()
            ->select([
                         'UNIX_TIMESTAMP(DATE(orders.date_purchased)) AS purchased_date',
                         'SUM(ROUND(orders_total.value / orders.currency_value, 2)) AS taxes',
                     ])
            ->from('orders')
            ->join('orders',
                   'orders_total',
                   'orders_total',
                   'orders.orders_id = orders_total.orders_id')
            ->where('orders_total.class="ot_tax"')
            ->andWhere('orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('orders.date_purchased BETWEEN :startDate AND :endDate')
            ->setParameters($parameters)
            ->groupBy('purchased_date')
            ->orderBy('purchased_date')
            ->executeQuery()
            ->fetchAllAssociative();
        
        $taxes = [];
        
        foreach ($taxesRaw as ['purchased_date' => $date, 'taxes' => $tax]) {
            $taxes[$date] = (float)$tax;
        }
        
        foreach ($revenues as ['date' => $date, 'sales' => $sales]) {
            $categories[] = $this->factory->useData()->useSerialData()->createCategory((string)$date);
            $values[]     = $this->factory->useData()
                ->useSerialData()
                ->createItemValue(isset($taxes[$date]) ? (float)$sales - (float)$taxes[$date] : (float)$sales);
        }
        
        return $this->factory->useData()->useSerialData()->createSerialData($this->factory->useData()
                                                                                ->useSerialData()
                                                                                ->createCategories(...$categories),
                                                                            $this->factory->useData()
                                                                                ->useSerialData()
                                                                                ->createItems($this->factory->useData()
                                                                                                  ->useSerialData()
                                                                                                  ->createItem($this->factory->useData()
                                                                                                                   ->useSerialData()
                                                                                                                   ->createItemTitle($categoryTitle),
                                                                                                               $this->factory->useData()
                                                                                                                   ->useSerialData()
                                                                                                                   ->createItemValues(...
                                                                                                                       $values))));
    }
}
