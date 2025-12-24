<?php
/* --------------------------------------------------------------
 AdminMenuLoader.php 2021-09-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Admin\Layout\Menu\AdminMenuService;
use Gambio\Core\Configuration\Model\Interfaces\Configuration;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TemplateEngine\Loader;
use Gambio\Core\UserConfiguration\Services\CurrentUserConfigurationService;

/**
 * Class AdminMenuLoader
 *
 * @package Gambio\Admin\Layout\Renderer\Loaders
 */
class AdminMenuLoader implements Loader
{
    private const CONFIG_KEY_FAVORITES_ALAWAYS_OPEN = 'configuration/UNFOLD_FAVS_BOX';
    
    /**
     * @var AdminMenuService
     */
    private $menuService;
    
    /**
     * @var CurrentUserConfigurationService
     */
    private $userConfigurationService;
    
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    /**
     * AdminMenuLoader constructor.
     *
     * @param AdminMenuService                $menuService
     * @param CurrentUserConfigurationService $userConfigurationService
     * @param ConfigurationService            $configurationService
     */
    public function __construct(
        AdminMenuService                $menuService,
        CurrentUserConfigurationService $userConfigurationService,
        ConfigurationService            $configurationService
    ) {
        $this->menuService              = $menuService;
        $this->userConfigurationService = $userConfigurationService;
        $this->configurationService     = $configurationService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $data->assign('menu', $this->menuService->getAdminMenu());
        
        $menuVisibility = $this->userConfigurationService->getValue('menuVisibility', 'expand');
        $menuVisibility = $menuVisibility === 'expandAll' ? 'expand-all' : $menuVisibility;
        $data->assign('menuVisibility', $menuVisibility);
        
        $this->assignAlwaysOpenFavorites($data);
    }
    
    
    /**
     * Assings the setting for the favorites' menu to always open it
     *
     * @param LayoutData $data
     */
    private function assignAlwaysOpenFavorites(LayoutData $data): void
    {
        $configuration       = $this->configurationService->find(self::CONFIG_KEY_FAVORITES_ALAWAYS_OPEN);
        $alwaysOpenFavorites = $this->isConfigTrue($configuration);
        
        $data->assign('alwaysOpenFavorites', $alwaysOpenFavorites);
    }
    
    
    /**
     * Returns only valid true values or false if configuration does not exist
     *
     * @param Configuration|null $configuration
     *
     * @return bool
     */
    private function isConfigTrue(?Configuration $configuration): bool
    {
        if ($configuration === null) {
            return false;
        }
        
        $valid = ['1', 'true', 'TRUE', 'True'];
        
        return in_array($configuration->value(), $valid, true);
    }
}
