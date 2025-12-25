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

namespace Gambio\Core\Permission\Services;

use Gambio\Admin\Modules\AccessGroup\Services\Exceptions\AccessGroupDoesNotExistException;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\AccessRoleDoesNotExistException;
use Gambio\Admin\Modules\AccessRole\Services\Exceptions\StorageOfAccessRolesFailedException;

/**
 * Interface PermissionService
 *
 * @package Gambio\Core\Permission\Services
 */
interface PermissionService
{
    /**
     * Checks the permission of an admin for a access group item, based on the provided admin ID, action (read,
     * write or delete), group item type and descriptor.
     *
     * @param int    $adminId
     * @param string $action
     * @param string $groupItemType
     * @param string $groupItemDescriptor
     *
     * @return bool
     */
    public function checkAdminPermission(
        int $adminId,
        string $action,
        string $groupItemType,
        string $groupItemDescriptor
    ): bool;
    
    
    /**
     * Set role permission for a group based on its type and descriptor.
     *
     * @param int    $accessRoleId
     * @param string $accessGroupItemType
     * @param string $accessGroupItemDescriptor
     * @param bool   $readPermission
     * @param bool   $writingPermission
     * @param bool   $deletingPermission
     *
     * @throws AccessGroupDoesNotExistException
     * @throws AccessRoleDoesNotExistException
     * @throws StorageOfAccessRolesFailedException
     */
    public function setAccessRolePermissionsForAccessGroup(
        int $accessRoleId,
        string $accessGroupItemType,
        string $accessGroupItemDescriptor,
        bool $readPermission,
        bool $writingPermission,
        bool $deletingPermission
    ): void;
}