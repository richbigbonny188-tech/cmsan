<?php
/*--------------------------------------------------------------
   GambioConversionRateTrend.php 2023-06-09
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
 * Class representing a widget showing the conversion rate as trend.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioConversionRateTrend extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioConversionRateTrend';
    
    /**
     * Name.
     */
    private const WIDGET_NAME_VALUE = 'Conversion Rate (Trend)';
    
    /**
     * Multilingual widget titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => self::WIDGET_NAME_VALUE,
        self::LANGUAGE_CODE_ENGLISH => self::WIDGET_NAME_VALUE,
    ];
    
    /**
     * Category name.
     */
    private const CATEGORY_TITLE_VALUE = 'Conversion';
    
    /**
     * Multilingual category titles.
     */
    private const CATEGORY_TITLE = [
        self::LANGUAGE_CODE_GERMAN  => self::CATEGORY_TITLE_VALUE,
        self::LANGUAGE_CODE_ENGLISH => self::CATEGORY_TITLE_VALUE,
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
                            $factory->useCategories()->createForCustomers(),
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
        
        $orders = $this->connection->createQueryBuilder()
            ->select('UNIX_TIMESTAMP(DATE(date_purchased)) AS date, COUNT(*) AS orders')
            ->from('orders')
            ->where('orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('date_purchased BETWEEN :startDate AND :endDate')
            ->groupBy('date')
            ->orderBy('date')
            ->setParameters($parameters)
            ->executeQuery()
            ->fetchAllAssociative();
        
        $visitors = $this->connection->createQueryBuilder()
            ->select('UNIX_TIMESTAMP(DATE(gm_counter_date)) AS date, gm_counter_visits_total AS visitors')
            ->from('gm_counter_visits')
            ->where('gm_counter_date BETWEEN :startDate AND :endDate')
            ->orderBy('date')
            ->setParameters($parameters)
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($visitors as $visitor) {
            $visitorDate  = $visitor['date'];
            $visitorCount = (float)$visitor['visitors'];
            
            $hasOrders = false;
            foreach ($orders as $order) {
                $orderDate  = $order['date'];
                $orderCount = (float)$order['orders'];
                
                if ($visitorDate !== $orderDate) {
                    continue;
                }
                
                $hasOrders = true;
                
                $categories[] = $this->factory->useData()->useSerialData()->createCategory($orderDate);
                $values[]     = $this->factory->useData()->useSerialData()->createItemValue(round(($orderCount
                                                                                                   / $visitorCount)
                                                                                                  * 100));
            }
            
            if (!$hasOrders) {
                $categories[] = $this->factory->useData()->useSerialData()->createCategory($visitorDate);
                $values[]     = $this->factory->useData()->useSerialData()->createItemValue(0);
            }
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
