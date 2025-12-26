<?php

/* --------------------------------------------------------------
   AdminAccessPermissionManager.inc.php 2018-01-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessPermissionManager
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Managers
 */
class AdminAccessPermissionManager implements AdminAccessPermissionManagerInterface
{
    /**
     * @var AdminAccessGroupReaderInterface
     */
    protected $groupReader;
    
    /**
     * @var AdminAccessRoleReaderInterface
     */
    protected $roleReader;
    
    /**
     * @var AdminAccessUserReaderInterface
     */
    protected $userReader;
    
    /**
     * @var AdminAccessRoleFactoryInterface
     */
    protected $roleFactory;
    
    
    /**
     * AdminAccessPermissionManager constructor.
     *
     * @param AdminAccessGroupReaderInterface $groupReader Group reader instance.
     * @param AdminAccessRoleFactoryInterface $roleFactory Role factory instance.
     * @param AdminAccessUserReaderInterface  $userReader  User reader instance.
     * @param \AdminAccessRoleReaderInterface $roleReader  Role reader instance.
     *
     */
    public function __construct(
        AdminAccessGroupReaderInterface $groupReader,
        AdminAccessRoleFactoryInterface $roleFactory,
        AdminAccessUserReaderInterface $userReader,
        AdminAccessRoleReaderInterface $roleReader
    ) {
        
        $this->groupReader = $groupReader;
        $this->roleFactory = $roleFactory;
        $this->userReader  = $userReader;
        $this->roleReader  = $roleReader;
    }
    
    
    /**
     * Checks the deleting permission for a controller.
     *
     * @param NonEmptyStringType $identifier The name of a controller to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check the permission for.
     *
     * @return bool True if customer has a deleting permission for the controller, false otherwise.
     */
    public function checkDeletingPermissionForController(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByController($identifier);
            
            return $user->checkDeletingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkDeletingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Checks the deleting permission for a page.
     *
     * @param NonEmptyStringType $identifier The name of a page to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a deleting permission for the page, false otherwise.
     */
    public function checkDeletingPermissionForPage(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByPage($identifier);
            
            return $user->checkDeletingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkDeletingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Checks the deleting permission for an ajax handler.
     *
     * @param NonEmptyStringType $identifier The name of an ajax handler to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a deleting permission for the ajax handler, false otherwise.
     */
    public function checkDeletingPermissionForAjaxHandler(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByAjaxHandler($identifier);
            
            return $user->checkDeletingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkDeletingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Checks the reading permission for a controller.
     *
     * @param NonEmptyStringType $identifier The name of a controller to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check the permission for.
     *
     * @return bool True if customer has a reading permission for the controller, false otherwise.
     */
    public function checkReadingPermissionForController(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByController($identifier);
            
            return $user->checkReadingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkReadingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Checks the reading permission for a page.
     *
     * @param NonEmptyStringType $identifier The name of a page to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a reading permission for the page, false otherwise.
     */
    public function checkReadingPermissionForPage(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByPage($identifier);
            
            return $user->checkReadingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkReadingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Checks the reading permission for an ajax handler.
     *
     * @param NonEmptyStringType $identifier The name of an ajax handler to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a reading permission for the ajax handler, false otherwise.
     */
    public function checkReadingPermissionForAjaxHandler(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByAjaxHandler($identifier);
            
            return $user->checkReadingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkReadingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Checks the writing permission for a controller.
     *
     * @param NonEmptyStringType $identifier The name of a controller to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check the permission for.
     *
     * @return bool True if customer has a writing permission for the controller, false otherwise.
     */
    public function checkWritingPermissionForController(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByController($identifier);
            
            return $user->checkWritingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkWritingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Checks the writing permission for a page.
     *
     * @param NonEmptyStringType $identifier The name of a page to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a writing permission for the page, false otherwise.
     */
    public function checkWritingPermissionForPage(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByPage($identifier);
            
            return $user->checkWritingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkWritingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Checks the writing permission for an ajax handler.
     *
     * @param NonEmptyStringType $identifier The name of an ajax handler to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a writing permission for the ajax handler, false otherwise.
     */
    public function checkWritingPermissionForAjaxHandler(NonEmptyStringType $identifier, IdType $customerId)
    {
        try {
            /** @var AdminAccessUserInterface $user */
            $user = $this->_getUser($customerId);
            /** @var AdminAccessGroupInterface $group */
            $group = $this->_getGroupByAjaxHandler($identifier);
            
            return $user->checkWritingPermission($group);
        } catch (GroupNotFoundException $e) {
            return $user->checkWritingPermissionForUnknownGroup();
        }
    }
    
    
    /**
     * Grants deleting permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessPermissionManager Returns same instance for chained method calls.
     *
     * @throws GroupNotFoundException
     */
    public function grantDeletingPermission(IdType $roleId, IdType $groupId)
    {
        $permission = $this->_getPermission($roleId, $groupId);
        $permission->grantDeleting();
        $permission->store();
        
        // Delegate granting to parent group
        try {
            $parentGroup = $this->groupReader->getById($groupId)->getParentGroup();
            $permission  = $this->_getPermission($roleId, new IdType($parentGroup->getId()));
            $permission->grantDeleting();
            $permission->store();
        } catch (GroupNotFoundException $e) {
        }
        
        return $this;
    }
    
    
    /**
     * Grants reading permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessPermissionManager Returns same instance for chained method calls.
     *
     * @throws GroupNotFoundException
     */
    public function grantReadingPermission(IdType $roleId, IdType $groupId)
    {
        $permission = $this->_getPermission($roleId, $groupId);
        $permission->grantReading();
        $permission->store();
        
        // Delegate granting to parent group
        try {
            $parentGroup = $this->groupReader->getById($groupId)->getParentGroup();
            $permission  = $this->_getPermission($roleId, new IdType($parentGroup->getId()));
            $permission->grantReading();
            $permission->store();
        } catch (GroupNotFoundException $e) {
        }
        
        return $this;
    }
    
    
    /**
     * Grants writing permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return \AdminAccessPermissionManager Returns same instance for chained method calls.
     *
     * @throws GroupNotFoundException
     */
    public function grantWritingPermission(IdType $roleId, IdType $groupId)
    {
        $permission = $this->_getPermission($roleId, $groupId);
        $permission->grantWriting();
        $permission->store();
        
        // Delegate granting to parent group
        try {
            $parentGroup = $this->groupReader->getById($groupId)->getParentGroup();
            $permission  = $this->_getPermission($roleId, new IdType($parentGroup->getId()));
            $permission->grantWriting();
            $permission->store();
        } catch (GroupNotFoundException $e) {
        }
        
        return $this;
    }
    
    
    /**
     * Removes deleting permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessPermissionManagerInterface Returns same instance for chained method calls.
     *
     * @throws GroupNotFoundException
     */
    public function removeDeletingPermission(IdType $roleId, IdType $groupId)
    {
        $permission = $this->_getPermission($roleId, $groupId);
        $permission->removeDeleting();
        $permission->store();
        
        // Delegate granting to parent group
        try {
            $childGroups = $this->groupReader->getById($groupId)->getChildren();
            if ($childGroups->count()) {
                /** @var \AdminAccessGroupInterface $childGroup */
                foreach ($childGroups->getArray() as $childGroup) {
                    $permission = $this->_getPermission($roleId, new IdType($childGroup->getId()));
                    $permission->removeDeleting();
                    $permission->store();
                }
            }
        } catch (GroupNotFoundException $e) {
        }
        
        return $this;
    }
    
    
    /**
     * Removes reading permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessPermissionManagerInterface Returns same instance for chained method calls.
     *
     * @throws GroupNotFoundException
     */
    public function removeReadingPermission(IdType $roleId, IdType $groupId)
    {
        $permission = $this->_getPermission($roleId, $groupId);
        $permission->removeReading();
        $permission->store();
        
        // Delegate granting to parent group
        try {
            $childGroups = $this->groupReader->getById($groupId)->getChildren();
            if ($childGroups->count()) {
                /** @var \AdminAccessGroupInterface $childGroup */
                foreach ($childGroups->getArray() as $childGroup) {
                    $permission = $this->_getPermission($roleId, new IdType($childGroup->getId()));
                    $permission->removeReading();
                    $permission->store();
                }
            }
        } catch (GroupNotFoundException $e) {
        }
        
        return $this;
    }
    
    
    /**
     * Removes writing permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessPermissionManagerInterface Returns same instance for chained method calls.
     *
     * @throws GroupNotFoundException
     */
    public function removeWritingPermission(IdType $roleId, IdType $groupId)
    {
        $permission = $this->_getPermission($roleId, $groupId);
        $permission->removeWriting();
        $permission->store();
        
        // Delegate granting to parent group
        try {
            $childGroups = $this->groupReader->getById($groupId)->getChildren();
            if ($childGroups->count()) {
                /** @var \AdminAccessGroupInterface $childGroup */
                foreach ($childGroups->getArray() as $childGroup) {
                    $permission = $this->_getPermission($roleId, new IdType($childGroup->getId()));
                    $permission->removeWriting();
                    $permission->store();
                }
            }
        } catch (GroupNotFoundException $e) {
        }
        
        return $this;
    }
    
    
    /**
     * Returns a collection of all permissions by a given role ID.
     *
     * @param IdType $roleId Role ID.
     *
     * @return AdminAccessPermissionCollection Permission collection object with all role permissions.
     */
    public function getPermissionsByRoleId(IdType $roleId)
    {
        $groupCollection = $this->groupReader->getAll();
        
        $adminAccessRole = $this->roleReader->getById($roleId);
        
        return $adminAccessRole->getPermissionsByGroupCollection($groupCollection);
    }
    
    
    /**
     * Returns a collection of permissions of a role by a given group collection.
     *
     * @param IdType                     $roleId          Role ID.
     * @param AdminAccessGroupCollection $groupCollection Group collection.
     *
     * @return AdminAccessPermissionCollection Permission collection object with all role permissions.
     */
    public function getPermissionsByGroupCollection(IdType $roleId, AdminAccessGroupCollection $groupCollection)
    {
        $adminAccessRole = $this->roleReader->getById($roleId);
        
        return $adminAccessRole->getPermissionsByGroupCollection($groupCollection);
    }
    
    
    /**
     * Returns an user by a given customer ID.
     *
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessUserInterface User object.
     */
    protected function _getUser(IdType $customerId)
    {
        return $this->userReader->getByCustomerId($customerId);
    }
    
    
    /**
     * Returns a group by a given identifier.
     *
     * @param NonEmptyStringType $identifier Identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    protected function _getGroupByPage(NonEmptyStringType $identifier)
    {
        return $this->groupReader->getByPage($identifier);
    }
    
    
    /**
     * Returns a group by a given identifier.
     *
     * @param NonEmptyStringType $identifier Identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    protected function _getGroupByAjaxHandler(NonEmptyStringType $identifier)
    {
        return $this->groupReader->getByAjaxHandler($identifier);
    }
    
    
    /**
     * Returns a group by a given identifier.
     *
     * @param NonEmptyStringType $identifier Identifier.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws GroupNotFoundException
     */
    protected function _getGroupByController(NonEmptyStringType $identifier)
    {
        return $this->groupReader->getByController($identifier);
    }
    
    
    /**
     * Returns a permission by a given role ID and group ID.
     *
     * If no permission could be found, a new permission will be returned.
     *
     * @param IdType $roleId  Role ID.
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessPermissionPersistenceInterface|AdminAccessPermissionPresentationInterface Permission object.
     *
     * @throws GroupNotFoundException
     */
    protected function _getPermission(IdType $roleId, IdType $groupId)
    {
        $group = $this->groupReader->getById($groupId);
        $role  = $this->roleReader->getById($roleId);
        
        return $role->getPermissionByGroup($group);
    }
}
