<?php
/* --------------------------------------------------------------
 DirectHelpLoader.php 2022-10-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Core\Application\ValueObjects\Path;
use Gambio\Core\Application\ValueObjects\Url;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Core\TemplateEngine\Loader;
use Gambio\Core\TemplateEngine\LayoutData;

/**
 * Class DirectHelpLoader
 * @package Gambio\Admin\Layout\Smarty\Loaders
 */
class DirectHelpLoader implements Loader
{
    private const PROXY_URL   = 'admin.php?do=DirectHelpProxy';
    private const KEY_SCRIPTS = 'dynamic_script_assets';
    private const KEY_STYLES  = 'dynamic_style_assets';
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    /**
     * @var Path
     */
    private $path;
    
    /**
     * @var Url
     */
    private $url;
    
    
    /**
     * DirectHelpLoader constructor.
     *
     * @param ConfigurationService $configurationService
     * @param Path                 $path
     * @param Url                  $url
     */
    public function __construct(
        ConfigurationService $configurationService,
        Path $path,
        Url $url
    ) {
        $this->configurationService = $configurationService;
        $this->path                 = $path;
        $this->url                  = $url;
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $config = $this->configurationService->find('modules/GambioDirectHelp/active');
        if (null === $config) {
            return;
        }
        
        $isActive = $config->value() === '1';
        if (!$isActive) {
            return;
        }
        
        $assetsPostfix = file_exists($this->path->base() . '/.dev-environment') ? '' : '.min';
        $proxyUrl      = self::PROXY_URL;
        $assetsBase    = "{$this->url->base()}/GXModules/Gambio/DirectHelp/Build";
        
        $scriptAsset  = "{$assetsBase}/Admin/Javascript/extenders/online_manual{$assetsPostfix}.js";
        $scriptAssets = $data->get(self::KEY_SCRIPTS);
        $script       = $scriptAssets . PHP_EOL . "<script data-url='{$proxyUrl}' src='{$scriptAsset}'></script>";
        $data->assign(self::KEY_SCRIPTS, $script);
        
        $styleAsset  = "{$assetsBase}/Admin/Styles/online_manual{$assetsPostfix}.css";
        $styleAssets = $data->get(self::KEY_STYLES);
        $style       = $styleAssets . PHP_EOL . "<link rel='stylesheet' href='{$styleAsset}'/>";
        $data->assign(self::KEY_STYLES, $style);
    }
}