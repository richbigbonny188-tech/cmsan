<?php
/*------------------------------------------------------------------------------
  ActionIndex.php 2021-09-28
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -----------------------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Dashboard\App\Actions;

use Curl\Curl;
use Gambio\Admin\Application\Http\VuePageAction;
use Gambio\Admin\Modules\AccessGroup\Model\ValueObjects\AccessGroupItem;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\PermissionAction;
use Gambio\Admin\Modules\Dashboard\App\Data\DashboardConfigurationStorage;
use Gambio\Admin\Modules\Dashboard\Factories\ShopOriginFactory;
use Gambio\Admin\Modules\Dashboard\Factories\StatisticsResultFactory;
use Gambio\Admin\Modules\Dashboard\Html\HtmlProvider;
use Gambio\Admin\Modules\Dashboard\ValueObjects\DashboardData;
use Gambio\Admin\Modules\DashboardStatistics\Services\DashboardStatisticsService;
use Gambio\Admin\Modules\SetupWizard\SetupWizardServiceInterface;
use Gambio\Admin\Modules\DashboardStatistics\Model\ValueObjects\Result;
use Gambio\Core\Application\Http\Request;
use Gambio\Core\Application\Http\Response as HttpResponse;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Permission\Services\PermissionService;

/**
 * Class ActionIndex
 *
 * @package Gambio\Admin\Modules\Dashboard\App\Actions
 * @codeCoverageIgnore
 */
class ActionIndex extends VuePageAction
{
    /**
     * @var DashboardStatisticsService
     */
    private $statisticService;
    
    /**
     * @var SetupWizardServiceInterface
     */
    private $setupWizardService;
    
    /**
     * @var DashboardConfigurationStorage
     */
    private $storage;
    
    /**
     * @var HtmlProvider
     */
    private $htmlProvider;
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    /**
     * @var Curl
     */
    private $curl;
    
    /**
     * @var PermissionService
     */
    private $permissionService;
    
    /**
     * @var UserPreferences
     */
    private $userPreferences;
    
    /**
     * @var StatisticsResultFactory
     */
    private $statisticsResultFactory;
    
    
    /**
     * ActionIndex constructor.
     *
     * @param DashboardStatisticsService $statisticService
     * @param SetupWizardServiceInterface      $setupWizardService
     * @param DashboardConfigurationStorage    $storage
     * @param HtmlProvider                     $htmlProvider
     * @param Url                              $url
     * @param ConfigurationService             $configurationService
     * @param PermissionService                $permissionService
     * @param UserPreferences                  $userPreferences
     * @param StatisticsResultFactory          $statisticsResultFactory
     */
    public function __construct(
        DashboardStatisticsService $statisticService,
        SetupWizardServiceInterface      $setupWizardService,
        DashboardConfigurationStorage    $storage,
        HtmlProvider                     $htmlProvider,
        Url                              $url,
        ConfigurationService             $configurationService,
        PermissionService                $permissionService,
        UserPreferences                  $userPreferences,
        StatisticsResultFactory          $statisticsResultFactory
    ) {
        $this->statisticService        = $statisticService;
        $this->setupWizardService      = $setupWizardService;
        $this->storage                 = $storage;
        $this->htmlProvider            = $htmlProvider;
        $this->url                     = $url;
        $this->configurationService    = $configurationService;
        $this->permissionService       = $permissionService;
        $this->userPreferences         = $userPreferences;
        $this->statisticsResultFactory = $statisticsResultFactory;
    }
    
    
    /**
     * @inheritDoc
     *
     * @throws \Throwable
     */
    public function handle(Request $request, HttpResponse $response): HttpResponse
    {
        $this->enableLargePageLayout();
        $title    = $this->translate('PAGE_TITLE', 'dashboard');
        $template = dirname(__DIR__, 2) . '/ui/dashboard.html';
        
        return $response->write($this->render($title, $template, ['content' => $this->getDashboardContent()]));
    }
    
    
    /**
     * @inheritDoc
     */
    protected function jsEntrypoint(): string
    {
        return 'dashboard';
    }
    
    
    /**
     * @return string
     * @throws \Throwable
     */
    private function getDashboardContent(): string
    {
        $shopKeyConfig = $this->configurationService->find('configuration/GAMBIO_SHOP_KEY');
        $shopKey       = $shopKeyConfig ? $shopKeyConfig->value() : '';
        
        $shopUrl = $this->url->base();
        
        $shopVersionConfig = $this->configurationService->find('gm_configuration/INSTALLED_VERSION');
        $shopVersion       = $shopVersionConfig ? $shopVersionConfig->value() : '';
        
        $statistics            = $this->getStatisticsResult();
        $steps                 = $this->setupWizardService->getSteps();
        $userPreferredCategory = $this->statisticService->getPreferredCategory();
        $userPreferredPeriod   = $this->statisticService->getPreferredPeriod();
        
        $data                  = new DashboardData($shopKey,
                                                   $shopUrl,
                                                   $shopVersion,
                                                   $statistics,
                                                   $steps,
                                                   $this->storage->isExternalSocialMediaEmbedsAllowed(),
                                                   $userPreferredCategory,
                                                   $userPreferredPeriod,
                                                   (new ShopOriginFactory)->createShopOrigin());
        $json                  = json_encode($data, JSON_PRETTY_PRINT);
        $html                  = $this->htmlProvider->dashboardHtml();
        
        if (strpos($html, '[[dashboard_placeholder]]') === false) {
            $html .= "<script id='dashboardStatsJson' type='application/json'>$json</script>";
        } else {
            
            $html = str_replace('[[dashboard_placeholder]]', $json, $html);
            /**
             * The next replaces are here to fix some problems with the HTML template
             */
            $search  = [
                'https://www.gambio.de/files/admin-news/js',
                'https://www.gambio.com/files/admin-news/js',
            ];
            $replace = 'dashboard/javascript';
            
            $html    = str_replace($search, $replace, $html);
            $search  = 'title[language]';
            $replace = 'title';
            $html    = str_replace($search, $replace, $html);
        }
        
        return $html;
    }
    
    
    /**
     * Checks if the user has permission to access the Shop Statistics
     *
     * @return bool
     */
    private function userHasShopStatisticsPermission(): bool
    {
        $userId = $this->userPreferences->userId();
        
        return $this->permissionService->checkAdminPermission($userId,
                                                              PermissionAction::READ,
                                                              AccessGroupItem::CONTROLLER_TYPE,
                                                              'Dashboard/getStatisticBoxes');
    }
    
    
    /**
     * If the current admin do not have Shop Statistics permission, we return an empty Result object
     *
     * @return Result
     */
    private function getStatisticsResult(): Result
    {
        if ($this->userHasShopStatisticsPermission()) {
            return $this->statisticService->getStatistics();
        }
    
        return $this->statisticsResultFactory->createEmptyStatisticsResult();
    }
}