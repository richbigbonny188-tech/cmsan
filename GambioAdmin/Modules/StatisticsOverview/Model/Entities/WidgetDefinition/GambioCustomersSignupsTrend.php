<?php
/*--------------------------------------------------------------
   GambioCustomersSignupsTrend.php 2023-06-09
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
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class representing a widget showing the rate of new customer sign-ups, displayed as trend.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioCustomersSignupsTrend extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioCustomersSignupsTrend';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Neukunden (Trend)',
        self::LANGUAGE_CODE_ENGLISH => 'New Customers (Trend)',
    ];
    
    /**
     * Multilingual category titles.
     */
    private const CATEGORY_TITLE = [
        self::LANGUAGE_CODE_GERMAN  => 'Neukunden',
        self::LANGUAGE_CODE_ENGLISH => 'New Customers',
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
                            $factory->useCategories()->createForCustomers(),
                            $factory->useVisualizations()->createAreaChart(),
                            $factory->useOptions()->createOptions($factory->useOptions()
                                                                      ->usePredefined()
                                                                      ->createTimespanDropdown($factory),
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
        $categories    = [];
        $values        = [];
        $categoryTitle = self::CATEGORY_TITLE[$this->userPreferences->languageId()
                                              == self::LANGUAGE_ID_GERMAN ? self::LANGUAGE_CODE_GERMAN : self::LANGUAGE_CODE_ENGLISH];
        $timespan      = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        $signups       = $this->connection->createQueryBuilder()
            ->select([
                         'count(*) AS amount',
                         'UNIX_TIMESTAMP(DATE(customers_date_added)) AS date',
                     ])
            ->from('customers')
            ->where('account_type = 0')
            ->andWhere('customers_date_added BETWEEN :startDate AND :endDate')
            ->groupBy('date')
            ->orderBy('date')
            ->setParameters([
                                'startDate' => $timespan->startDate()->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
                                'endDate'   => $timespan->endDate()->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
                            ])
            ->executeQuery()
            ->fetchAllAssociative();
        
        foreach ($signups as $signup) {
            $categories[] = $this->factory->useData()->useSerialData()->createCategory($signup['date']);
            $values[]     = $this->factory->useData()->useSerialData()->createItemValue((int)$signup['amount']);
        }
        
        return $this->factory->useData()->useSerialData()->createSerialData($this->factory->useData()
                                                                                ->useSerialData()
                                                                                ->createCategories(...$categories),
                                                                            $this->factory->useData()
                                                                                ->useSerialData()
                                                                                ->createItems($this->factory->useData()
                                                                                                  ->useSerialData()
                                                                                                  ->createItem($this->factory->useData()
                                                                                                                   ->useSerialData()
                                                                                                                   ->createItemTitle($categoryTitle),
                                                                                                               $this->factory->useData()
                                                                                                                   ->useSerialData()
                                                                                                                   ->createItemValues(...
                                                                                                                       $values))));
    }
}