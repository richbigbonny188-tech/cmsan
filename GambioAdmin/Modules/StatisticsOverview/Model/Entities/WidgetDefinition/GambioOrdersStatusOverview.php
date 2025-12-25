<?php

/*--------------------------------------------------------------
   GambioOrdersStatusOverview.php 2023-06-09
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
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class representing a widget showing the order status.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioOrdersStatusOverview extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioOrdersStatusOverview';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Bestellstatus (Übersicht)',
        self::LANGUAGE_CODE_ENGLISH => 'Orders Status Overview',
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
     * User's preferences.
     *
     * @var UserPreferences
     */
    private $userPreferences;
    
    
    /**
     * @inheritDoc
     */
    public function __construct(
        StatisticsOverviewFactory $factory,
        UserPreferences           $userPreferences,
        Connection                $connection
    ) {
        $this->factory         = $factory;
        $this->connection      = $connection;
        $this->userPreferences = $userPreferences;
        
        parent::__construct($factory->createId(self::ID),
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForOrders(),
                            $factory->useVisualizations()->createPieChart(),
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
        $overview = [];
        
        $timespan = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        
        $ordersStatusOverview = $this->connection->createQueryBuilder()
            ->select([
                         'orders_status.orders_status_name AS status',
                         'COUNT(orders.orders_id) AS orders',
                     ])
            ->from('orders')
            ->leftJoin('orders',
                       'orders_status',
                       'orders_status',
                       'orders.orders_status = orders_status.orders_status_id')
            ->where('orders.orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('orders_status.language_id = :languageId')
            ->andWhere('date_purchased BETWEEN :startDate AND :endDate')
            ->groupBy('orders_status.orders_status_name')
            ->orderBy('orders', 'DESC')
            ->setMaxResults((int)$options->getById(MaxEntriesOptionFactory::ID)->value())
            ->setParameters([
                                'excludedOrderIds' => implode(',', self::EXCLUDED_ORDER_STATUS_IDS),
                                'languageId'       => $this->userPreferences->languageId(),
                                'startDate'        => $timespan->startDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
                                'endDate'          => $timespan->endDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
                            
                            ])
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($ordersStatusOverview as $orderStatus) {
            $overview[$orderStatus['status']] = $orderStatus['orders'];
        }
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems(...
                                                                              array_map(function (
                                                                                  string $orderStatusName,
                                                                                  float  $orderStatusOrders
                                                                              ) {
                                                                                  return $this->factory->useData()
                                                                                      ->useMapData()
                                                                                      ->createItem($this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemTitle($orderStatusName),
                                                                                                   $this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemValue($orderStatusOrders));
                                                                              },
                                                                                  array_keys($overview),
                                                                                  array_values($overview))));
    }
}