<?php
/*--------------------------------------------------------------
   GambioOrdersPaymentMethodsOverview.php 2023-06-09
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
 * Class representing a widget showing the most used payment methods.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition
 */
class GambioOrdersPaymentMethodsOverview extends WidgetDefinition
{
    /**
     * ID.
     */
    private const ID = 'GambioOrdersPaymentMethodsOverview';
    
    /**
     * Multilingual titles.
     */
    private const WIDGET_NAME = [
        self::LANGUAGE_CODE_GERMAN  => 'Häufigste Zahlungsweisen',
        self::LANGUAGE_CODE_ENGLISH => 'Most Used Payment Methods',
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
        $this->userPreferences = $userPreferences;
        $this->connection      = $connection;
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
        $timespan = $this->factory->useData()
            ->useTimespan()
            ->createFromTerm($options->getById(TimespanOptionFactory::ID)->value());
        
        $paymentMethods = $this->connection->createQueryBuilder()
            ->select([
                         'COUNT(payment_method) AS payments',
                         'payment_method AS name',
                         'gambio_hub_module_title AS hub_name',
                     ])
            ->from('orders')
            ->where('orders_status NOT IN (:excludedOrderIds)')
            ->andWhere('date_purchased BETWEEN :startDate AND :endDate')
            ->andWhere('(payment_method != "" AND payment_method != "gambio_hub") OR
                (payment_method = "gambio_hub" AND gambio_hub_module != "")')
            ->groupBy('payment_method, gambio_hub_module')
            ->orderBy('payment_method', 'DESC')
            ->setParameters([
                                'excludedOrderIds' => implode(',', self::EXCLUDED_ORDER_STATUS_IDS),
                                'startDate'        => $timespan->startDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_START),
                                'endDate'          => $timespan->endDate()
                                    ->format(self::DATA_QUERY_TIMESPAN_FORMAT_END),
                            ])
            ->setMaxResults((int)$options->getById(MaxEntriesOptionFactory::ID)->value())
            ->executeQuery()
            ->fetchAllAssociative();
        
        return $this->factory->useData()->useMapData()->createMapData($this->factory->useData()
                                                                          ->useMapData()
                                                                          ->createItems(...
                                                                              array_map(function ($paymentMethod) {
                                                                                  return $this->factory->useData()
                                                                                      ->useMapData()
                                                                                      ->createItem($this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemTitle($paymentMethod['hub_name'] ? : $this->getPaymentMethodClearName($paymentMethod['name'])),
                                                                                                   $this->factory->useData()
                                                                                                       ->useMapData()
                                                                                                       ->createItemValue((int)$paymentMethod['payments']));
                                                                              },
                                                                                  $paymentMethods)));
    }
    
    
    /**
     * Return clear name for payment method code.
     *
     * @param string $paymentMethodCode Payment method code.
     *
     * @return string Payment method clear name.
     */
    private function getPaymentMethodClearName(string $paymentMethodCode): string
    {
        $isHubPaymentMethod = strpos(strtoupper($paymentMethodCode), 'GAMBIO_HUB');
        
        if ($isHubPaymentMethod === false) {
            $localPhrase      = "MODULE_PAYMENT_" . strtoupper($paymentMethodCode) . "_TEXT_TITLE";
            $localPaymentName = $this->textManager->getPhraseText($localPhrase,
                                                                  $paymentMethodCode,
                                                                  $this->userPreferences->languageId());
            
            if ($localPaymentName !== $localPhrase) {
                return $localPaymentName;
            }
        }
        
        $hubPhrase      = "GAMBIO_HUB_" . strtoupper($paymentMethodCode) . "_TITLE";
        $hubPaymentName = $this->textManager->getPhraseText($hubPhrase,
                                                            $paymentMethodCode,
                                                            $this->userPreferences->languageId());
        
        return $hubPaymentName !== $hubPhrase ? $hubPaymentName : $paymentMethodCode;
    }
}