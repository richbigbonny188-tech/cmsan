<?php
/*--------------------------------------------------------------------
 ThemeSettingsService.php 2023-03-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

namespace Gambio\GX\Services\System\ThemeSettings;

use CacheControl;
use Exception;
use ExistingDirectory;
use FileNotFoundException;
use Gambio\GX\Services\System\ThemeSettings\Interfaces\ThemeInitialisationServiceInterface;
use Gambio\GX\Services\System\ThemeSettings\Interfaces\ThemeSettingsServiceInterface;
use Gambio\GX\Services\System\ThemeSettings\Repositories\Interfaces\ThemeSettingsRepositoryInterface;
use Gambio\StyleEdit\Core\Components\Theme\Validator;
use Gambio\StyleEdit\Core\Services\Configuration\ConfigurationService;
use Gambio\StyleEdit\Core\Services\StyleEdit3Configuration\StyleEdit3ConfigurationService;
use Gambio\StyleEdit\Core\SingletonPrototype;
use Gambio\StyleEdit\DependencyInjector;
use StaticGXCoreLoader;
use ThemeDirectoryRoot;
use ThemeId;
use ThemeServiceInterface;
use ThemeSettings;

/**
 * Class ThemeSettingsService
 * @todo create unit test for this class
 */
class ThemeSettingsService implements ThemeSettingsServiceInterface
{
    /**
     * @var ThemeSettingsRepositoryInterface
     */
    protected $repository;
    
    /**
     * @var CacheControl
     */
    protected $cacheControl;
    
    /**
     * @var ThemeServiceInterface
     */
    protected $themeService;
    
    /**
     * @var ThemeInitialisationServiceInterface
     */
    protected $initialisationService;


    /**
     * ThemeSettingsService constructor.
     *
     * @param ThemeSettingsRepositoryInterface $repository
     * @param CacheControl $cacheControl
     * @param ThemeServiceInterface $themeService
     */
    public function __construct(
        ThemeSettingsRepositoryInterface $repository,
        CacheControl $cacheControl,
        ThemeServiceInterface $themeService
    ) {
        $this->repository            = $repository;
        $this->cacheControl          = $cacheControl;
        $this->themeService          = $themeService;
    }
    
    
    /**
     * @param string $themeId
     * @param bool   $clearCache
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function activateTheme(string $themeId, bool $clearCache = true): void
    {
        $allThemes = $this->repository->getAll();
        
        if (count($allThemes)) {
            
            foreach ($allThemes as $theme) {
                
                if ($themeId === $theme->id()) {
                    
                    $this->repository->updateDatabaseEntry($theme);
                }
            }
            
            if ($clearCache === true) {
                
                $this->clearCache($themeId);
            }
        }
        
        $this->loadStyleEdit3DependenciesForStyleEdit4($themeId);
    }
    
    
    /**
     * @param string $themeId
     *
     * @throws FileNotFoundException
     * @throws Exception
     */
    protected function loadStyleEdit3DependenciesForStyleEdit4(string $themeId): void
    {
        DependencyInjector::inject();
    
        if (Validator::for($themeId)->canBeOpenedInStyleEdit4() === false) {
        
            $styleEdit3ConfigurationService = SingletonPrototype::instance()->get(StyleEdit3ConfigurationService::class);
            
            /** @var StyleEdit3ConfigurationService $styleEdit3ConfigurationService */
            foreach ($styleEdit3ConfigurationService->configurations() as $configuration) {
                
                if ($configuration->name() === $themeId && $configuration->isActive() === true) {
    
                    /** @var ConfigurationService $styleEdit4ConfigurationService */
                    $styleEdit4ConfigurationService = SingletonPrototype::instance()->get(ConfigurationService::class);
                    $styleEdit4Configuration        = $styleEdit4ConfigurationService->convertFromStyleEdit3($configuration);
                    $settingJsonContent             = json_encode($styleEdit4Configuration, JSON_PRETTY_PRINT);
                    
                    $this->storeSettingsJsonForTheme($themeId, $settingJsonContent, true);
                    break;
                }
            }
        }
    }
    
    
    /**
     * @param string $themeId
     * @param string $content
     * @param bool   $overwrite
     */
    protected function storeSettingsJsonForTheme(string $themeId, string $content, bool $overwrite = false): void
    {
        $settingJsonPath = SHOP_ROOT . 'themes/' . $themeId . '/settings.json';
        
        if ($overwrite === true) {
            
            file_put_contents($settingJsonPath, $content);
        }
    }
    
    /**
     * @param string $themeId
     */
    protected function clearCache(string $themeId): void
    {
        $themeSourcePath      = DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemesPath();
        $themeDestinationPath = DIR_FS_CATALOG . StaticGXCoreLoader::getThemeControl()->getThemePath();
    
        $themeSettings = ThemeSettings::create(ThemeDirectoryRoot::create(new ExistingDirectory($themeSourcePath)),
                                               ThemeDirectoryRoot::create(new ExistingDirectory($themeDestinationPath)));
    
        $this->themeService->buildTemporaryTheme(ThemeId::create($themeId), $themeSettings);
    
        $this->cacheControl->clear_content_view_cache();
        $this->cacheControl->clear_templates_c();
        $this->cacheControl->clear_template_cache();
        $this->cacheControl->clear_google_font_cache();
        $this->cacheControl->clear_css_cache();
        $this->cacheControl->clear_expired_shared_shopping_carts();
        $this->cacheControl->remove_reset_token();
    }
}