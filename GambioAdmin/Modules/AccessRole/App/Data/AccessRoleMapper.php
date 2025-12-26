<?php
/* --------------------------------------------------------------
   AccessRoleMapper.php 2020-10-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\AccessRole\App\Data;

use Gambio\Admin\Modules\AccessRole\Model\AccessRole;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoleIds;
use Gambio\Admin\Modules\AccessRole\Model\Collections\AccessRoles;
use Gambio\Admin\Modules\AccessRole\Model\Entities\Permission;
use Gambio\Admin\Modules\AccessRole\Model\ValueObjects\AccessRoleId;
use Gambio\Admin\Modules\AccessRole\Services\AccessRoleFactory;

/**
 * Class AccessRoleMapper
 *
 * @package Gambio\Admin\Modules\AccessRole\App\Data
 */
class AccessRoleMapper
{
    /**
     * @var AccessRoleFactory
     */
    private $factory;
    
    
    /**
     * AccessRoleMapper constructor.
     *
     * @param AccessRoleFactory $factory
     */
    public function __construct(AccessRoleFactory $factory)
    {
        $this->factory = $factory;
    }
    
    
    /**
     * @param array $permissionData
     *
     * @return Permission
     */
    private function mapRolePermission(array $permissionData): Permission
    {
        return $this->factory->createPermission($permissionData['groupId'],
                                                $permissionData['readingGranted'],
                                                $permissionData['writingGranted'],
                                                $permissionData['deletingGranted']);
    }
    
    
    /**
     * @param array $roleData
     *
     * @return AccessRole
     */
    public function mapAccessRole(array $roleData): AccessRole
    {
        $rolePermissions = array_map([$this, 'mapRolePermission'], $roleData['permissions']);
        
        return $this->factory->createAccessRole($roleData['id'],
                                                $roleData['names'],
                                                $roleData['descriptions'],
                                                $this->factory->createPermissions(...$rolePermissions),
                                                $roleData['sortOrder'],
                                                $roleData['isProtected']);
    }
    
    
    /**
     * @param array $rolesData
     *
     * @return AccessRoles
     */
    public function mapAccessRoles(array $rolesData): AccessRoles
    {
        $roles = array_map([$this, 'mapAccessRole'], $rolesData);
        
        return $this->factory->createAccessRoles(...$roles);
    }
    
    
    /**
     * @param int $roleId
     *
     * @return AccessRoleId
     */
    public function mapAccessRoleId(int $roleId): AccessRoleId
    {
        return $this->factory->createAccessRoleId($roleId);
    }
    
    
    /**
     * @param array $roleIds
     *
     * @return AccessRoleIds
     */
    public function mapAccessRoleIds(array $roleIds): AccessRoleIds
    {
        $roleIds = array_map([$this, 'mapAccessRoleId'], $roleIds);
        
        return $this->factory->createAccessRoleIds(...$roleIds);
    }
}