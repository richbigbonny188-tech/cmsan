<?php
/* --------------------------------------------------------------
  GambioCustomersOnlineList.php 2023-06-09
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
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\MaxEntriesOptionFactory;
use Gambio\Core\Application\ValueObjects\UserPreferences;

/**
 * Class representing a widget showing the current customers online.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioCustomersOnlineList extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioCustomersOnlineList';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Wer ist online?',
        self::LANGUAGE_CODE_ENGLISH => 'Who is online?',
    ];
    
    /**
     * Multilingual column titles.
     */
    private const COLUMN_TITLE = [
        self::LANGUAGE_CODE_GERMAN  => 'Kunde',
        self::LANGUAGE_CODE_ENGLISH => 'Customer',
    ];
    
    /**
     * Customer IDs to be excluded from database queries.
     */
    protected const EXCLUDED_CUSTOMER_IDS = [0];
    
    
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
        $this->connection      = $connection;
        $this->userPreferences = $userPreferences;
        
        parent::__construct($factory->createId(self::ID),
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForCustomers(),
                            $factory->useVisualizations()->createTable(),
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
        $languageCode = $this->userPreferences->languageId()
                        === self::LANGUAGE_ID_GERMAN ? self::LANGUAGE_CODE_GERMAN : self::LANGUAGE_CODE_ENGLISH;
        
        $customersOnline = $this->connection->createQueryBuilder()
            ->select('`full_name` AS `name`')
            ->from('whos_online')
            ->where('`customer_id` NOT IN (:excludedCustomerIds)')
            ->groupBy('`full_name`')
            ->orderBy('MAX(`time_last_click`)', 'DESC')
            ->setMaxResults((int)$options->getById(MaxEntriesOptionFactory::ID)->value())
            ->setParameter('excludedCustomerIds', implode(',', self::EXCLUDED_CUSTOMER_IDS))
            ->executeQuery()
            ->fetchAllAssociative();
        
        return $this->factory->useData()->useTableData()->createTableData($this->factory->useData()
                                                                              ->useTableData()
                                                                              ->createColumns($this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createTextColumn(self::COLUMN_TITLE[$languageCode],
                                                                                                                     'customer')),
                                                                          $this->factory->useData()
                                                                              ->useTableData()
                                                                              ->createRows(...
                                                                                  array_map(function (array $customer) {
                                                                                      return $this->factory->useData()
                                                                                          ->useTableData()
                                                                                          ->createRow($this->factory->useData()
                                                                                                          ->useTableData()
                                                                                                          ->createRowFields($this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowTextField('customer',
                                                                                                                                                     $customer['name'])));
                                                                                  }, $customersOnline)));
    }
    
}