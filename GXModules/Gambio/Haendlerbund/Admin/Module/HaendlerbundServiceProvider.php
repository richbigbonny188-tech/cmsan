<?php
/* --------------------------------------------------------------
   HaendlerbundServiceProvider.php 2022-03-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace GXModules\Gambio\Haendlerbund\Admin\Module;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\Language\Services\LanguageReadService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationFinder;
use Gambio\Core\Configuration\Services\ConfigurationService;
use GXModules\Gambio\Haendlerbund\Admin\App\Actions\SaveConfiguration;
use GXModules\Gambio\Haendlerbund\Admin\App\Actions\ShowConfiguration;
use GXModules\Gambio\Haendlerbund\Admin\App\Actions\UpdateNow;
use GXModules\Gambio\Haendlerbund\Admin\Classes\BatixService;
use GXModules\Gambio\Haendlerbund\Admin\Classes\HaendlerbundConfigurationFinder;
use GXModules\Gambio\Haendlerbund\Admin\Classes\HaendlerbundUpdateService;
use GXModules\Gambio\Haendlerbund\Admin\Classes\LegaltextUpdateService;

class HaendlerbundServiceProvider extends AbstractServiceProvider
{
    
    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            BatixService::class,
            LegaltextUpdateService::class,
            HaendlerbundUpdateService::class,
            ShowConfiguration::class,
            SaveConfiguration::class,
            UpdateNow::class,
        ];
    }
    
    
    /**
     *
     */
    public function register(): void
    {
        $this->application->registerShared(HaendlerbundConfigurationFinder::class)
            ->addArgument(ConfigurationFinder::class);
        
        $this->application->registerShared(BatixService::class)->addArgument(HaendlerbundConfigurationFinder::class);
        
        $this->application->registerShared(LegaltextUpdateService::class)
            ->addArgument(Connection::class)
            ->addArgument(ConfigurationService::class)
            ->addArgument(LanguageReadService::class);
        
        $this->application->registerShared(ShowConfiguration::class)
            ->addArgument(HaendlerbundConfigurationFinder::class);
        
        $this->application->registerShared(SaveConfiguration::class)->addArgument(ConfigurationService::class);
        
        $this->application->registerShared(HaendlerbundUpdateService::class)
            ->addArgument(BatixService::class)
            ->addArgument(LegaltextUpdateService::class)
            ->addArgument(HaendlerbundConfigurationFinder::class);
        
        $this->application->registerShared(UpdateNow::class)->addArgument(HaendlerbundUpdateService::class);
    }
}
