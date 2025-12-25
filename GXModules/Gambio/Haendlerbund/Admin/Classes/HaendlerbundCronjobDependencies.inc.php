<?php
/* --------------------------------------------------------------
   HaendlerbundCronjobDependencies.inc.php 2022-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Gambio\Haendlerbund\Admin\Classes\HaendlerbundConfigurationFinder;
use GXModules\Gambio\Haendlerbund\Admin\Classes\BatixService;
use GXModules\Gambio\Haendlerbund\Admin\Classes\HaendlerbundUpdateService;
use GXModules\Gambio\Haendlerbund\Admin\Classes\LegaltextUpdateService;

class HaendlerbundCronjobDependencies extends AbstractCronjobDependencies
{
    
    /**
     * @return array
     */
    public function getDependencies()
    {
        $configurationFinder       = LegacyDependencyContainer::getInstance()->get(ConfigurationFinder::class);
        $hbConfigurationFinder     = new HaendlerbundConfigurationFinder($configurationFinder);
        $batixService              = new BatixService($hbConfigurationFinder);
        $connection                = LegacyDependencyContainer::getInstance()->get(Connection::class);
        $configurationService      = LegacyDependencyContainer::getInstance()->get(ConfigurationService::class);
        $languageReadService = LegacyDependencyContainer::getInstance()->get(LanguageReadService::class);
        $legaltextUpdateService    = new LegaltextUpdateService($connection, $configurationService, $languageReadService);
        $haendlerbundUpdateService = new HaendlerbundUpdateService($batixService, $legaltextUpdateService, $hbConfigurationFinder);
        
        return [
            'HaendlerbundConfigurationFinder' => $hbConfigurationFinder,
            'HaendlerbundUpdateService'       => $haendlerbundUpdateService,
        ];
    }
}