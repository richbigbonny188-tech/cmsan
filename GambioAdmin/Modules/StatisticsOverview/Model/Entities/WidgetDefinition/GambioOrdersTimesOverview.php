<?php
/*--------------------------------------------------------------
   GambioOrdersTimesOverview.php 2023-06-09
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

/**
 * Class representing a widget showing the orders grouped by their creation hours.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioOrdersTimesOverview extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioOrdersTimesOverview';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Bestellzeiten',
        self::LANGUAGE_CODE_ENGLISH => 'Order Hours',
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
                            $factory->useCategories()->createForOrders(),
                            $factory->useVisualizations()->createBarChart(),
                            $factory->useOptions()->createOptions($factory->useOptions()
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
        $parameters = [
            'excludedOrderIds' => implode(',', self::EXCLUDED_ORDER_STATUS_IDS),
        ];
        
        $ordersHours = $this->connection->createQueryBuilder()
            ->select([
                         'HOUR(date_purchased) AS hour',
                         'COUNT(customers_id) AS orders',
                     ])
            ->from('orders')
            ->where('orders_status NOT IN (:excludedOrderIds)')
            ->groupBy('hour')
            ->orderBy('hour')
            ->setParameters($parameters)
            ->executeQuery()
            ->fetchAllAssociative();
        
        $data = [];
        for ($dataHour = 0; $dataHour < 24; $dataHour++) {
            $data[$dataHour] = [
                'hour'   => str_pad((string)$dataHour, 2, '0', STR_PAD_LEFT),
                'orders' => 0,
            ];
        }
        foreach ($ordersHours as $ordersHour) {
            $data[$ordersHour['hour']]['orders'] = (int)$ordersHour['orders'];
        }
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems(...
                                                                              array_map(function ($ordersHour) {
                                                                                  return $this->factory->useData()
                                                                                      ->useMapData()
                                                                                      ->createItem($this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemTitle($ordersHour['hour']),
                                                                                                   $this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemValue((int)$ordersHour['orders']));
                                                                              },
                                                                                  $data)));
    }
}