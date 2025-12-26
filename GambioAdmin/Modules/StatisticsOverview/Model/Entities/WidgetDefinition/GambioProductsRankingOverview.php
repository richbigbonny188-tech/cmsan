<?php
/*--------------------------------------------------------------
   GambioProductsRankingOverview.php 2023-06-09
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
 * Class representing a widget showing the most selling products.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioProductsRankingOverview extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioProductsRankingOverview';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Meistverkaufte Produkte',
        self::LANGUAGE_CODE_ENGLISH => 'Best-Selling Products',
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
                            $factory->useVisualizations()->createTreemapChart(),
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
        $products = $this->connection->createQueryBuilder()
            ->select([
                         'COUNT(products_description.products_id) AS orders',
                         'products_description.products_name AS name',
                     ])
            ->from('orders')
            ->join('orders',
                   'orders_products',
                   'orders_products',
                   'orders.orders_id = orders_products.orders_id')
            ->join('orders_products',
                   'products_description',
                   'products_description',
                   'products_description.products_id = orders_products.products_id')
            ->where('products_description.language_id = :languageId')
            ->andWhere('orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('date_purchased BETWEEN :startDate AND :endDate')
            ->groupBy('products_description.products_id')
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
        
        $deletedProducts = $this->connection->createQueryBuilder()
            ->select([
                         'COUNT(orders_products.products_name) AS orders',
                         'orders_products.products_name AS name'
                     ])
            ->from('orders')
            ->join('orders',
                   'orders_products',
                   'orders_products',
                   'orders.orders_id = orders_products.orders_id')
            ->where('orders_products.products_id = 0')
            ->andWhere('orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('date_purchased BETWEEN :startDate AND :endDate')
            ->groupBy('orders_products.products_name')
            ->orderBy('orders', 'DESC')
            ->setMaxResults((int)$options->getById(MaxEntriesOptionFactory::ID)->value())
            ->setParameters([
                                'excludedOrderIds' => implode(',', self::EXCLUDED_ORDER_STATUS_IDS),
                                'startDate'        => $timespan->startDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
                                'endDate'          => $timespan->endDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_END)
                            ])
            ->executeQuery()
            ->fetchAllAssociative();
        
        $products = array_merge($products, $deletedProducts);
        
        usort($products, function ($a, $b) {
            return $b['orders'] <=> $a['orders'];
        });
        
        $products = array_slice($products, 0, (int)$options->getById(MaxEntriesOptionFactory::ID)->value());
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems(...array_map(function ($order) {
                                                                              return $this->factory->useData()
                                                                                  ->useMapData()
                                                                                  ->createItem($this->factory->useData()
                                                                                                   ->useMapData()
                                                                                                   ->createItemTitle($order['name']),
                                                                                               $this->factory->useData()
                                                                                                   ->useMapData()
                                                                                                   ->createItemValue((integer)$order['orders']));
                                                                          },
                                                                              $products)));
    }
}
