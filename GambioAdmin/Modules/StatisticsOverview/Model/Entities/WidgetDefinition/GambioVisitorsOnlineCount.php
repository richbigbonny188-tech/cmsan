<?php
/*--------------------------------------------------------------
   GambioVisitorsOnlineCount.php 2023-06-09
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
use NumberFormatter;

/**
 * Class representing a widget showing the current visitors online.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioVisitorsOnlineCount extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioVisitorsOnlineCount';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Besucher Online',
        self::LANGUAGE_CODE_ENGLISH => 'Visitors Online',
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
        Connection                $connection,
        NumberFormatter           $numberFormatter
    ) {
        $this->factory         = $factory;
        $this->connection      = $connection;
        $this->numberFormatter = $numberFormatter;
        
        parent::__construct($factory->createId(self::ID),
                            $factory->createNames($factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_GERMAN),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_GERMAN]),
                                                  $factory->createName($factory->createLanguageCode(self::LANGUAGE_CODE_ENGLISH),
                                                                       self::WIDGET_NAME[self::LANGUAGE_CODE_ENGLISH])),
                            $factory->useCategories()->createForCustomers(),
                            $factory->useVisualizations()->createText(),
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
        $visitors = $this->connection->createQueryBuilder()
                        ->select('COUNT(session_id) AS visitors')
                        ->from('whos_online')
                        ->where('FROM_UNIXTIME(time_last_click) BETWEEN DATE_SUB(NOW(), INTERVAL 5 MINUTE) AND NOW()')
                        ->where('is_bot = 0')
                        ->executeQuery()
                        ->fetchAllAssociative()[0];
        
        return $this->factory->useData()->useTextData()->createTextData($this->factory->useData()
                                                                            ->useTextData()
                                                                            ->createValue($this->numberFormatter->format((int)(($visitors['visitors']
                                                                                                                                ??
                                                                                                                                0)))));
    }
}

