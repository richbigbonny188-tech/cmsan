<?php
/* --------------------------------------------------------------
 ConfigExists.php 2020-09-21
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Filter\Types;

use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class ConfigExists
 * @package Gambio\Admin\Layout\Menu\Filter\Types
 * @codeCoverageIgnore
 */
class ConfigExists implements FilterInterface
{
    public const FILTER_METHOD = 'configExists';
    
    /**
     * @var ConfigurationService
     */
    private $service;
    
    
    /**
     * ConfigExists constructor.
     *
     * @param ConfigurationService $service
     */
    public function __construct(ConfigurationService $service)
    {
        $this->service = $service;
    }
    
    
    /**
     * @inheritDoc
     */
    public function check(FilterConditionArguments $condition): bool
    {
        foreach ($condition->args() as $arg) {
            if ($this->isEmpty($arg)) {
                return false;
            }
        }
        
        return true;
    }
    
    
    /**
     * Checks if the configuration key refers to an empty configuration value.
     *
     * @param string $configurationKey
     *
     * @return bool
     */
    private function isEmpty(string $configurationKey): bool
    {
        $namespaces = ['gm_configuration', 'configuration'];
        foreach ($namespaces as $namespace) {
            $key    = "$namespace/$configurationKey";
            $config = $this->service->find($key);
            
            if ($config && (!$config->value() || $config->value() === '')) {
                return true;
            }
        }
        
        return false;
    }
}
