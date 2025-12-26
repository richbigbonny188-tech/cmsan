<?php
/* --------------------------------------------------------------
 AdminHeaderLoader.php 2021-05-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TemplateEngine\Loader;
use Gambio\Core\TextManager\Services\TextManager;
use Gambio\Core\UserConfiguration\Services\CurrentUserConfigurationService;

/**
 * Class AdminHeaderLoader
 * @package Gambio\Admin\Layout\Renderer\Loaders
 */
class AdminHeaderLoader implements Loader
{
    /**
     * @var TextManager
     */
    private $textManager;
    
    /**
     * @var CurrentUserConfigurationService
     */
    private $userConfigurationService;
    
    
    /**
     * AdminHeaderLoader constructor.
     *
     * @param TextManager                     $textManager
     * @param CurrentUserConfigurationService $userConfigurationService
     */
    public function __construct(
        TextManager $textManager,
        CurrentUserConfigurationService $userConfigurationService
    ) {
        $this->textManager              = $textManager;
        $this->userConfigurationService = $userConfigurationService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $data->assign('adminGeneral', $this->textManager->getSectionPhrases('admin_general'));
        $data->assign('admin_labels', $this->textManager->getSectionPhrases('admin_labels'));
        
        $recentSearchArea = $this->userConfigurationService->getValue('recent_search_area', 'categories');
        $data->assign('searchPlaceholder',
                      $this->textManager->getPhraseText("admin_search_{$recentSearchArea}", 'admin_labels'));
        
        $devEnvironment = file_exists(__DIR__ . '/../../../../.dev-environment');
        $jsNextConfig   = $this->userConfigurationService->getValue('jsNextDev');
        $jsNextDev      = $devEnvironment && (int)$jsNextConfig === 1;
        $data->assign('jsNextDev', $jsNextDev);
    }
}