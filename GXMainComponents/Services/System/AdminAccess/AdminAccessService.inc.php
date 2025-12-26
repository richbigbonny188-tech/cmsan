<?php

/* --------------------------------------------------------------
   AdminAccessService.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessService
 *
 * @category   System
 * @package    AdminAccess
 */
class AdminAccessService implements AdminAccessServiceInterface
{
    /**
     * @var AdminAccessSettingsInterface
     */
    protected $settings;
    
    /**
     * @var AdminAccessPermissionManagerInterface
     */
    protected $permissionManager;
    
    /**
     * @var AdminAccessRoleManagerInterface
     */
    protected $roleManager;
    
    /**
     * @var AdminAccessUserManagerInterface
     */
    protected $userManager;
    
    /**
     * @var AdminAccessGroupManagerInterface
     */
    protected $groupManager;
    
    
    /**
     * AdminAccessService constructor.
     *
     * @param AdminAccessSettingsInterface          $settings          Settings.
     * @param AdminAccessPermissionManagerInterface $permissionManager Permission Manager.
     * @param AdminAccessRoleManagerInterface       $roleManager       Role Manager.
     * @param AdminAccessUserManagerInterface       $userManager       User Manager.
     * @param AdminAccessGroupManagerInterface      $groupManager      Group Manager.
     */
    public function __construct(
        AdminAccessSettingsInterface $settings,
        AdminAccessPermissionManagerInterface $permissionManager,
        AdminAccessRoleManagerInterface $roleManager,
        AdminAccessUserManagerInterface $userManager,
        AdminAccessGroupManagerInterface $groupManager
    ) {
        $this->settings          = $settings;
        $this->permissionManager = $permissionManager;
        $this->roleManager       = $roleManager;
        $this->userManager       = $userManager;
        $this->groupManager      = $groupManager;
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkDeletingPermissionForController($identifier, $customerId);
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkDeletingPermissionForPage($identifier, $customerId);
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkDeletingPermissionForAjaxHandler($identifier, $customerId);
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkReadingPermissionForController($identifier, $customerId);
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkReadingPermissionForPage($identifier, $customerId);
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkReadingPermissionForAjaxHandler($identifier, $customerId);
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkWritingPermissionForController($identifier, $customerId);
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkWritingPermissionForPage($identifier, $customerId);
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
        if ($customerId->asInt() === $this->settings->getMainAdminId()) {
            return true;
        }
        
        return $this->permissionManager->checkWritingPermissionForAjaxHandler($identifier, $customerId);
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
        return $this->permissionManager->getPermissionsByRoleId($roleId);
    }
    
    
    /**
     * Adds a role to a user by a given role and customer ID.
     *
     * @param IdType $roleId     Role ID.
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function addRoleToUserByCustomerId(IdType $roleId, IdType $customerId)
    {
        $this->userManager->addRoleToUserByCustomerId($roleId, $customerId);
        
        return $this;
    }
    
    
    /**
     * Removes a role from a user by given role and customer ID.
     *
     * @param IdType $roleId     Role ID.
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function removeRoleFromUserByCustomerId(IdType $roleId, IdType $customerId)
    {
        $this->userManager->removeRoleFromUserByCustomerId($roleId, $customerId);
        
        return $this;
    }
    
    
    /**
     * Returns all roles of certain user by a given user ID.
     *
     * @param IdType $id User ID.
     *
     * @return AdminAccessRoleCollection Collection of all roles that certain user has.
     */
    public function getRolesByCustomerId(IdType $id)
    {
        return $this->userManager->getRolesByCustomerId($id);
    }
    
    
    /**
     * Grants deleting permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function grantDeletingPermissionToRole(IdType $groupId, IdType $roleId)
    {
        $this->permissionManager->grantDeletingPermission($roleId, $groupId);
        
        return $this;
    }
    
    
    /**
     * Removes deleting permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function removeDeletingPermissionFromRole(IdType $groupId, IdType $roleId)
    {
        $this->permissionManager->removeDeletingPermission($roleId, $groupId);
        
        return $this;
    }
    
    
    /**
     * Grants reading permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function grantReadingPermissionToRole(IdType $groupId, IdType $roleId)
    {
        $this->permissionManager->grantReadingPermission($roleId, $groupId);
        
        return $this;
    }
    
    
    /**
     * Removes reading permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function removeReadingPermissionFromRole(IdType $groupId, IdType $roleId)
    {
        $this->permissionManager->removeReadingPermission($roleId, $groupId);
        
        return $this;
    }
    
    
    /**
     * Grants writing permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function grantWritingPermissionToRole(IdType $groupId, IdType $roleId)
    {
        $this->permissionManager->grantWritingPermission($roleId, $groupId);
        
        return $this;
    }
    
    
    /**
     * Removes writing permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function removeWritingPermissionFromRole(IdType $groupId, IdType $roleId)
    {
        $this->permissionManager->removeWritingPermission($roleId, $groupId);
        
        return $this;
    }
    
    
    /**
     * Creates a new role.
     *
     * @param KeyValueCollection $name                    Collection with the role names. Index of a role name must be
     *                                                    his language code.
     * @param KeyValueCollection $description             Collection with the role descriptions. Index of a role name
     *                                                    must be his language code.
     * @param IntType            $sortOrder               Roles sort order.
     * @param BoolType           $unknownReadingGranted   Value of the reading permission for unknown groups.
     * @param BoolType           $unknownWritingGranted   Value of the writing permission for unknown groups.
     * @param BoolType           $unknownDeletingGranted  Value of the deleting permission for unknown groups.
     *
     * @return AdminAccessRoleInterface Returns the create role.
     */
    public function createNewRole(
        KeyValueCollection $name,
        KeyValueCollection $description,
        IntType $sortOrder,
        BoolType $unknownReadingGranted,
        BoolType $unknownWritingGranted,
        BoolType $unknownDeletingGranted
    ) {
        return $this->roleManager->createNewRole($name,
                                                 $description,
                                                 $sortOrder,
                                                 $unknownReadingGranted,
                                                 $unknownWritingGranted,
                                                 $unknownDeletingGranted);
    }
    
    
    /**
     * Updates a role by a given role ID.
     *
     * @param IdType             $roleId                  Role ID to remove permission from.
     * @param KeyValueCollection $newName                 Collection with the new role names. Index of a role name must
     *                                                    be his language code.
     * @param KeyValueCollection $newDescription          Collection with the new role descriptions. Index of a role
     *                                                    name must be his language code.
     * @param IntType            $newSortOrder            New roles sort order.
     * @param BoolType           $unknownReadingGranted   Value of the reading permission for unknown groups.
     * @param BoolType           $unknownWritingGranted   Value of the writing permission for unknown groups.
     * @param BoolType           $unknownDeletingGranted  Value of the deleting permission for unknown groups.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function updateRoleById(
        IdType $roleId,
        KeyValueCollection $newName,
        KeyValueCollection $newDescription,
        IntType $newSortOrder,
        BoolType $unknownReadingGranted,
        BoolType $unknownWritingGranted,
        BoolType $unknownDeletingGranted
    ) {
        $this->roleManager->updateRole($roleId,
                                       $newName,
                                       $newDescription,
                                       $newSortOrder,
                                       $unknownReadingGranted,
                                       $unknownWritingGranted,
                                       $unknownDeletingGranted);
        
        return $this;
    }
    
    
    /**
     * Deletes role by a given role ID.
     *
     * @param IdType $roleId ID of the role that should be deleted.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function deleteRoleById(IdType $roleId)
    {
        $this->roleManager->deleteRole($roleId);
        
        return $this;
    }
    
    
    /**
     * Returns a collection of all roles.
     *
     * @return AdminAccessRoleCollection Role collection with all available roles.
     */
    public function getAllRoles()
    {
        return $this->roleManager->getAllRoles();
    }
    
    
    /**
     * Deletes an admin access user by a given customer ID.
     *
     * @param IdType $customerId ID of the user that should be deleted.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function deleteUserByCustomerId(IdType $customerId)
    {
        $this->userManager->deleteUserByCustomerId($customerId);
        
        return $this;
    }
    
    
    /**
     * Returns a role by a given role ID.
     *
     * @param IdType $roleId ID of the requested role.
     *
     * @return \AdminAccessRoleInterface
     */
    public function getRoleById(IdType $roleId)
    {
        return $this->roleManager->getRoleById($roleId);
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
        return $this->permissionManager->getPermissionsByGroupCollection($roleId, $groupCollection);
    }
    
    
    /**
     * Returns a collection of all groups.
     *
     * @return AdminAccessGroupCollection Group collection.
     */
    public function getAllGroups()
    {
        return $this->groupManager->getAllGroups();
    }
    
    
    /**
     * Returns a group by a given controller identifier.
     *
     * @param \NonEmptyStringType $identifier Controller identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByController(NonEmptyStringType $identifier)
    {
        return $this->groupManager->getGroupByController($identifier);
    }
    
    
    /**
     * Returns a group by a given page identifier.
     *
     * @param \NonEmptyStringType $identifier Page identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByPage(NonEmptyStringType $identifier)
    {
        return $this->groupManager->getGroupByPage($identifier);
    }
    
    
    /**
     * Returns a group by a given ajax handler identifier.
     *
     * @param \NonEmptyStringType $identifier Ajax handler identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByAjaxHandler(NonEmptyStringType $identifier)
    {
        return $this->groupManager->getGroupByAjaxHandler($identifier);
    }
    
    
    /**
     * Returns a group by a given group id.
     *
     * @param IdType $id Group id.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupById(IdType $id)
    {
        return $this->groupManager->getGroupById($id);
    }
    
    
    /**
     * Adds a group item to an existing group.
     *
     * @param \IdType             $groupId    Id of the group.
     * @param \NonEmptyStringType $identifier Identifier for this item.
     * @param \NonEmptyStringType $type       Type of this item.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function addItemToGroup(IdType $groupId, NonEmptyStringType $identifier, NonEmptyStringType $type)
    {
        return $this->groupManager->addItemToGroup($groupId, $identifier, $type);
    }
    
    
    /**
     * Removes a group item from an existing group.
     *
     * @param \IdType             $groupId    Id of the group.
     * @param \NonEmptyStringType $identifier Identifier for this item.
     * @param \NonEmptyStringType $type       Type of this item.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     * @throws \GroupItemNotFoundInCollectionException
     */
    public function removeItemFromGroup(IdType $groupId, NonEmptyStringType $identifier, NonEmptyStringType $type)
    {
        return $this->groupManager->removeItemFromGroup($groupId, $identifier, $type);
    }
    
    
    /**
     * Creates a new group.
     *
     * @param KeyValueCollection $name        Collection with the group names. Index of a group name must be his
     *                                        language code.
     * @param KeyValueCollection $description Collection with the group descriptions. Index of a group name must be his
     *                                        language code.
     * @param IdType             $parentId    Id of the parent group.
     * @param IntType            $sortOrder   Group sorts order.
     *
     * @return AdminAccessGroupInterface Returns the create role.
     *
     * @throws \GroupNotFoundException
     */
    public function createNewGroup(
        KeyValueCollection $name,
        KeyValueCollection $description,
        IdType $parentId,
        IntType $sortOrder
    ) {
        return $this->groupManager->createNewGroup($name, $description, $parentId, $sortOrder);
    }
    
    
    /**
     * Updates a group by a given group ID.
     *
     * @param KeyValueCollection $newName        Collection with the group names. Index of a group name must be his
     *                                           language code.
     * @param KeyValueCollection $newDescription Collection with the group descriptions. Index of a group name must be
     *                                           his language code.
     * @param IdType             $newParentId    New id of the parent group.
     * @param IntType            $newSortOrder   New group sorts order.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function updateGroup(
        IdType $id,
        KeyValueCollection $newName,
        KeyValueCollection $newDescription,
        IdType $newParentId,
        IntType $newSortOrder
    ) {
        $this->groupManager->updateGroup($id, $newName, $newDescription, $newParentId, $newSortOrder);
        
        return $this;
    }
    
    
    /**
     * Deletes a group by a given group ID.
     *
     * @param IdType $id ID of the group that should be deleted.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function deleteGroupById(IdType $id)
    {
        $this->groupManager->deleteGroupById($id);
        
        return $this;
    }
}
