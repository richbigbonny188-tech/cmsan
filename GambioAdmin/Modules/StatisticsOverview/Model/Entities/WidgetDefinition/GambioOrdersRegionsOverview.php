<?php
/*--------------------------------------------------------------
   GambioProductsRankingOverview.php 2023-06-13
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
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\DropdownOption\OptionItems;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions\OptionTitles;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetOption\DropdownOption;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\MaxEntriesOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\TimespanOptionFactory;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class representing a widget showing orders grouped by regions.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioOrdersRegionsOverview extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioOrdersRegionsOverview';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Bestellungen per Regionen (Übersicht)',
        self::LANGUAGE_CODE_ENGLISH => 'Orders By Regions Overview',
    ];
    
    /**
     * Region dropdown ID.
     */
    private const REGION_DROPDOWN_ID = "region";
    
    /**
     * Region dropdown default value.
     */
    private const REGION_DROPDOWN_DEFAULT_VALUE = self::REGION_DROPDOWN_COUNTRY_ITEM;
    
    
    /**
     * Region dropdown multilingual titles.
     */
    private const REGION_DROPDOWN_TITLES = [
        self::LANGUAGE_CODE_GERMAN  => "Region",
        self::LANGUAGE_CODE_ENGLISH => "Region",
    ];
    
    /**
     * Country as item for region dropdown.
     */
    private const REGION_DROPDOWN_COUNTRY_ITEM = "country_code";
    
    /**
     * City as item for region dropdown.
     */
    private const REGION_DROPDOWN_CITY_ITEM = "city";
    
    /**
     * Multilingual item titles for region dropdown.
     */
    private const REGION_DROPDOWN_ITEMS = [
        self::REGION_DROPDOWN_COUNTRY_ITEM => [
            self::LANGUAGE_CODE_GERMAN  => "Land",
            self::LANGUAGE_CODE_ENGLISH => "Country",
        ],
        self::REGION_DROPDOWN_CITY_ITEM    => [
            self::LANGUAGE_CODE_GERMAN  => "Stadt",
            self::LANGUAGE_CODE_ENGLISH => "City",
        ],
    ];
    
    /**
     * Source dropdown ID.
     */
    private const SOURCE_DROPDOWN_ID = "source";
    
    /**
     * Source dropdown default value.
     */
    private const SOURCE_DROPDOWN_DEFAULT_VALUE = self::SOURCE_DROPDOWN_BILLING_ITEM;
    
    /**
     * Source dropdown multilingual titles.
     */
    private const SOURCE_DROPDOWN_TITLES = [
        self::LANGUAGE_CODE_GERMAN  => "Quelle",
        self::LANGUAGE_CODE_ENGLISH => "Source",
    ];
    
    /**
     * Billing as item for source dropdown.
     */
    private const SOURCE_DROPDOWN_BILLING_ITEM = "billing";
    
    /**
     * Delivery as item for source dropdown.
     */
    private const SOURCE_DROPDOWN_DELIVERY_ITEM = "delivery";
    
    /**
     * Multilingual item titles for source dropdown.
     */
    private const SOURCE_DROPDOWN_ITEMS = [
        self::SOURCE_DROPDOWN_BILLING_ITEM  => [
            self::LANGUAGE_CODE_GERMAN  => "Rechnung",
            self::LANGUAGE_CODE_ENGLISH => "Billing",
        ],
        self::SOURCE_DROPDOWN_DELIVERY_ITEM => [
            self::LANGUAGE_CODE_GERMAN  => "Lieferung",
            self::LANGUAGE_CODE_ENGLISH => "Delivery",
        ],
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
     * Text manager.
     *
     * @var TextManager
     */
    private $textManager;
    
    
    /**
     * @inheritDoc
     */
    public function __construct(
        StatisticsOverviewFactory $factory,
        Connection                $connection,
        TextManager               $textManager
    ) {
        $this->factory     = $factory;
        $this->connection  = $connection;
        $this->textManager = $textManager;
        
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
                                                                  $this->regionDropdown(),
                                                                  $this->sourceDropdown(),
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
        $region     = $options->getById(self::REGION_DROPDOWN_ID)->value();
        $source     = $options->getById(self::SOURCE_DROPDOWN_ID)->value();
        $timespan   = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        $maxEntries = (int)$options->getById(MaxEntriesOptionFactory::ID)->value();
        
        $isCountryRegionSelected = $region === self::REGION_DROPDOWN_DEFAULT_VALUE;
        
        $orders = $this->connection->createQueryBuilder()
            ->select("COUNT(orders_id) AS orders, {$source}_country_iso_code_2 AS country_code, {$source}_city AS city")
            ->from('orders')
            ->where('orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('date_purchased BETWEEN :startDate AND :endDate')
            ->andWhere("{$source}_country_iso_code_2 != ''")
            ->andWhere("{$source}_city != ''")
            ->groupBy($region)
            ->orderBy('orders', 'DESC')
            ->setMaxResults($maxEntries)
            ->setParameters([
                                'excludedOrderIds' => implode(',', self::EXCLUDED_ORDER_STATUS_IDS),
                                'startDate'        => $timespan->startDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
                                'endDate'          => $timespan->endDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
                            ])
            ->executeQuery()
            ->fetchAllAssociative();
        
        $data = [];
        
        foreach ($orders as $order) {
            $country     = $this->textManager->getPhraseText($order['country_code'], 'countries');
            $orderRegion = $isCountryRegionSelected ? $country : $order['city'] . ', ' . $country;
            
            $data[$orderRegion] = $order['orders'];
        }
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems(...
                                                                              array_map(function (
                                                                                  string $orderRegion,
                                                                                  string $orderCount
                                                                              ) {
                                                                                  return $this->factory->useData()
                                                                                      ->useMapData()
                                                                                      ->createItem($this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemTitle($orderRegion),
                                                                                                   $this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemValue((integer)$orderCount));
                                                                              },
                                                                                  array_keys($data),
                                                                                  $data)));
    }
    
    
    /**
     * Return region dropdown option.
     *
     * @return DropdownOption Region dropdown.
     */
    private function regionDropdown(): DropdownOption
    {
        return $this->factory->useOptions()->useDropdowns()->createDropdown($this->factory->useOptions()
                                                                                ->createId(self::REGION_DROPDOWN_ID),
                                                                            self::REGION_DROPDOWN_DEFAULT_VALUE,
                                                                            self::createDropdownItems($this->factory,
                                                                                                      self::REGION_DROPDOWN_ITEMS),
                                                                            self::createDropdownTitles($this->factory,
                                                                                                       self::REGION_DROPDOWN_TITLES));
    }
    
    
    /**
     * Return source dropdown option.
     *
     * @return DropdownOption Source dropdown.
     */
    private function sourceDropdown(): DropdownOption
    {
        return $this->factory->useOptions()->useDropdowns()->createDropdown($this->factory->useOptions()
                                                                                ->createId(self::SOURCE_DROPDOWN_ID),
                                                                            self::SOURCE_DROPDOWN_DEFAULT_VALUE,
                                                                            self::createDropdownItems($this->factory,
                                                                                                      self::SOURCE_DROPDOWN_ITEMS),
                                                                            self::createDropdownTitles($this->factory,
                                                                                                       self::SOURCE_DROPDOWN_TITLES));
    }
    
    
    /**
     * Return dropdown titles.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     * @param array                     $titles  Dropdown titles as array.
     *
     * @return OptionTitles Dropdown titles.
     */
    private static function createDropdownTitles(StatisticsOverviewFactory $factory, array $titles): OptionTitles
    {
        return $factory->useOptions()
            ->createTitles(...array_map(function (string $languageCode, string $title) use ($factory) {
                return $factory->useOptions()->createTitle($factory->createLanguageCode($languageCode), $title);
            },
                array_keys($titles),
                $titles));
    }
    
    
    /**
     * Return dropdown items.
     *
     * @param StatisticsOverviewFactory $factory Factory.
     * @param array                     $items   Dropdown items as array.
     *
     * @return OptionItems Dropdown items.
     */
    private static function createDropdownItems(StatisticsOverviewFactory $factory, array $items): OptionItems
    {
        return $factory->useOptions()
            ->useDropdowns()
            ->createItems(...array_map(function (string $value, array $titles) use ($factory) {
                return $factory->useOptions()->useDropdowns()->createItem($factory->useOptions()
                                                                              ->useDropdowns()
                                                                              ->createValue($value),
                                                                          $factory->useOptions()
                                                                              ->useDropdowns()
                                                                              ->createItemTitles(...
                                                                                  array_map(function (
                                                                                      string $languageCode,
                                                                                      string $title
                                                                                  ) use ($factory) {
                                                                                      return $factory->useOptions()
                                                                                          ->useDropdowns()
                                                                                          ->createItemTitle($factory->createLanguageCode($languageCode),
                                                                                                            $title);
                                                                                  },
                                                                                      array_keys($titles),
                                                                                      $titles)));
            },
                array_keys($items),
                $items));
    }
}