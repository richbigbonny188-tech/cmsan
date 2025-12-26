<?php
/*--------------------------------------------------------------
   GambioCustomersNewsletterSubscriptionsRate.php 2023-06-09
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
 * Class representing a widget showing the newsletter subscription rate.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioCustomersNewsletterSubscriptionsRate extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioCustomersNewsletterSubscriptionsRate';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Newsletter-Abonnenten',
        self::LANGUAGE_CODE_ENGLISH => 'Newsletter Subscriptions Rate',
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
                            $factory->useVisualizations()->createRadialBarChart(),
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
        $newsletterData   = $this->connection->createQueryBuilder()
                                ->select([
                                             'COUNT(customers_id) AS totalCustomers',
                                             'SUM(customers_newsletter) AS subscribers',
                                         ])
                                ->from('customers')
                                ->where('account_type = 0')
                                ->andWhere('customers_status != 0')
                                ->executeQuery()
                                ->fetchAllAssociative()[0];
        $subscriptionRate = 0;
        if ($newsletterData['totalCustomers'] > 0) {
            $subscriptionRate = (int)(($newsletterData['subscribers'] / $newsletterData['totalCustomers']) * 100);
        }
        
        return $this->factory->useData()->useNumberData()->createNumberData($this->factory->useData()
                                                                                ->useNumberData()
                                                                                ->createValue($subscriptionRate));
    }
}