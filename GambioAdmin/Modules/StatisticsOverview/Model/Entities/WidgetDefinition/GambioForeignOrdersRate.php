<?php
/* --------------------------------------------------------------
  GambioForeignOrdersRate.php 2023-06-09
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class representing a widget showing the rate of orders of foreign origin.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioForeignOrdersRate extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioForeignOrdersRate';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Rate der Auslandsbestellungen',
        self::LANGUAGE_CODE_ENGLISH => 'Rate Of Foreign Orders',
    ];
    
    /**
     * Multilingual item titles.
     */
    private const ITEM_TITLES = [
        'domestic' => [
            self::LANGUAGE_CODE_GERMAN  => 'Inlandsbestellungen',
            self::LANGUAGE_CODE_ENGLISH => 'Domestic Orders',
        ],
        'foreign'  => [
            self::LANGUAGE_CODE_GERMAN  => 'Auslandsbestellungen',
            self::LANGUAGE_CODE_ENGLISH => 'Foreign Orders',
        ],
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
                            $factory->useVisualizations()->createPieChart(),
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
        $languageId        = $this->userPreferences->languageId()
                             == self::LANGUAGE_ID_GERMAN ? self::LANGUAGE_CODE_GERMAN : self::LANGUAGE_CODE_ENGLISH;
        $itemTitleDomestic = self::ITEM_TITLES['domestic'][$languageId];
        $itemTitleForeign  = self::ITEM_TITLES['foreign'][$languageId];
        
        $storeCountry = $this->connection->createQueryBuilder()
            ->select('countries.countries_name AS storeCountry')
            ->from('countries')
            ->leftJoin('countries',
                       'gx_configurations',
                       'gx_configurations',
                       'countries.countries_id = gx_configurations.value')
            ->where('`key` = \'configuration/STORE_COUNTRY\'')
            ->executeQuery()
            ->fetchAssociative();
        $storeCountry = $storeCountry['storeCountry'];
        
        $orders = $this->connection->createQueryBuilder()
            ->select([
                         'SUM(orders.customers_country = :storeCountry) as "domestic"',
                         'SUM(orders.customers_country != :storeCountry) as "foreign"',
                     ])
            ->from('orders')
            ->setParameter('storeCountry', $storeCountry)
            ->executeQuery()
            ->fetchAssociative();
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems($this->factory->useData()
                                                                                            ->useMapData()
                                                                                            ->createItem($this->factory->useData()
                                                                                                             ->useMapData()
                                                                                                             ->createItemTitle($itemTitleDomestic),
                                                                                                         $this->factory->useData()
                                                                                                             ->useMapData()
                                                                                                             ->createItemValue((int)$orders['domestic'])),
                                                                                        $this->factory->useData()
                                                                                            ->useMapData()
                                                                                            ->createItem($this->factory->useData()
                                                                                                             ->useMapData()
                                                                                                             ->createItemTitle($itemTitleForeign),
                                                                                                         $this->factory->useData()
                                                                                                             ->useMapData()
                                                                                                             ->createItemValue((int)$orders['foreign']))));
    }
}