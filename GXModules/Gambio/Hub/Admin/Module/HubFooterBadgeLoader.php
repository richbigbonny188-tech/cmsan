<?php
/* --------------------------------------------------------------
 HubFooterBadgeLoader.php 2020-10-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

namespace GXModules\Gambio\Hub\Admin\Module;

use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\TemplateEngine\LayoutData;
use Gambio\Core\TemplateEngine\Loader;

/**
 * Class FooterBadgesLoader
 *
 * @package Gambio\Admin\Layout\Smarty\Loaders
 */
class HubFooterBadgeLoader implements Loader
{
    /**
     * @var ConfigurationFinder
     */
    private $configurationFinder;
    
    
    /**
     * FooterBadgesLoader constructor.
     *
     * @param ConfigurationFinder $configurationFinder
     */
    public function __construct(ConfigurationFinder $configurationFinder)
    {
        $this->configurationFinder = $configurationFinder;
    }
    
    
    /**
     * @inheritDoc
     */
    public function load(LayoutData $data): void
    {
        $data->assign('isConnectedWithHub', $this->isConnected());
    }
    
    
    /**
     * @inheritDoc
     */
    protected function isConnected(): bool
    {
        $config = $this->configurationFinder->get('gm_configuration/GAMBIO_HUB_CLIENT_KEY');
        
        return !($config === null || $config === '');
    }
}
