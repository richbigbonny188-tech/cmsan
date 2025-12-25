<?php
/*--------------------------------------------------------------------------------------------------
    ConfigurationAdapter.php 2021-10-14
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2021 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------------------------------------------
 */

namespace GXModules\Gambio\StyleEdit\Adapters;

use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\StyleEdit\Core\SingletonPrototype;

class ConfigurationAdapter implements Interfaces\ConfigurationAdapterInterface
{
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function get(string $key)
    {
        return $this->configurationService->find($key);
    }
    
    
    /**
     * @inheritDoc
     */
    public function set(string $key, $value): void
    {
        $this->configurationService->save($key, $value);
    }
}
