<?php
/*--------------------------------------------------------------
   GambioConversionRateCount.php 2023-06-09
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
use NumberFormatter;

/**
 * Class representing a widget showing the conversion rate as count.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioConversionRateCount extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioConversionRateCount';
    
    /**
     * Name.
     */
    private const WIDGET_NAME_VALUE = 'Conversion Rate';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => self::WIDGET_NAME_VALUE,
        self::LANGUAGE_CODE_ENGLISH => self::WIDGET_NAME_VALUE,
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
     * Number formatter.
     *
     * @var NumberFormatter
     */
    private $numberFormatter;
    
    
    /**
     * @inheritDoc
     */
    public function __construct(
        StatisticsOverviewFactory $factory,
        Connection $connection,
        NumberFormatter $numberFormatter
    ) {
        $this->factory         = $factory;
        $this->connection      = $connection;
        $this->numberFormatter = $numberFormatter;
        
        parent::__construct($factory->createId(self::ID),
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForCustomers(),
                            $factory->useVisualizations()->createText(),
                            $factory->useOptions()->createOptions($factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createTimespanDropdownIncludingToday($factory),
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
        $conversionValues = [];
        $timespan         = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        $parameters       = [
            'excludedOrderIds' => implode(',', self::EXCLUDED_ORDER_STATUS_IDS),
            'startDate'        => $timespan->startDate()->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
            'endDate'          => $timespan->endDate()->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
        ];
        
        $orders   = $this->connection->createQueryBuilder()
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
                
                $hasOrders          = true;
                $conversionValues[] = $orderCount / $visitorCount;
            }
            
            if (!$hasOrders) {
                $conversionValues[] = 0.0;
            }
        }
        
        $conversionRate = array_sum($conversionValues) / sizeof($conversionValues);
        
        return $this->factory->useData()->useTextData()->createTextData($this->factory->useData()
                                                                            ->useTextData()
                                                                            ->createValue($this->numberFormatter->format(is_nan($conversionRate) ? 0 : $conversionRate)));
    }
}