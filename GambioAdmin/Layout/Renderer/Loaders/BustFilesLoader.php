<?php
/* --------------------------------------------------------------
 BustFilesLoader.php 2020-02-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Renderer\Loaders;

use Gambio\Core\Application\ValueObjects\ServerInformation;
use Gambio\Core\Configuration\App\Creation\ConfigurationFinderBuilder;
use Gambio\Core\Configuration\App\NamespaceConfigurationFinder;
use Gambio\Core\TemplateEngine\Loader;
use Gambio\Core\TemplateEngine\LayoutData;

/**
 * Class BustFilesLoader
 * @package Gambio\Admin\Layout\Smarty\Loaders
 */
class BustFilesLoader implements Loader
{
    /**
     * @var ServerInformation
     */
    private $serverInformation;
    
    /**
     * @var NamespaceConfigurationFinder
     */
    private $configurationFinder;
    
    
    /**
     * BustFilesLoader constructor.
     *
     * @param ServerInformation          $serverInformation
     * @param ConfigurationFinderBuilder $configurationFinderBuilder
     */
    public function __construct(
        ServerInformation $serverInformation,
        ConfigurationFinderBuilder $configurationFinderBuilder
    ) {
        $this->serverInformation   = $serverInformation;
        $this->configurationFinder = $configurationFinderBuilder->buildNamespaceFinder('configuration');
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $infoAvailable = $this->serverInformation->modRewriteAvailable()
                         && $this->serverInformation->htaccessVersionAvailable();
        $condition     = $this->serverInformation->modRewriteWorking()
                         && $this->serverInformation->htaccessVersionGreaterEquals('2.8');
        $bustFiles     = $infoAvailable && $condition && $this->configEnabled();
        
        $data->assign('bust_files', $bustFiles ? 'true' : 'false');
    }
    
    
    /**
     * Checks if "use bust files" configuration is enabled.
     *
     * @return bool
     */
    private function configEnabled(): bool
    {
        $key   = 'USE_BUSTFILES';
        $value = strtolower($this->configurationFinder->get($key));
        
        return $value === 'true' || $value === '1';
    }
}