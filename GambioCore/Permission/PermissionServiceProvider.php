<?php
/* --------------------------------------------------------------
   PermissionServiceProvider.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Permission;

use Gambio\Admin\Modules\AccessGroup\AccessGroupServiceProvider;
use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupReadService;
use Gambio\Admin\Modules\AccessRole\AccessRoleServiceProvider;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleFactory;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleReadService;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleWriteService;
use Gambio\Core\Application\DependencyInjection\AbstractServiceProvider;

/**
 * Class PermissionServiceProvider
 *
 * @package Gambio\Core\Permission
 */
class PermissionServiceProvider extends AbstractServiceProvider
{
    /**
     * @inheritDoc
     */
    public function provides(): array
    {
        return [
            Services\PermissionService::class,
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        if ($this->application->has(AccessGroupReadService::class) === false) {
            $this->application->registerProvider(AccessGroupServiceProvider::class);
        }
        
        if ($this->application->has(AccessRoleReadService::class) === false
            || $this->application->has(AccessRoleFactory::class) === false) {
            $this->application->registerProvider(AccessRoleServiceProvider::class);
        }
        
        $this->application->registerShared(Services\PermissionService::class, App\PermissionService::class)
            ->addArgument(AccessGroupReadService::class)
            ->addArgument(AccessRoleReadService::class)
            ->addArgument(AccessRoleWriteService::class)
            ->addArgument(AccessRoleFactory::class);
    }
}