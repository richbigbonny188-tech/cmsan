<?php
/* --------------------------------------------------------------
 HubMenuFilterOneConfigActive.php 2021-05-06
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

namespace GXModules\Gambio\Hub\Admin\Filter;

use Gambio\Admin\Layout\Menu\Filter\FilterConditionArguments;
use Gambio\Admin\Layout\Menu\Filter\FilterInterface;
use Gambio\Core\Configuration\Model\Configuration;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class HubMenuFilterOneConfigActive
 *
 */
class HubMenuFilterOneConfigActive implements FilterInterface
{
    public const FILTER_METHOD = 'oneConfigActive';
    
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    
    
    /**Gambio\Admin\Layout\Menu\Filter\FilterInterface
     * ConfigKey constructor.
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
    public function check(FilterConditionArguments $condition): bool
    {
        foreach ($condition->args() as $arg) {
            if ($this->checkConfigActive($arg)) {
                return  true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Evaluates to true if the first argument is a configuration
     * item that represents a "true - boolean" value.
     *
     * @param string $arg
     *
     * @return bool
     */
    public function checkConfigActive(string $arg): bool
    {
        $configurationKey = "configuration/{$arg}";
        
        $configuration = $this->configurationService->find($configurationKey);
        
        if (!$configuration) {
            $configurationKey = "gm_configuration/{$arg}";
            $configuration    = $this->configurationService->find($configurationKey);
        }
        
        if (!$configuration) {
            $configuration = $this->configurationService->find($arg);
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