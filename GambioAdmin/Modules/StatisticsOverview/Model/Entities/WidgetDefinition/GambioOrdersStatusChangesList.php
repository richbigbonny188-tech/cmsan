<?php
/*--------------------------------------------------------------
   GambioOrdersStatusChangesList.php 2023-06-09
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

/**
 * Class representing a widget showing a list of recent order status changes.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioOrdersStatusChangesList extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioOrdersStatusChangesList';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Bestellstatusveränderungen (Liste)',
        self::LANGUAGE_CODE_ENGLISH => 'Order Status Changes (List)',
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
     * Multilingual column titles.
     */
    private const TABLE_COLUMNS = [
        'id'   => [
            self::LANGUAGE_CODE_GERMAN  => 'Bestellung',
            self::LANGUAGE_CODE_ENGLISH => 'Order',
        ],
        'date' => [
            self::LANGUAGE_CODE_GERMAN  => 'Datum',
            self::LANGUAGE_CODE_ENGLISH => 'Date',
        ],
        'from' => [
            self::LANGUAGE_CODE_GERMAN  => 'Von',
            self::LANGUAGE_CODE_ENGLISH => 'From',
        ],
        'to'   => [
            self::LANGUAGE_CODE_GERMAN  => 'Zu',
            self::LANGUAGE_CODE_ENGLISH => 'To',
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
        
        $ordersStatusChanges = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('orders_status_history', 'orders_status_history_a')
            ->leftJoin('orders_status_history_a',
                       'orders_status',
                       'orders_status_a',
                       'orders_status_history_a.orders_status_id =
                    orders_status_a.orders_status_id')
            ->where('orders_status_a.language_id = :languageId')
            ->orderBy('orders_status_history_a.date_added',
                      'DESC')
            ->setParameter('languageId',
                           $this->userPreferences->languageId())
            ->setMaxResults(1000)
            ->executeQuery()
            ->fetchAllAssociative();
        
        $maxResults              = (int)$options->getById(MaxEntriesOptionFactory::ID)->value();
        $ordersStatusChangesList = [];
        $index                   = 0;
        $alreadyDone             = '';
        foreach ($ordersStatusChanges as $ordersToChange) {
            foreach ($ordersStatusChanges as $ordersFromChange) {
                if ($ordersToChange['orders_id'] === $alreadyDone || $ordersFromChange['orders_id'] === $alreadyDone) {
                    continue;
                }
                
                if (($index < $maxResults) && ($ordersToChange['orders_id'] === $ordersFromChange['orders_id'])
                    && ($ordersToChange['orders_status_id'] !== $ordersFromChange['orders_status_id'])) {
                    $changesList['id']               = $ordersToChange['orders_id'];
                    $changesList['date']             = $ordersToChange['date_added'];
                    $changesList['from']             = $ordersFromChange['orders_status_name'];
                    $changesList['to']               = $ordersToChange['orders_status_name'];
                    $ordersStatusChangesList[$index] = $changesList;
                    $alreadyDone                     = $ordersToChange['orders_id'];
                    $index++;
                }
            }
        }
        
        return $this->factory->useData()->useTableData()->createTableData($this->factory->useData()
                                                                              ->useTableData()
                                                                              ->createColumns($this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createIntegerColumn(self::TABLE_COLUMNS['id'][$languageCode],
                                                                                                                        'id'),
                                                                                              $this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createDateColumn(self::TABLE_COLUMNS['date'][$languageCode],
                                                                                                                     'date',
                                                                                                                     self::TABLE_DATE_COLUMN_DATE_INPUT_FORMAT,
                                                                                                                     self::TABLE_DATE_COLUMN_DATE_OUTPUT_FORMAT),
                                                                                              $this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createTextColumn(self::TABLE_COLUMNS['from'][$languageCode],
                                                                                                                     'from'),
                                                                                              $this->factory->useData()
                                                                                                  ->useTableData()
                                                                                                  ->createTextColumn(self::TABLE_COLUMNS['to'][$languageCode],
                                                                                                                     'to')),
                                                                          $this->factory->useData()
                                                                              ->useTableData()
                                                                              ->createRows(...
                                                                                  array_map(function (
                                                                                      array $orderStatusChange
                                                                                  ) {
                                                                                      return $this->factory->useData()
                                                                                          ->useTableData()
                                                                                          ->createRow($this->factory->useData()
                                                                                                          ->useTableData()
                                                                                                          ->createRowFields($this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowNumberField('id',
                                                                                                                                                       (int)$orderStatusChange['id']),
                                                                                                                            $this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowTextField('date',
                                                                                                                                                     DateTime::createFromFormat(self::TABLE_DATE_COLUMN_DATE_INPUT_FORMAT,
                                                                                                                                                                                $orderStatusChange['date'])
                                                                                                                                                         ->format(self::TABLE_DATE_COLUMN_DATE_OUTPUT_FORMAT)),
                                                                                                                            $this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowTextField('from',
                                                                                                                                                     $orderStatusChange['from']),
                                                                                                                            $this->factory->useData()
                                                                                                                                ->useTableData()
                                                                                                                                ->createRowTextField('to',
                                                                                                                                                     $orderStatusChange['to'])));
                                                                                  },
                                                                                      $ordersStatusChangesList)));
    }
}