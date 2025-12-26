<?php
/*------------------------------------------------------------------------------
 DashboardStatisticsServiceProvider.php 2021-09-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\DashboardStatistics;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\DashboardStatistics\App\DashboardStatisticsService;
use Gambio\Admin\Modules\DashboardStatistics\App\Data\DashboardStatisticsMapper;
use Gambio\Admin\Modules\DashboardStatistics\App\Data\DashboardStatisticsPreferredCategoryStorage;
use Gambio\Admin\Modules\DashboardStatistics\App\Data\DashboardStatisticsPreferredPeriodStorage;
use Gambio\Admin\Modules\DashboardStatistics\App\Data\DashboardStatisticsReader;
use Gambio\Admin\Modules\DashboardStatistics\App\Data\DashboardStatisticsRepository;
use Gambio\Admin\Modules\DashboardStatistics\App\Summarizer\AbstractSummarizer;
use Gambio\Admin\Modules\DashboardStatistics\App\Summarizer\MonthSummarizer;
use Gambio\Admin\Modules\DashboardStatistics\App\Summarizer\WeekSummarizer;
use Gambio\Admin\Modules\DashboardStatistics\App\Summarizer\YearSummarizer;
use Gambio\Admin\Modules\DashboardStatistics\Services\ConversionDataProviderResultFactory;
use Gambio\Admin\Modules\DashboardStatistics\Services\DashboardStatisticsService as DashboardStatisticsServiceInterface;
use Gambio\Admin\Modules\DashboardStatistics\Services\OrdersDataProviderResultFactory;
use Gambio\Admin\Modules\DashboardStatistics\Services\SalesDataProviderResultFactory;
use Gambio\Admin\Modules\DashboardStatistics\Services\SummarizableTimespanFactory;
use Gambio\Admin\Modules\DashboardStatistics\Services\VisitorsDataProviderResultFactory;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * @codeCoverageIgnore
 */
class DashboardStatisticsServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            DashboardStatisticsServiceInterface::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(DashboardStatisticsMapper::class)
            ->addArgument(OrdersDataProviderResultFactory::class)
            ->addArgument(ConversionDataProviderResultFactory::class)
            ->addArgument(SalesDataProviderResultFactory::class)
            ->addArgument(VisitorsDataProviderResultFactory::class);
        
        $this->application->registerShared(DashboardStatisticsPreferredCategoryStorage::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(DashboardStatisticsPreferredPeriodStorage::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        
        $this->application->registerShared(DashboardStatisticsReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(DashboardStatisticsRepository::class)
            ->addArgument(DashboardStatisticsReader::class)
            ->addArgument(DashboardStatisticsMapper::class);
        
        $this->application->registerShared(AbstractSummarizer::class)->addArgument(TextManager::class);
        
        $this->application->registerShared(MonthSummarizer::class);
        
        $this->application->registerShared(WeekSummarizer::class);
        
        $this->application->registerShared(YearSummarizer::class);
        
        $this->application->registerShared(ConversionDataProviderResultFactory::class)->addArgument(TextManager::class);
        
        $this->application->registerShared(OrdersDataProviderResultFactory::class)->addArgument(TextManager::class);
        
        $this->application->registerShared(SalesDataProviderResultFactory::class)
            ->addArgument(TextManager::class)
            ->addArgument(ConfigurationService::class);
        
        $this->application->registerShared(VisitorsDataProviderResultFactory::class)->addArgument(TextManager::class);
        
        $this->application->registerShared(SummarizableTimespanFactory::class)->addArgument(TextManager::class);
        
        $this->application->registerShared(DashboardStatisticsServiceInterface::class,
                                           DashboardStatisticsService::class)
            ->addArgument(SummarizableTimespanFactory::class)
            ->addArgument(DashboardStatisticsPreferredPeriodStorage::class)
            ->addArgument(DashboardStatisticsPreferredCategoryStorage::class)
            ->addArgument(DashboardStatisticsRepository::class);
    }
}