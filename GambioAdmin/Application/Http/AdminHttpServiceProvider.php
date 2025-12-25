<?php
/* --------------------------------------------------------------
 AdminHttpServiceProvider.php 2020-10-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Application\Http;

use Gambio\Admin\Layout\Renderer\GambioAdminRenderer;
use Gambio\Admin\Layout\Renderer\Translations\FrontendTranslations;
use Gambio\Core\Application\DependencyInjection\AbstractBootableServiceProvider;
use Gambio\Core\TextManager\Services\TextManager;

/**
 * Class AdminHttpServiceProvider
 * @package Gambio\Admin\Application\Http
 */
class AdminHttpServiceProvider extends AbstractBootableServiceProvider
{
    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        $this->application->inflect(AdminAction::class)->invokeMethod('initAdminAction', [GambioAdminRenderer::class]);
        $this->application->inflect(VuePageAction::class)->invokeMethod('useFrontendTranslations',
                                                                        [FrontendTranslations::class]);
        $this->bootJSEngineController();
    }
    
    
    /**
     * Boots the js engine controller and inflect the ::initialize method.
     */
    private function bootJSEngineController(): void
    {
        $dependencies = [
            GambioAdminRenderer::class,
            TextManager::class,
            FrontendTranslations::class,
        ];
        $this->application->inflect(Controller\JSEngineController::class)
            ->invokeMethod('initializeJSEngineController', $dependencies);
    }
    
    
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
    }
}