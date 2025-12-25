<?php
/* --------------------------------------------------------------
   RedirectRulesProvider.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\RedirectRules;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\RedirectRules\Factories\RedirectServiceFactory;
use Gambio\Admin\Modules\RedirectRules\Repository\RedirectRepository;
use Gambio\Admin\Modules\RedirectRules\Repository\RedirectRepositoryInterface;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Configuration\Services\ConfigurationService;

class RedirectRulesServiceProvider extends AbstractServiceProvider
{
    
    public function provides(): array
    {
        return [
            RedirectRulesAdminController::class,
            RedirectServiceFactory::class,
        ];
    }
    
    
    public function register(): void
    {
        $this->application->registerShared(RedirectRulesAdminController::class)
            ->addArgument(RedirectServiceFactory::class)
            ->addArgument(ConfigurationService::class);
        
        $this->application->registerShared(RedirectRepositoryInterface::class, RedirectRepository::class)
            ->addArgument(Connection::class);
        
        $this->application->registerShared(RedirectRepository::class)->addArgument(Connection::class);
        
        $this->application->registerShared(RedirectServiceFactory::class)
            ->addArgument(RedirectRepositoryInterface::class);
    }
}
