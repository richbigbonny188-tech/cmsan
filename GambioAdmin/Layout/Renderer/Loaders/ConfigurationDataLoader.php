<?php
/* --------------------------------------------------------------
 ConfigurationDataLoader.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TemplateEngine\Loader;

/**
 * Class ConfigurationDataLoader
 * @package Gambio\Admin\Layout\Renderer\Loaders
 */
class ConfigurationDataLoader implements Loader
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * ConfigurationDataLoader constructor.
     *
     * @param ConfigurationService $configurationService
     * @param Url                  $url
     */
    public function __construct(ConfigurationService $configurationService, Url $url)
    {
        $this->configurationService = $configurationService;
        $this->url                  = $url;
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $offlineConfig = $this->configurationService->find('gm_configuration/GM_SHOP_OFFLINE');
        $shopOffline   = $offlineConfig && $offlineConfig->value() === 'checked';
        $data->assign('shopOffline', $shopOffline);
        
        $shopKeyConfig = $this->configurationService->find('gm_configuration/SHOP_KEY_VALID');
        $shopKeyValid  = $shopKeyConfig
                         && ((strtolower($shopKeyConfig->value()) === 'true' || $shopKeyConfig->value() === '1'));
        $data->assign('shopKeyState', $shopKeyValid);
        
        $installedVersion = $this->configurationService->find('gm_configuration/INSTALLED_VERSION');
        $data->assign('shopVersion', $installedVersion ? $installedVersion->value() : '');
        
        $data->assign('baseUrl', $this->url->base());
        $data->assign('base_url', $this->url->base()); // compatibility for AdminLayoutHttpControllerResponse
    }
}