<?php
/*------------------------------------------------------------------------------
 StatisticsOverviewServiceProvider.php 2023-03-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\StatisticsOverview;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Language\Services\LanguageFactory;
use Gambio\Admin\Modules\StatisticsOverview\App\Action\ConfigureWidget;
use Gambio\Admin\Modules\StatisticsOverview\App\Action\GetWidgets;
use Gambio\Admin\Modules\StatisticsOverview\App\Action\Overview;
use Gambio\Admin\Modules\StatisticsOverview\App\Data\StatisticsOverviewWidgetDefinitionProvider;
use Gambio\Admin\Modules\StatisticsOverview\App\Data\StatisticsOverviewWidgetOptionsStorage;
use Gambio\Admin\Modules\StatisticsOverview\App\StatisticsOverviewService;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCategoriesRankingOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioConversionRateCount;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioConversionRateTrend;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCustomersCartOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCustomersNewsletterSubscriptionsRate;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCustomersOnlineList;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCustomersRanking;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCustomersRevenueRanking;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCustomersSignupsCount;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCustomersSignupsTrend;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioCustomersStructure;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioForeignOrdersRate;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersCount;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersList;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersPaymentMethodsOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersRegionsOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersShippingMethodsOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersStatusChangesList;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersStatusOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersTimesOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioOrdersTrend;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioProductsRankingOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioRevenueCount;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioRevenueTrend;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioVisitorsCount;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioVisitorsOnlineCount;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioVisitorsTimesOverview;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioVisitorsTrend;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioWithdrawalsCount;
use Gambio\Admin\Modules\StatisticsOverview\Model\Entities\WidgetDefinition\GambioWithdrawalsTrend;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetCategoryFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\MapDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\NumberDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\SerialDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\TableDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\TextDataFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetDataFactory\TimespanFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\DropdownOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\MaxEntriesOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetOptionFactory\PredefinedOptionFactory\TimespanOptionFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewFactory\WidgetVisualizationFactory;
use Gambio\Admin\Modules\StatisticsOverview\Services\StatisticsOverviewService as StatisticsOverviewServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\TextManager\Services\TextManager;
use NumberFormatter;

/**
 * Class representing the service provider for the statistics overview service.
 *
 * @package Gambio\Admin\Modules\StatisticsOverview
 * @codeCoverageIgnore
 */
class StatisticsOverviewServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * Method name reference.
     */
    private const WIDGET_DEFINITION_REGISTER_METHOD = 'register';
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioCategoriesRankingOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioConversionRateCount::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioConversionRateTrend::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioCustomersCartOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD,
                           [GambioCustomersNewsletterSubscriptionsRate::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioCustomersOnlineList::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioCustomersRanking::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioCustomersRevenueRanking::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioCustomersSignupsCount::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioCustomersSignupsTrend::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioForeignOrdersRate::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersCount::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersList::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersPaymentMethodsOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersRegionsOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersShippingMethodsOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersStatusChangesList::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersStatusOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersTimesOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioOrdersTrend::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioProductsRankingOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioRevenueCount::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioRevenueTrend::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioVisitorsCount::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioVisitorsTrend::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioVisitorsOnlineCount::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioVisitorsTimesOverview::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioWithdrawalsTrend::class]);
        $this->application->inflect(StatisticsOverviewWidgetDefinitionProvider::class)
            ->invokeMethod(self::WIDGET_DEFINITION_REGISTER_METHOD, [GambioWithdrawalsCount::class]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            StatisticsOverviewServiceInterface::class,
            StatisticsOverviewFactory::class,
            GetWidgets::class,
            ConfigureWidget::class,
            Overview::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $languageCode = $this->application->get(UserPreferences::class)->languageId() === 2 ? 'de' : 'en';
        
        $currencyFormatter   = new NumberFormatter($languageCode, NumberFormatter::CURRENCY);
        $numberFormatter     = new NumberFormatter($languageCode, NumberFormatter::DEFAULT_STYLE);
        $percentageFormatter = new NumberFormatter($languageCode, NumberFormatter::PERCENT);
        
        $this->application->registerShared(ConfigureWidget::class)
            ->addArgument(StatisticsOverviewServiceInterface::class);
        
        $this->application->registerShared(GetWidgets::class)
            ->addArgument(StatisticsOverviewServiceInterface::class)
            ->addArgument(LanguageService::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(Overview::class);
        
        $this->application->registerShared(StatisticsOverviewWidgetDefinitionProvider::class)
            ->addArgument(StatisticsOverviewFactory::class);
        
        $this->application->registerShared(StatisticsOverviewWidgetOptionsStorage::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class)
            ->addArgument(StatisticsOverviewFactory::class);
        
        $this->application->registerShared(StatisticsOverviewServiceInterface::class,
                                           StatisticsOverviewService::class)
            ->addArgument(StatisticsOverviewWidgetDefinitionProvider::class)
            ->addArgument(StatisticsOverviewWidgetOptionsStorage::class)
            ->addArgument(StatisticsOverviewFactory::class);
        
        $this->application->registerShared(MapDataFactory::class);
        
        $this->application->registerShared(NumberDataFactory::class);
        
        $this->application->registerShared(SerialDataFactory::class);
        
        $this->application->registerShared(TableDataFactory::class);
        
        $this->application->registerShared(TextDataFactory::class);
        
        $this->application->registerShared(TimespanFactory::class);
        
        $this->application->registerShared(MaxEntriesOptionFactory::class);
        
        $this->application->registerShared(TimespanOptionFactory::class);
        
        $this->application->registerShared(DropdownOptionFactory::class);
        
        $this->application->registerShared(PredefinedOptionFactory::class);
        
        $this->application->registerShared(WidgetCategoryFactory::class);
        
        $this->application->registerShared(WidgetDataFactory::class)
            ->addArgument(MapDataFactory::class)
            ->addArgument(SerialDataFactory::class)
            ->addArgument(TableDataFactory::class)
            ->addArgument(TextDataFactory::class)
            ->addArgument(NumberDataFactory::class)
            ->addArgument(TimespanFactory::class);
        
        $this->application->registerShared(WidgetOptionFactory::class)
            ->addArgument(DropdownOptionFactory::class)
            ->addArgument(PredefinedOptionFactory::class);
        
        $this->application->registerShared(WidgetVisualizationFactory::class);
        
        $this->application->registerShared(StatisticsOverviewFactory::class)
            ->addArgument(WidgetOptionFactory::class)
            ->addArgument(WidgetVisualizationFactory::class)
            ->addArgument(WidgetCategoryFactory::class)
            ->addArgument(WidgetDataFactory::class)
            ->addArgument(LanguageFactory::class);
        
        $this->application->registerShared(GambioCategoriesRankingOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioConversionRateTrend::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioCustomersCartOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioCustomersNewsletterSubscriptionsRate::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioCustomersOnlineList::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioConversionRateCount::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class)
            ->addArgument($percentageFormatter);
        
        $this->application->registerShared(GambioCustomersRanking::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioCustomersRevenueRanking::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioCustomersSignupsCount::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class)
            ->addArgument($numberFormatter);
        
        $this->application->registerShared(GambioCustomersSignupsTrend::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioForeignOrdersRate::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioOrdersCount::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class)
            ->addArgument($numberFormatter);
        
        $this->application->registerShared(GambioOrdersList::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class)
            ->addArgument($currencyFormatter);
        
        $this->application->registerShared(GambioOrdersPaymentMethodsOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(GambioOrdersRegionsOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(GambioOrdersShippingMethodsOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class)
            ->addArgument(TextManager::class);
        
        $this->application->registerShared(GambioOrdersStatusChangesList::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioOrdersStatusOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioOrdersTimesOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioOrdersTrend::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioProductsRankingOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioRevenueCount::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class)
            ->addArgument($currencyFormatter);
        
        $this->application->registerShared(GambioRevenueTrend::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioVisitorsCount::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class)
            ->addArgument($numberFormatter);
        
        $this->application->registerShared(GambioVisitorsOnlineCount::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class)
            ->addArgument($numberFormatter);
        
        $this->application->registerShared(GambioVisitorsTimesOverview::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioVisitorsTrend::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioWithdrawalsTrend::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(GambioWithdrawalsCount::class)
            ->addArgument(StatisticsOverviewFactory::class)
            ->addArgument(Connection::class)
            ->addArgument($numberFormatter);
    }
}