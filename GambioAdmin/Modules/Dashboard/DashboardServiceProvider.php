<?php
/*--------------------------------------------------------------
   DashboardServiceProvider.php 2021-09-28
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard;

use Curl\Curl;
use Gambio\Admin\Modules\Configuration\App\Data\Repositories\ConfigurationRepository;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionIndex;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionResourceProxy;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdateCategory;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdatePeriod;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdateSocialMediaEmbeds;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdateStep;
use Gambio\Admin\Modules\Dashboard\App\Actions\ActionUpdateVisibility;
use Gambio\Admin\Modules\Dashboard\App\Data\DashboardConfigurationStorage;
use Gambio\Admin\Modules\Dashboard\App\Data\DashboardConfigurationStorageImpl;
use Gambio\Admin\Modules\Dashboard\Factories\StatisticsResultFactory;
use Gambio\Admin\Modules\Dashboard\Html\Builders\EndpointUrlBuilder;
use Gambio\Admin\Modules\Dashboard\Html\HtmlCacheReader;
use Gambio\Admin\Modules\Dashboard\Html\ValueObjects\CacheFilePath;
use Gambio\Admin\Modules\DashboardStatistics\Services\DashboardStatisticsService;
use Gambio\Admin\Modules\SetupWizard\SetupWizardServiceInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Compatibility\ConfigurationStorageRepositoryBuilder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Permission\Services\PermissionService;
use GambioCloudPlan;

/**
 * Class DashboardServiceProvider
 *
 * @package Gambio\Admin\Modules\Dashboard
 * @codeCoverageIgnore
 */
class DashboardServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            ActionResourceProxy::class,
            ActionUpdateSocialMediaEmbeds::class,
            ActionUpdateStep::class,
            ActionUpdateVisibility::class,
            ActionIndex::class,
            ActionUpdateCategory::class,
            ActionUpdatePeriod::class
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerActions();
        
        $this->application->registerShared(ConfigurationService::class)->addArgument(ConfigurationRepository::class);
        
        $this->application->registerShared(DashboardConfigurationStorage::class,
                                           DashboardConfigurationStorageImpl::class)
            ->addArgument(ConfigurationStorageRepositoryBuilder::class);
        //TODO: remove this $_SESSION variable
        $languageId = (int)($_SESSION['languages_id'] ?? null);
        
        //TODO: this will always fail, because the MainFactory doesnt work anymore
        $endpoint = EndpointUrlBuilder::create()
            ->withLanguageId($languageId)
            ->setIsCloudShop(class_exists(GambioCloudPlan::class))
            ->build();
        
        $cacheFilePath = new CacheFilePath(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR
                                           . "admin_dashboard_{$languageId}.html.cache");
        
        $this->application->registerShared(HtmlCacheReader::class)
            ->addArgument($endpoint)
            ->addArgument(Curl::class)
            ->addArgument($cacheFilePath);
        
        $this->application->registerShared(Curl::class);
        
        $this->application->registerShared(StatisticsResultFactory::class);
    }
    
    
    protected function registerActions()
    {
        $this->application->registerShared(ActionResourceProxy::class)->addArgument(Curl::class);
        
        $this->application->registerShared(ActionUpdateSocialMediaEmbeds::class)
            ->addArgument(DashboardConfigurationStorage::class);
        
        $this->application->registerShared(ActionUpdateStep::class)->addArgument(SetupWizardServiceInterface::class);
        
        $this->application->registerShared(ActionUpdateVisibility::class)
            ->addArgument(SetupWizardServiceInterface::class);
        
        $this->application->registerShared(ActionIndex::class)
            ->addArgument(DashboardStatisticsService::class)
            ->addArgument(SetupWizardServiceInterface::class)
            ->addArgument(DashboardConfigurationStorage::class)
            ->addArgument(HtmlCacheReader::class)
            ->addArgument(Url::class)
            ->addArgument(ConfigurationService::class)
            ->addArgument(PermissionService::class)
            ->addArgument(UserPreferences::class)
            ->addArgument(StatisticsResultFactory::class);
        
        $this->application->registerShared(ActionUpdateCategory::class)
            ->addArgument(DashboardStatisticsService::class);
        $this->application->registerShared(ActionUpdatePeriod::class)
            ->addArgument(DashboardStatisticsService::class);
    }
}