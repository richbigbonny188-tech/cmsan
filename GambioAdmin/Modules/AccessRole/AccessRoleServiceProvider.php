<?php
/* --------------------------------------------------------------
   AccessRoleServiceProvider.php 2020-10-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole;

use Doctrine\DBAL\Connection;
use Gambio\Admin\Modules\AccessRole\App\Data\AccessRoleMapper;
use Gambio\Admin\Modules\AccessRole\App\Data\AccessRoleReader;
use Gambio\Admin\Modules\AccessRole\App\Data\AccessRoleRepository;
use Gambio\Admin\Modules\AccessRole\App\Data\AccessRoleWriter;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleFactory;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleReadService;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;
use Gambio\Core\Language\Services\LanguageService;
use Gambio\Core\Permission\Services\PermissionService;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class AccessRoleServiceProvider
 *
 * @package Gambio\Admin\Modules\AccessRole
 */
class AccessRoleServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            AccessRoleReadService::class,
            AccessRoleWriteService::class,
            AccessRoleFactory::class,
            PermissionService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->application->registerShared(AccessRoleMapper::class)->addArgument(AccessRoleFactory::class);
        
        $this->application->registerShared(AccessRoleReader::class)->addArgument(Connection::class);
        
        $this->application->registerShared(AccessRoleWriter::class)
            ->addArgument(Connection::class)
            ->addArgument(LanguageService::class);
        
        $this->application->registerShared(AccessRoleRepository::class)
            ->addArgument(AccessRoleMapper::class)
            ->addArgument(AccessRoleReader::class)
            ->addArgument(AccessRoleWriter::class)
            ->addArgument(EventDispatcherInterface::class);
        
        $this->application->registerShared(AccessRoleFactory::class);
        
        $this->application->registerShared(AccessRoleReadService::class, App\AccessRoleReadService::class)
            ->addArgument(AccessRoleRepository::class)
            ->addArgument(AccessRoleFactory::class);
        
        $this->application->registerShared(AccessRoleWriteService::class, App\AccessRoleWriteService::class)
            ->addArgument(AccessRoleRepository::class)
            ->addArgument(AccessRoleFactory::class);
    }
}