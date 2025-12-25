<?php
/* --------------------------------------------------------------
 ConfigurationFinder.php 2020-04-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Configuration\App;

use Gambio\Core\Configuration\Services\ConfigurationFinder as Finder;

/**
 * Class ConfigurationFinder
 * @package Gambio\Core\Configuration\Services
 */
class ConfigurationFinder implements Finder
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    /**
     * ConfigurationFinder constructor.
     *
     * @param ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * @inheritDoc
     */
    public function get(string $key, string $default = null): ?string
    {
        if ($config = $this->configurationService->find($key)) {
            return $config->value();
        }
        
        return $default;
    }
}