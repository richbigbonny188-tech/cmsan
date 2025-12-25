<?php
/* --------------------------------------------------------------
   PermissionService.php 2021-05-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Permission\App;

use Gambio\Admin\Modules\AccessGroup\Services\AccessGroupReadService;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleFactory;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleReadService;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleWriteService;
use Gambio\Core\Permission\Services\PermissionService as PermissionServiceInterface;

/**
 * Class PermissionService
 *
 * @package Gambio\Core\Permission\App
 */
class PermissionService implements PermissionServiceInterface
{
    private const MAIN_ADMIN_ID = 1;
    
    /**
     * @var AccessGroupReadService
     */
    private $groupService;
    
    /**
     * @var AccessRoleReadService
     */
    private $roleReadService;
    
    /**
     * @var AccessRoleWriteService
     */
    private $roleWriteService;
    
    /**
     * @var AccessRoleFactory
     */
    private $factory;
    
    
    /**
     * PermissionService constructor.
     *
     * @param AccessGroupReadService $groupService
     * @param AccessRoleReadService  $roleReadService
     * @param AccessRoleWriteService $roleWriteService
     * @param AccessRoleFactory      $factory
     */
    public function __construct(
        AccessGroupReadService $groupService,
        AccessRoleReadService $roleReadService,
        AccessRoleWriteService $roleWriteService,
        AccessRoleFactory $factory
    ) {
        $this->groupService     = $groupService;
        $this->roleReadService  = $roleReadService;
        $this->roleWriteService = $roleWriteService;
        $this->factory          = $factory;
    }
    
    
    /**
     * @inheritDoc
     */
    public function checkAdminPermission(
        int $adminId,
        string $action,
        string $groupItemType,
        string $groupItemDescriptor
    ): bool {
        if ($adminId === self::MAIN_ADMIN_ID) {
            return true;
        }
        
        $group     = $this->groupService->findAccessGroupByTypeAndDescriptor($groupItemType, $groupItemDescriptor);
        $roles     = $this->roleReadService->getAccessRolesByAdmin($adminId);
        $actionObj = $this->factory->createPermissionAction($action);
        $groupId   = $this->factory->createGroupId($group->id());
        foreach ($roles as $role) {
            if ($role->checkPermission($actionObj, $groupId)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * @inheritDoc
     */
    public function setAccessRolePermissionsForAccessGroup(
        int $accessRoleId,
        string $accessGroupItemType,
        string $accessGroupItemDescriptor,
        bool $readPermission,
        bool $writingPermission,
        bool $deletingPermission
    ): void {
        $group      = $this->groupService->getAccessGroupByTypeAndDescriptor($accessGroupItemType,
                                                                             $accessGroupItemDescriptor);
        $role       = $this->roleReadService->getAccessRoleById($accessRoleId);
        $permission = $this->factory->createPermission($group->id(),
                                                       $readPermission,
                                                       $writingPermission,
                                                       $deletingPermission);
        $role->updatePermission($permission);
        
        $this->roleWriteService->storeAccessRoles($role);
    }
}