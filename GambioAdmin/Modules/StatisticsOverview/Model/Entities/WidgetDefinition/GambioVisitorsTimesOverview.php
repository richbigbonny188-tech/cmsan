<?php
/*--------------------------------------------------------------
   GambioVisitorsTimesOverview.php 2023-06-09
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
 * Class representing a widget showing the hours of visits.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioVisitorsTimesOverview extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioVisitorsTimesOverview';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Besuchszeiten',
        self::LANGUAGE_CODE_ENGLISH => 'Visit Hours',
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
        $result = $this->connection->createQueryBuilder()
            ->select([
                         'HOUR(gm_counter_date) AS hour',
                         'SUM(gm_counter_visits_total) AS visits',
                     ])
            ->from('gm_counter_visits')
            ->groupBy('hour')
            ->orderBy('hour')
            ->executeQuery()
            ->fetchAllAssociative();
        
        $visitorData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $visitorData[$hour] = [
                'hour'   => str_pad((string)$hour, 2, '0', STR_PAD_LEFT),
                'visits' => 0,
            ];
        }
        
        foreach ($result as $row) {
            $visitorData[$row['hour']] = [
                'hour'   => str_pad((string)$row['hour'], 2, '0', STR_PAD_LEFT),
                'visits' => (int)$row['visits'],
            ];
        }
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems(...
                                                                              array_map(function ($visitorsHour) {
                                                                                  return $this->factory->useData()
                                                                                      ->useMapData()
                                                                                      ->createItem($this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemTitle($visitorsHour['hour']),
                                                                                                   $this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemValue((int)$visitorsHour['visits']));
                                                                              },
                                                                                  $visitorData)));
    }
}