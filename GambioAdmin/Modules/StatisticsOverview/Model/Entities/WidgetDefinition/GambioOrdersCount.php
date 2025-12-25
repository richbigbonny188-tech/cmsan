<?php
/*--------------------------------------------------------------
   GambioOrdersCount.php 2023-06-09
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
 * Class representing a widget showing the order count.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioOrdersCount extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioOrdersCount';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Bestellungen',
        self::LANGUAGE_CODE_ENGLISH => 'Orders',
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
        Connection                $connection,
        NumberFormatter           $numberFormatter
    ) {
        $this->factory         = $factory;
        $this->connection      = $connection;
        $this->numberFormatter = $numberFormatter;
        
        parent::__construct($factory->createId(self::ID),
                            
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForOrders(),
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
        $timespan = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        $orders   = $this->connection->createQueryBuilder()
                        ->select('COUNT(date_purchased) AS orders')
                        ->from('orders')
                        ->where('orders_status NOT IN (:excludedOrderIds)')
                        ->andWhere('date_purchased BETWEEN :startDate AND :endDate')
                        ->setParameters([
                                            'excludedOrderIds' => implode(',', self::EXCLUDED_ORDER_STATUS_IDS),
                                            'startDate'        => $timespan->startDate()
                                                ->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
                                            'endDate'          => $timespan->endDate()
                                                ->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
                                        ])
                        ->executeQuery()
                        ->fetchAllAssociative()[0];
        
        return $this->factory->useData()->useTextData()->createTextData($this->factory->useData()
                                                                            ->useTextData()
                                                                            ->createValue($this->numberFormatter->format((int)($orders['orders']
                                                                                                                               ??
                                                                                                                               0))));
    }
}
