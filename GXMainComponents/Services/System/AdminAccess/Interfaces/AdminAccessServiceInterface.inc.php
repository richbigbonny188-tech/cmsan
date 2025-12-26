<?php

/* --------------------------------------------------------------
   AdminAccessServiceInterface.inc.php 2018-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AuthServiceInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Interfaces
 */
interface AdminAccessServiceInterface
{
    /**
     * Checks the deleting permission for a controller.
     *
     * @param NonEmptyStringType $identifier The name of a controller to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check the permission for.
     *
     * @return bool True if customer has a deleting permission for the controller, false otherwise.
     */
    public function checkDeletingPermissionForController(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Checks the deleting permission for a page.
     *
     * @param NonEmptyStringType $identifier The name of a page to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a deleting permission for the page, false otherwise.
     */
    public function checkDeletingPermissionForPage(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Checks the deleting permission for an ajax handler.
     *
     * @param NonEmptyStringType $identifier The name of an ajax handler to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a deleting permission for the ajax handler, false otherwise.
     */
    public function checkDeletingPermissionForAjaxHandler(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Checks the reading permission for a controller.
     *
     * @param NonEmptyStringType $identifier The name of a controller to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check the permission for.
     *
     * @return bool True if customer has a reading permission for the controller, false otherwise.
     */
    public function checkReadingPermissionForController(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Checks the reading permission for a page.
     *
     * @param NonEmptyStringType $identifier The name of a page to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a reading permission for the page, false otherwise.
     */
    public function checkReadingPermissionForPage(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Checks the reading permission for an ajax handler.
     *
     * @param NonEmptyStringType $identifier The name of an ajax handler to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a reading permission for the ajax handler, false otherwise.
     */
    public function checkReadingPermissionForAjaxHandler(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Checks the writing permission for a controller.
     *
     * @param NonEmptyStringType $identifier The name of a controller to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check the permission for.
     *
     * @return bool True if customer has a writing permission for the controller, false otherwise.
     */
    public function checkWritingPermissionForController(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Checks the writing permission for a page.
     *
     * @param NonEmptyStringType $identifier The name of a page to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a writing permission for the page, false otherwise.
     */
    public function checkWritingPermissionForPage(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Checks the writing permission for an ajax handler.
     *
     * @param NonEmptyStringType $identifier The name of an ajax handler to identify an admin access group.
     * @param IdType             $customerId ID of a customer to check permission for.
     *
     * @return bool True if customer has a writing permission for the ajax handler, false otherwise.
     */
    public function checkWritingPermissionForAjaxHandler(NonEmptyStringType $identifier, IdType $customerId);
    
    
    /**
     * Returns a collection of all permissions by a given role ID.
     *
     * @param IdType $roleId Role ID.
     *
     * @return AdminAccessPermissionCollection Permission collection object with all role permissions.
     */
    public function getPermissionsByRoleId(IdType $roleId);
    
    
    /**
     * Adds a role to a user by a given role and customer ID.
     *
     * @param IdType $roleId     Role ID.
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function addRoleToUserByCustomerId(IdType $roleId, IdType $customerId);
    
    
    /**
     * Removes a role from a user by given role and customer ID.
     *
     * @param IdType $roleId     Role ID.
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function removeRoleFromUserByCustomerId(IdType $roleId, IdType $customerId);
    
    
    /**
     * Returns all roles of certain user by a given user ID.
     *
     * @param idType $id User ID.
     *
     * @return AdminAccessRoleCollection Collection of all roles that certain user has.
     */
    public function getRolesByCustomerId(IdType $id);
    
    
    /**
     * Grants deleting permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function grantDeletingPermissionToRole(IdType $groupId, IdType $roleId);
    
    
    /**
     * Removes deleting permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function removeDeletingPermissionFromRole(IdType $groupId, IdType $roleId);
    
    
    /**
     * Grants reading permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function grantReadingPermissionToRole(IdType $groupId, IdType $roleId);
    
    
    /**
     * Removes reading permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function removeReadingPermissionFromRole(IdType $groupId, IdType $roleId);
    
    
    /**
     * Grants writing permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function grantWritingPermissionToRole(IdType $groupId, IdType $roleId);
    
    
    /**
     * Removes writing permission from role for a given group id.
     *
     * @param IdType $groupId Group ID to remove permission for.
     * @param IdType $roleId  Role ID to remove permission from.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function removeWritingPermissionFromRole(IdType $groupId, IdType $roleId);
    
    
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
     * @return \AdminAccessRoleInterface Returns the create role.
     */
    public function createNewRole(
        KeyValueCollection $name,
        KeyValueCollection $description,
        IntType $sortOrder,
        BoolType $unknownReadingGranted,
        BoolType $unknownWritingGranted,
        BoolType $unknownDeletingGranted
    );
    
    
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
    );
    
    
    /**
     * Deletes role by a given role ID.
     *
     * @param IdType $roleId ID of the role that should be deleted.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function deleteRoleById(IdType $roleId);
    
    
    /**
     * Returns a collection of all roles.
     *
     * @return AdminAccessRoleCollection Role collection with all available roles.
     */
    public function getAllRoles();
    
    
    /**
     * Deletes an admin access user by a given customer ID.
     *
     * @param IdType $customerId ID of the user that should be deleted.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     */
    public function deleteUserByCustomerId(IdType $customerId);
    
    
    /**
     * Returns a role by a given role ID.
     *
     * @param IdType $roleId ID of the requested role.
     *
     * @return \AdminAccessRoleInterface
     */
    public function getRoleById(IdType $roleId);
    
    
    /**
     * Returns a collection of permissions of a role by a given group collection.
     *
     * @param IdType                     $roleId          Role ID.
     * @param AdminAccessGroupCollection $groupCollection Group collection.
     *
     * @return AdminAccessPermissionCollection Permission collection object with all role permissions.
     */
    public function getPermissionsByGroupCollection(IdType $roleId, AdminAccessGroupCollection $groupCollection);
    
    
    /**
     * Returns a collection of all groups.
     *
     * @return AdminAccessGroupCollection Group collection.
     */
    public function getAllGroups();
    
    
    /**
     * Returns a group by a given controller identifier.
     *
     * @param \NonEmptyStringType $identifier Controller identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByController(NonEmptyStringType $identifier);
    
    
    /**
     * Returns a group by a given page identifier.
     *
     * @param \NonEmptyStringType $identifier Page identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByPage(NonEmptyStringType $identifier);
    
    
    /**
     * Returns a group by a given ajax handler identifier.
     *
     * @param \NonEmptyStringType $identifier Ajax handler identifier.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupByAjaxHandler(NonEmptyStringType $identifier);
    
    
    /**
     * Returns a group by a given group id.
     *
     * @param IdType $id Group id.
     *
     * @return \AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroupById(IdType $id);
    
    
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
    public function addItemToGroup(IdType $groupId, NonEmptyStringType $identifier, NonEmptyStringType $type);
    
    
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
    public function removeItemFromGroup(IdType $groupId, NonEmptyStringType $identifier, NonEmptyStringType $type);
    
    
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
    );
    
    
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
    );
    
    
    /**
     * Deletes a group by a given group ID.
     *
     * @param IdType $id ID of the group that should be deleted.
     *
     * @return AdminAccessServiceInterface Returns same instance for chained method calls.
     *
     * @throws \GroupNotFoundException
     */
    public function deleteGroupById(IdType $id);
}
