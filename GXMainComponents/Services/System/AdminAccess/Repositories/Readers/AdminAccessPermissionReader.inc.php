<?php

/* --------------------------------------------------------------
   AdminAccessPermissionReader.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessPermissionReader
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Readers
 */
class AdminAccessPermissionReader implements AdminAccessPermissionReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var AdminAccessPermissionFactoryInterface
     */
    protected $permissionFactory;
    
    /**
     * @var string
     */
    protected $permissionTable;
    
    
    /**
     * AdminAccessPermissionReader constructor.
     *
     * @param CI_DB_query_builder                   $queryBuilder      Query builder.
     * @param AdminAccessPermissionFactoryInterface $permissionFactory Permission factory.
     */
    public function __construct(
        CI_DB_query_builder $queryBuilder,
        AdminAccessPermissionFactoryInterface $permissionFactory
    ) {
        $this->queryBuilder      = $queryBuilder;
        $this->permissionFactory = $permissionFactory;
        
        $this->permissionTable = 'admin_access_permissions';
    }
    
    
    /**
     * Returns an AdminAccessPermission object by given AdminAccessRole and AdminAccessGroup objects.
     *
     * @param AdminAccessRoleInterface  $accessRole  Role object.
     * @param AdminAccessGroupInterface $accessGroup Group object.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface|null Permission
     *                                                                                                   object or null
     *                                                                                                   if permission
     *                                                                                                   not found.
     */
    public function get(AdminAccessRoleInterface $accessRole, AdminAccessGroupInterface $accessGroup)
    {
        $permissionData = $this->queryBuilder->select()->from($this->permissionTable)->where([
                                                                                                 'admin_access_role_id'  => $accessRole->getId(),
                                                                                                 'admin_access_group_id' => $accessGroup->getId(),
                                                                                             ])->get()->row_array();
        
        return $this->_createPermissionObject($permissionData, $accessRole, $accessGroup);
    }
    
    
    /**
     * Returns an AdminAccessPermissionCollection by a given AdminAccessRole and AdminAccessGroupCollection object.
     *
     * @param AdminAccessRoleInterface   $accessRole      User role to find permission for.
     * @param AdminAccessGroupCollection $groupCollection Collection of all groups in system.
     *
     * @return AdminAccessPermissionCollection Permission collection.
     */
    public function getByGroupCollection(
        AdminAccessRoleInterface $accessRole,
        AdminAccessGroupCollection $groupCollection
    ) {
        $resultArray = [];
        
        /** @var \AdminAccessGroupInterface $group */
        foreach ($groupCollection->getArray() as $group) {
            $resultArray[] = $this->get($accessRole, $group);
        }
        
        return $this->permissionFactory->createAdminAccessPermissionCollection($resultArray);
    }
    
    
    /**
     * Returns an AdminAccessPermission objects, that gets its property values from permission data array.
     *
     * @param array|null                $permissionData Permission data.
     * @param AdminAccessRoleInterface  $role           Role needed to create permission object.
     * @param AdminAccessGroupInterface $group          Group needed to create permission object.
     *
     * @return AdminAccessPermissionPersistenceInterface|\AdminAccessPermissionPresentationInterface Permission object.
     */
    protected function _createPermissionObject(
        $permissionData,
        AdminAccessRoleInterface $role,
        AdminAccessGroupInterface $group
    ) {
        $permission = $this->permissionFactory->createAdminAccessPermission();
        
        $permission->setRole($role);
        $permission->setGroup($group);
        
        if ($permissionData !== null && $permissionData['reading_granted']) {
            $permission->grantReading();
        }
        
        if ($permissionData !== null && $permissionData['writing_granted']) {
            $permission->grantWriting();
        }
        
        if ($permissionData !== null && $permissionData['deleting_granted']) {
            $permission->grantDeleting();
        }
        
        return $permission;
    }
    
    
}
