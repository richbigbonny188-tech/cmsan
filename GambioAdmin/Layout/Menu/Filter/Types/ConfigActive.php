<?php
/* --------------------------------------------------------------
 ConfigActive.php 2020-01-31
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 31 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Admin\Layout\Menu\Filter\Types;

use Gambio\Core\Configuration\Model\Interfaces\Configuration;
use Gambio\Core\Configuration\Services\ConfigurationService;
use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;

/**
 * Class ConfigActive
 * @package Gambio\Admin\Layout\Menu\Filter\Types
 *
 * @codeCoverageIgnore
 */
class ConfigActive implements FilterInterface
{
    public const FILTER_METHOD = 'configActive';
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    /**
     * ConfigKey constructor.
     *
     * @param ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }
    
    
    /**
     * Evaluates to true if the first argument is a configuration
     * item that represents a "true - boolean" value.
     *
     * @param FilterConditionArguments $condition
     *
     * @return bool
     */
    public function check(FilterConditionArguments $condition): bool
    {
        $args = $condition->args();
        if (!array_key_exists(0, $args)) {
            return false;
        }
        
        $key              = $args[0];
        $configurationKey = "configuration/{$key}";
        
        $configuration = $this->configurationService->find($configurationKey);
        if (!$configuration) {
            $configurationKey = "gm_configuration/{$key}";
            $configuration    = $this->configurationService->find($configurationKey);
        }
        
        if (!$configuration) {
            $configuration = $this->configurationService->find($key);
        }
        
        return $this->isTrue($configuration);
    }
    
    
    /**
     * Evaluates the configuration item.
     *
     * This function performs several checks to determine if the given configuration
     * should represent a "true - boolean" value. Returns false if it is not the case.
     *
     * @param Configuration|null $configuration
     *
     * @return bool
     */
    private function isTrue(?Configuration $configuration): bool
    {
        if (!$configuration) {
            return false;
        }
        
        $value = $configuration->value();
        if (!$value) {
            return false;
        }
        
        return $value === 'true' || $value === 'TRUE' || $value === 'True' || $value === '1';
    }
}