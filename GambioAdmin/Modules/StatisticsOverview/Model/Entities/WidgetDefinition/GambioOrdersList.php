<?php
/*--------------------------------------------------------------
   GambioOrdersList.php 2023-06-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Gambio\Admin\Modules\StatisticsOverview\Model\Collections\WidgetOptions;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition;
use Gambio\Admin\Modules\StatisticsOverview\Model\ValueObjects\WidgetData;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\MaxEntriesOptionFactory;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use NumberFormatter;

/**
 * Class representing a widget showing a list of orders.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioOrdersList extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioOrdersList';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Bestellungen (Liste)',
        self::LANGUAGE_CODE_ENGLISH => 'Orders (List)',
    ];
    
    /**
     * Date column input format.
     */
    private const TABLE_DATE_COLUMN_DATE_INPUT_FORMAT = 'Y-m-d H:i:s';
    
    /**
     * Date column output format.
     */
    private const TABLE_DATE_COLUMN_DATE_OUTPUT_FORMAT = 'd.m.Y H:i';
    
    /**
     * Customer column name.
     */
    private const TABLE_COLUMN_CUSTOMER = 'customer';
    
    /**
     * Date column name.
     */
    private const TABLE_COLUMN_DATE = 'date';
    
    /**
     * Amount column name.
     */
    private const TABLE_COLUMN_AMOUNT = 'amount';
    
    /**
     * Status column name.
     */
    private const TABLE_COLUMN_STATUS = 'status';
    
    /**
     * Multilingual column titles.
     */
    private const TABLE_COLUMNS = [
        self::TABLE_COLUMN_CUSTOMER => [
            self::LANGUAGE_CODE_GERMAN  => 'Kunde',
            self::LANGUAGE_CODE_ENGLISH => 'Customer',
        ],
        self::TABLE_COLUMN_DATE     => [
            self::LANGUAGE_CODE_GERMAN  => 'Zeit',
            self::LANGUAGE_CODE_ENGLISH => 'Time',
        ],
        self::TABLE_COLUMN_AMOUNT   => [
            self::LANGUAGE_CODE_GERMAN  => 'Summe',
            self::LANGUAGE_CODE_ENGLISH => 'Amount',
        ],
        self::TABLE_COLUMN_STATUS   => [
            self::LANGUAGE_CODE_GERMAN  => 'Status',
            self::LANGUAGE_CODE_ENGLISH => 'Status',
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
     * Number formatter.
     *
     * @var NumberFormatter
     */
    private $numberFormatter;
    
    
    /**
     * @inheritDoc
     */
    public function __construct(
        StatisticsOverviewFactory $factory,
        UserPreferences           $userPreferences,
        Connection                $connection,
        NumberFormatter           $numberFormatter
    ) {
        $this->factory         = $factory;
        $this->connection      = $connection;
        $this->userPreferences = $userPreferences;
        $this->numberFormatter = $numberFormatter;
        
        parent::__construct($factory->createId(self::ID),
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForOrders(),
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
        
        $orders = $this->connection->createQueryBuilder()
            ->select([
                         'orders.customers_name AS customer',
                         'orders.date_purchased AS date',
                         'orders_total.value AS amount',
                         'orders.currency AS currency',
                         'orders_status.orders_status_name AS status',
                     ])
            ->from('orders')
            ->join('orders',
                   'orders_total',
                   'orders_total',
                   'orders.orders_id = orders_total.orders_id')
            ->join('orders',
                   'orders_status',
                   'orders_status',
                   'orders.orders_status = orders_status.orders_status_id')
            ->where('orders_total.class="ot_total"')
            ->andWhere('orders_status.language_id = :languageId')
            ->orderBy('orders.date_purchased', 'DESC')
            ->setMaxResults((int)$options->getById(MaxEntriesOptionFactory::ID)->value())
            ->setParameter('languageId', $this->userPreferences->languageId())
            ->executeQuery()
            ->fetchAllAssociative();
        
        return $this->factory->useData()->useTableData()->createTableData($this->factory->useData()
                                                                              ->useTableData()
                                                                              ->createColumns($this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createTextColumn(self::TABLE_COLUMNS[self::TABLE_COLUMN_CUSTOMER][$languageCode],
                                                                                                                     self::TABLE_COLUMN_CUSTOMER),
                                                                                              $this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createTextColumn(self::TABLE_COLUMNS[self::TABLE_COLUMN_AMOUNT][$languageCode],
                                                                                                                     self::TABLE_COLUMN_AMOUNT),
                                                                                              $this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createDateColumn(self::TABLE_COLUMNS[self::TABLE_COLUMN_DATE][$languageCode],
                                                                                                                     self::TABLE_COLUMN_DATE,
                                                                                                                     self::TABLE_DATE_COLUMN_DATE_INPUT_FORMAT,
                                                                                                                     self::TABLE_DATE_COLUMN_DATE_OUTPUT_FORMAT),
                                                                                              $this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createTextColumn(self::TABLE_COLUMNS[self::TABLE_COLUMN_STATUS][$languageCode],
                                                                                                                     self::TABLE_COLUMN_STATUS)),
                                                                          $this->factory->useData()
                                                                              ->useTableData()
                                                                              ->createRows(...
                                                                                  array_map(function (array $order) {
                                                                                      return $this->factory->useData()
                                                                                          ->useTableData()
                                                                                          ->createRow($this->factory->useData()
                                                                                                          ->useTableData()
                                                                                                          ->createRowFields($this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowTextField(self::TABLE_COLUMN_CUSTOMER,
                                                                                                                                                     $order[self::TABLE_COLUMN_CUSTOMER]),
                                                                                                                            $this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowTextField(self::TABLE_COLUMN_DATE,
                                                                                                                                                     DateTime::createFromFormat(self::TABLE_DATE_COLUMN_DATE_INPUT_FORMAT,
                                                                                                                                                                                $order[self::TABLE_COLUMN_DATE])
                                                                                                                                                         ->format(self::TABLE_DATE_COLUMN_DATE_OUTPUT_FORMAT)),
                                                                                                                            $this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowTextField(self::TABLE_COLUMN_AMOUNT,
                                                                                                                                                     $this->numberFormatter->formatCurrency((float)$order[self::TABLE_COLUMN_AMOUNT],
                                                                                                                                                                                            $order['currency'])),
                                                                                                                            $this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowTextField(self::TABLE_COLUMN_STATUS,
                                                                                                                                                     $order[self::TABLE_COLUMN_STATUS])));
                                                                                  },
                                                                                      $orders)));
    }
}
