<?php
/* --------------------------------------------------------------
   DefaultServerConfiguration.php 2022-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Application\Bootstrapper;

use Gambio\Core\Application\Application;
use Gambio\Core\Application\Bootstrapper;
use Gambio\Core\Configuration\Services\ConfigurationService;

/**
 * Class SetDefaultServerConfiguration
 *
 * @package Gambio\Core\Application\Bootstrapper
 */
class DefaultServerConfiguration implements Bootstrapper
{
    /**
     * @inheritDoc
     */
    public function boot(Application $application): void
    {
        $this->setDefaultErrorReporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);
        $this->setDefaultTimezone($application, 'Europe/Berlin');
        $this->setDefaultMemoryLimit(128);
    }
    
    
    /**
     * @param int $errorReporting
     */
    private function setDefaultErrorReporting(int $errorReporting): void
    {
        @error_reporting($errorReporting);
    }
    
    
    /**
     * @param Application $application
     * @param string      $timeZone
     */
    private function setDefaultTimezone(Application $application, string $timeZone): void
    {
        /** @var ConfigurationService $configurationService */
        $configurationService = $application->get(ConfigurationService::class);
        
        $defaultTimezone = $configurationService->find('configuration/DATE_TIMEZONE');
        $defaultTimezone = ($defaultTimezone !== null) ? $defaultTimezone->value() : $timeZone;
        
        @date_default_timezone_set($defaultTimezone);
    }
    
    
    /**
     * @param int $neededLimitInMegaBytes
     */
    private function setDefaultMemoryLimit(int $neededLimitInMegaBytes): void
    {
        if (!function_exists('ini_get') || !function_exists('ini_set')) {
            return;
        }
        
        $serverMemoryLimitInBytes = $this->getMemoryLimitInBytes();
        if ($serverMemoryLimitInBytes < $neededLimitInMegaBytes * 1024 * 1024 && $serverMemoryLimitInBytes !== -1) {
            @ini_set('memory_limit', $neededLimitInMegaBytes . 'M');
        }
    }
    
    
    /**
     * @return int
     */
    private function getMemoryLimitInBytes(): int
    {
        $value = @ini_get('memory_limit');
        if (empty($value)) {
            return 0;
        }
        
        if (is_numeric($value)) { # unlimited (-1) or values that are already configured in bytes
        
            return (int)$value;
        }
        
        $unit = substr($value, -1);
        $unitValue = (int)substr($value, 0, -1);
        switch (strtolower($unit)) {
            case 'g':
                $unitValue *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $unitValue *= 1024 * 1024;
                break;
            case 'k':
                $unitValue *= 1024;
                break;
        }
        
        return $unitValue;
    }
}
