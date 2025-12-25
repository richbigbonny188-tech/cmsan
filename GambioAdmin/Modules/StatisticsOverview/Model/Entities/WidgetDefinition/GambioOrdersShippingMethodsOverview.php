<?php
/*--------------------------------------------------------------
   GambioOrdersShippingMethodsOverview.php 2023-06-13
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
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class representing a widget showing the most used shipping methods.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioOrdersShippingMethodsOverview extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioOrdersShippingMethodsOverview';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Häufigste Versandarten',
        self::LANGUAGE_CODE_ENGLISH => 'Most Used Shipping Methods',
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
        UserPreferences           $userPreferences,
        Connection                $connection,
        TextManager               $textManager
    ) {
        $this->factory         = $factory;
        $this->connection      = $connection;
        $this->userPreferences = $userPreferences;
        $this->textManager     = $textManager;
        
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
        $sectionsWithShippingPrefixInLanguageFiles = [
            'amazon',
            'b2czones',
            'ebay',
            'gambioultra',
            'hitmeister',
            'marketplace',
            'yatego',
            'zones',
        ];
        
        $timespan   = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        $languageId = $this->userPreferences->languageId();
        
        $ordersToShippingMethods = $this->connection->createQueryBuilder()
            ->select([
                         'shipping_class as name',
                         'COUNT(shipping_class) AS amountOfOrders',
                     ])
            ->distinct()
            ->from('orders')
            ->where('orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('date_purchased BETWEEN :startDate AND :endDate')
            ->andWhere('shipping_class IS NOT NULL')
            ->andWhere('shipping_class != ""')
            ->groupBy('name')
            ->orderBy('amountOfOrders', 'DESC')
            ->setMaxResults((int)$options->getById(MaxEntriesOptionFactory::ID)->value())
            ->setParameters([
                                'excludedOrderIds' => implode(',',
                                                               self::EXCLUDED_ORDER_STATUS_IDS),
                                'startDate'        => $timespan->startDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
                                'endDate'          => $timespan->endDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
                            ])
            ->executeQuery()
            ->fetchAllAssociative();
        
        $shippingMethods = [];
        foreach ($ordersToShippingMethods as $shippingMethod) {
            $shippingMethodName = $shippingMethod['name'];
            
            if (strpos($shippingMethodName, '_') !== false) {
                $shippingMethodClassName = substr($shippingMethodName, 0, strpos($shippingMethodName, '_'));
                $phrase                  = 'MODULE_SHIPPING_' . strtoupper($shippingMethodClassName) . '_TEXT_TITLE';
                $section                 = in_array($shippingMethodClassName,
                                                    $sectionsWithShippingPrefixInLanguageFiles) ? 'shipping_'
                                                                                                  . $shippingMethodClassName : $shippingMethodClassName;
                $shippingMethodName      = $this->textManager->getPhraseText($phrase, $section, $languageId);
            }
            $shippingMethods[$shippingMethodName] = (int)$shippingMethod['amountOfOrders'];
        }
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems(...
                                                                              array_map(function (
                                                                                  string $shippingMethod,
                                                                                  int    $orderAmount
                                                                              ) {
                                                                                  return $this->factory->useData()
                                                                                      ->useMapData()
                                                                                      ->createItem($this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemTitle($shippingMethod),
                                                                                                   $this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemValue($orderAmount));
                                                                              },
                                                                                  array_keys($shippingMethods),
                                                                                  array_values($shippingMethods))));
    }
}