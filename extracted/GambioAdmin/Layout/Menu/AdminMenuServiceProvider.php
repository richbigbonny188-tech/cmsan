<?php
/* --------------------------------------------------------------
   AdminMenuServiceProvider.php 2021-06-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Layout\Menu\Compatibility\GXModulesJsonUtility;
use Gambio\Admin\Layout\Menu\EventListeners\GxModules\Json\JsonMenuFinder;
use Gambio\Admin\Layout\Menu\EventListeners\GxModules\Json\JsonMenuListener;
use Gambio\Admin\Layout\Menu\EventListeners\GxModules\ModulesFinder;
use Gambio\Admin\Layout\Menu\EventListeners\GxModules\Xml\XmlMenuFinder;
use Gambio\Admin\Layout\Menu\EventListeners\GxModules\Xml\XmlMenuListener;
use Gambio\Admin\Layout\Menu\Events\CoreMenuDataCollected;
use Gambio\Admin\Layout\Menu\Factories\CacheMenuFactory;
use Gambio\Admin\Layout\Menu\Factories\Helper\Utility;
use Gambio\Admin\Layout\Menu\Factories\Helper\Verifier;
use Gambio\Admin\Layout\Menu\Factories\PostCacheMenuFactory;
use Gambio\Admin\Layout\Menu\Filter\FilterFactory;
use Gambio\Admin\Layout\Menu\Filter\Types\ConfigActive;
use Gambio\Admin\Layout\Menu\Filter\Types\ConfigExists;
use Gambio\Admin\Layout\Menu\Filter\Types\DisplayOldModuleCenter;
use Gambio\Admin\Layout\Menu\Filter\Types\TemplateVersion;
use Gambio\Admin\Layout\Menu\Models\Cached\MenuSettings;
use Gambio\Admin\Layout\Menu\Repositories\Cache\AdminMenuCacheRepository;
use Gambio\Admin\Layout\Menu\Repositories\Cache\Implementation\MenuCache;
use Gambio\Admin\Layout\Menu\Repositories\Cache\Implementation\MenuMapper;
use Gambio\Admin\Layout\Menu\Repositories\PostCache\FavouritesHashListRepository;
use Gambio\Admin\Layout\Menu\Repositories\PostCache\Implementation\FavouritesHashListDbRepository;
use Gambio\Admin\Layout\Menu\Repositories\PostCache\Implementation\MenuProcessor;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\Application\ValueObjects\Environment;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Server;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Cache\Services\CacheFactory;
use Gambio\Core\Cache\Services\ClearCacheService;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\Permission\Services\PermissionService;
use Gambio\Core\TemplateEngine\Engines\Smarty\SmartyEngine;
use Gambio\Core\TextManager\Services\TextManager;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AdminMenuServiceProvider
 * @package Gambio\Admin\Layout\Menu
 */
class AdminMenuServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @return array
     */
    public function provides(): array
    {
        return [
            AdminMenuService::class,
            GXModulesJsonUtility::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->attachEventListener(CoreMenuDataCollected::class, XmlMenuListener::class);
        $this->application->attachEventListener(CoreMenuDataCollected::class, JsonMenuListener::class);
        
        $this->application->inflect(ClearCacheService::class)
            ->invokeMethod('addNamespaceToTemplateCaches', ['admin_menu']);
        $this->application->inflect(FilterFactory::class)
            ->invokeMethod('addFilter', [ConfigActive::FILTER_METHOD, ConfigActive::class]);
        $this->application->inflect(FilterFactory::class)
            ->invokeMethod('addFilter', [ConfigExists::FILTER_METHOD, ConfigExists::class]);
        $this->application->inflect(FilterFactory::class)
            ->invokeMethod('addFilter', [DisplayOldModuleCenter::FILTER_METHOD, DisplayOldModuleCenter::class]);
        $this->application->inflect(FilterFactory::class)
            ->invokeMethod('addFilter', [TemplateVersion::FILTER_METHOD, TemplateVersion::class]);
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->registerService();
        $this->registerCacheRepository();
        $this->registerMenuProcessor();
        $this->registerEventListeners();
        $this->registerUtilityComponents();
        $this->registerMenuFilter();
    }
    
    
    private function registerService(): void
    {
        $this->application->registerShared(AdminMenuService::class)
            ->addArgument(AdminMenuCacheRepository::class)
            ->addArgument(MenuProcessor::class);
    }
    
    
    private function registerCacheRepository(): void
    {
        $this->application->registerShared(AdminMenuCacheRepository::class,
                                           Repositories\Cache\Implementation\AdminMenuCacheRepository::class)
            ->addArgument(MenuCache::class)
            ->addArgument(MenuMapper::class)
            ->addArgument(FilterFactory::class)
            ->addArgument(LanguageService::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(MenuCache::class)
            ->addArgument(CacheFactory::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(MenuMapper::class)->addArgument(CacheMenuFactory::class);
        $this->application->registerShared(CacheMenuFactory::class)
            ->addArgument(TextManager::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(FilterFactory::class, Factories\FilterFactory::class);
    }
    
    
    private function registerMenuProcessor(): void
    {
        $this->application->registerShared(MenuProcessor::class)
            ->addArgument(FavouritesHashListRepository::class)
            ->addArgument(PostCacheMenuFactory::class);
        
        $this->application->registerShared(FavouritesHashListRepository::class,
                                           FavouritesHashListDbRepository::class)
            ->addArgument(Connection::class)
            ->addArgument(UserPreferences::class);
        
        $this->application->registerShared(PostCacheMenuFactory::class)
            ->addArgument(MenuSettings::class)
            ->addArgument(Verifier::class);
        
        $this->application->registerShared(MenuSettings::class)->addArgument(Url::class)->addArgument(Server::class);
        
        $this->application->registerShared(Verifier::class)
            ->addArgument(PermissionService::class)
            ->addArgument(Utility::class)
            ->addArgument(UserPreferences::class);
        $this->application->registerShared(Models\UserId::class)->addArgument(UserPreferences::class);
        
        $this->application->registerShared(Utility::class)->addArgument(MenuSettings::class);
    }
    
    
    private function registerEventListeners(): void
    {
        $this->application->registerShared(XmlMenuListener::class)
            ->addArgument(XmlMenuFinder::class)
            ->addArgument(CacheMenuFactory::class)
            ->addArgument(SmartyEngine::class)
            ->addArgument(Environment::class);
        $this->application->registerShared(XmlMenuFinder::class)->addArgument(ModulesFinder::class);
        
        $this->application->registerShared(JsonMenuListener::class)
            ->addArgument(JsonMenuFinder::class)
            ->addArgument(MenuMapper::class);
        $this->application->registerShared(MagnalisterMenuExtender::class)->addArgument(CacheMenuFactory::class);
        
        $this->application->registerShared(JsonMenuFinder::class)->addArgument(ModulesFinder::class);
        
        $this->application->registerShared(ModulesFinder::class)
            ->addArgument(Path::class)
            ->addArgument(ConfigurationFinder::class);
    }
    
    
    private function registerUtilityComponents(): void
    {
        $this->application->registerShared(GXModulesJsonUtility::class)
            ->addArgument(JsonMenuFinder::class)
            ->addArgument(MenuMapper::class);
    }
    
    
    private function registerMenuFilter(): void
    {
        $this->application->registerShared(ConfigActive::class)->addArgument(ConfigurationService::class);
        $this->application->registerShared(ConfigExists::class)->addArgument(ConfigurationService::class);
        $this->application->registerShared(DisplayOldModuleCenter::class)->addArgument(Path::class);
        $this->application->registerShared(TemplateVersion::class)
            ->addArgument(ConfigurationService::class)
            ->addArgument(Path::class);
    }
}
