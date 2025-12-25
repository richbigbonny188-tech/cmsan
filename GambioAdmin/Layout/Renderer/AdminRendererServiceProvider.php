<?php
/* --------------------------------------------------------------
 AdminRendererServiceProvider.php 2021-09-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer;

use Gambio\Admin\Application\Token\TokenService;
use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Admin\Layout\Renderer\Translations\FrontendTranslations;
use Gambio\Admin\Layout\Renderer\Translations\Translations;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\ServerInformation;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Application\ValueObjects\UserPreferences;
use Gambio\Core\Configuration\App\Creation\ConfigurationFinderBuilder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\TemplateEngine\Engines\Smarty\SmartyEngine;
use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Core\UserConfiguration\Services\CurrentUserConfigurationService;

/**
 * Class AdminRendererServiceProvider
 *
 * @package Gambio\Admin\Layout\Renderer
 */
class AdminRendererServiceProvider extends AbstractServiceProvider
{
    private const LAYOUT_LOADERS = [
        Loaders\AdminMenuLoader::class,
        Loaders\BustFilesLoader::class,
        Loaders\DirectHelpLoader::class,
        Loaders\ConfigurationDataLoader::class,
        Loaders\LanguagesLoader::class,
        Loaders\AdminHeaderLoader::class,
        Loaders\TranslationsLoader::class,
        Loaders\TokenLoader::class,
        Loaders\EnvironmentLoader::class,
        Loaders\TranslationsLoader::class,
    ];
    
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            GambioAdminRenderer::class,
            GambioAdminLoader::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(GambioAdminRenderer::class)->addArguments([
                                                                                         SmartyEngine::class,
                                                                                         GambioAdminLoader::class,
                                                                                         LayoutData::class,
                                                                                     ]);
        $this->application->registerShared(GambioAdminLoader::class)->addArguments(static::LAYOUT_LOADERS);
        $this->registerLoaders();
        $this->registerTranslations();
    }
    
    
    /**
     * Dedicated method to register sub loaders.
     */
    private function registerLoaders(): void
    {
        $this->application->registerShared(Loaders\AdminMenuLoader::class)->addArguments([
                                                                                             AdminMenuService::class,
                                                                                             CurrentUserConfigurationService::class,
                                                                                             ConfigurationService::class,
                                                                                         ]);
        $this->application->registerShared(Loaders\ConfigurationDataLoader::class)->addArguments([
                                                                                                     ConfigurationService::class,
                                                                                                     Url::class,
                                                                                                 ]);
        $this->application->registerShared(Loaders\LanguagesLoader::class)->addArguments([
                                                                                             LanguageService::class,
                                                                                             UserPreferences::class,
                                                                                         ]);
        $this->application->registerShared(Loaders\AdminHeaderLoader::class)->addArguments([
                                                                                               TextManager::class,
                                                                                               CurrentUserConfigurationService::class,
                                                                                           ]);
        $this->application->registerShared(Loaders\BustFilesLoader::class)->addArguments([
                                                                                             ServerInformation::class,
                                                                                             ConfigurationFinderBuilder::class,
                                                                                         ]);
        $this->application->registerShared(Loaders\DirectHelpLoader::class)->addArguments([
                                                                                              ConfigurationService::class,
                                                                                              Path::class,
                                                                                              Url::class,
                                                                                          ]);
        
        $this->application->registerShared(Loaders\TokenLoader::class)->addArgument(TokenService::class);
        $this->application->registerShared(Loaders\TranslationsLoader::class)->addArgument(FrontendTranslations::class);
        $this->application->registerShared(Loaders\EnvironmentLoader::class);
        $this->application->registerShared(Loaders\FontAwesomeFallbackLoader::class);
    }
    
    
    /**
     * Registers the translations component.
     */
    private function registerTranslations(): void
    {
        $this->application->registerShared(FrontendTranslations::class)->addArguments([
                                                                                          Translations::class,
                                                                                          Translations::class,
                                                                                          Translations::class,
                                                                                      ]);
        $this->application->register(Translations::class)->addArgument(TextManager::class);
    }
}