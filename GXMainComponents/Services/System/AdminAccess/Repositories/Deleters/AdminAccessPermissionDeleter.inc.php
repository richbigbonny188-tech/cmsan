<?php

/* --------------------------------------------------------------
   AdminAccessPermissionDeleter.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessPermissionDeleter
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Deleters
 */
class AdminAccessPermissionDeleter implements AdminAccessPermissionDeleterInterface
{
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected $permissionTable;
    
    
    /**
     * AdminAccessPermissionDeleter constructor.
     *
     * @param CI_DB_query_builder $queryBuilder Query builder.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder    = $queryBuilder;
        $this->permissionTable = 'admin_access_permissions';
    }
    
    
    /**
     * Deletes a permission by given AdminAccessRole and AdminAccessGroup objects.
     *
     * @param AdminAccessRoleInterface  $accessRole  Role object.
     * @param AdminAccessGroupInterface $accessGroup Group object.
     *
     * @return AdminAccessPermissionDeleterInterface Returns same instance for chained method calls.
     */
    public function delete(AdminAccessRoleInterface $accessRole, AdminAccessGroupInterface $accessGroup)
    {
        $this->deleteByIds(new IdType($accessRole->getId()), new IdType($accessGroup->getId()));
        
        return $this;
    }
    
    
    /**
     * Deletes a permission by given AdminAccessRole id and AdminAccessGroup id.
     *
     * @param IdType $roleId  Role ID.
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessPermissionDeleterInterface Returns same instance for chained method calls.
     */
    public function deleteByIds(IdType $roleId, IdType $groupId)
    {
        $where = [
            'admin_access_role_id'  => $roleId->asInt(),
            'admin_access_group_id' => $groupId->asInt(),
        ];
        $this->queryBuilder->delete($this->permissionTable, $where);
        
        return $this;
    }
}
