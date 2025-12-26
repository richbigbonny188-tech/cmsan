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

/**
 * Class representing a widget showing the customers ranking.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioCustomersRanking extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioCustomersRanking';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Aktivste Kunden',
        self::LANGUAGE_CODE_ENGLISH => 'Most Active Customers',
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
        $customers = $this->connection->createQueryBuilder()
            ->select([
                         'COUNT(customers_id) AS orders',
                         'customers_name AS name',
                     ])
            ->from('orders')
            ->where('orders_status NOT IN (:excludedOrderIds)')
            ->groupBy('customers_name')
            ->orderBy('orders', 'DESC')
            ->setParameter('excludedOrderIds', implode(',', self::EXCLUDED_ORDER_STATUS_IDS))
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
                                                                                                       ->createItemTitle($customer['name']),
                                                                                                   $this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemValue((int)$customer['orders']));
                                                                              },
                                                                                  $customers)));
    }
}
