<?php
/* --------------------------------------------------------------
   AccessRoleFactory.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\Services;

use Gambio\Admin\Modules\AccessRole\Model\AccessRole;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoleIds;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoles;
use Gambio\Admin\Modules\AccessRole\Model\Collections\Permissions;
use Gambio\Admin\Modules\AccessRole\Model\Entities\Permission;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleDescriptions;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleNames;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AdminId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\GroupId;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\PermissionAction;

/**
 * Class AccessRoleFactory
 *
 * @package Gambio\Admin\Modules\AccessRole\Services
 */
class AccessRoleFactory
{
    /**
     * Creates and returns an access role.
     *
     * @param int         $id
     * @param array       $names
     * @param array       $descriptions
     * @param Permissions $permissions
     * @param int         $sortOrder
     * @param bool        $isProtected
     *
     * @return AccessRole
     */
    public function createAccessRole(
        int $id,
        array $names,
        array $descriptions,
        Permissions $permissions,
        int $sortOrder,
        bool $isProtected
    ): AccessRole {
        return AccessRole::create($this->createAccessRoleId($id),
                                  $this->createAccessRoleNames($names),
                                  $this->createAccessRoleDescriptions($descriptions),
                                  $permissions,
                                  $sortOrder,
                                  $isProtected);
    }
    
    
    /**
     * Creates and returns a collection of access roles.
     *
     * @param AccessRole ...$roles
     *
     * @return AccessRoles
     */
    public function createAccessRoles(AccessRole ...$roles): AccessRoles
    {
        return AccessRoles::create(...$roles);
    }
    
    
    /**
     * Creates and returns an access role ID.
     *
     * @param int $id
     *
     * @return AccessRoleId
     */
    public function createAccessRoleId(int $id): AccessRoleId
    {
        return AccessRoleId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of access role IDs.
     *
     * @param AccessRoleId ...$ids
     *
     * @return AccessRoleIds
     */
    public function createAccessRoleIds(AccessRoleId ...$ids): AccessRoleIds
    {
        return AccessRoleIds::create(...$ids);
    }
    
    
    /**
     * Creates and returns a permission.
     *
     * @param int  $groupId
     * @param bool $readingGranted
     * @param bool $writingGranted
     * @param bool $deletingGranted
     *
     * @return Permission
     */
    public function createPermission(
        int $groupId,
        bool $readingGranted,
        bool $writingGranted,
        bool $deletingGranted
    ): Permission {
        return Permission::create($this->createGroupId($groupId), $readingGranted, $writingGranted, $deletingGranted);
    }
    
    
    /**
     * Creates and returns a collection of permissions.
     *
     * @param Permission ...$permissions
     *
     * @return Permissions
     */
    public function createPermissions(Permission ...$permissions): Permissions
    {
        return Permissions::create(...$permissions);
    }
    
    
    /**
     * Creates and returns an admin ID.
     *
     * @param int $id
     *
     * @return AdminId
     */
    public function createAdminId(int $id): AdminId
    {
        return AdminId::create($id);
    }
    
    
    /**
     * Creates and returns a group ID.
     *
     * @param int $id
     *
     * @return GroupId
     */
    public function createGroupId(int $id): GroupId
    {
        return GroupId::create($id);
    }
    
    
    /**
     * Creates and returns a permission action.
     *
     * @param string $action
     *
     * @return PermissionAction
     */
    public function createPermissionAction(string $action): PermissionAction
    {
        return PermissionAction::create($action);
    }
    
    
    /**
     * Creates and returns a collection of access role names.
     *
     * @param array $names
     *
     * @return AccessRoleNames
     */
    public function createAccessRoleNames(array $names): AccessRoleNames
    {
        return AccessRoleNames::create($names);
    }
    
    
    /**
     * Creates and returns a collection of access role descriptions.
     *
     * @param array $descriptions
     *
     * @return AccessRoleDescriptions
     */
    public function createAccessRoleDescriptions(array $descriptions): AccessRoleDescriptions
    {
        return AccessRoleDescriptions::create($descriptions);
    }
}