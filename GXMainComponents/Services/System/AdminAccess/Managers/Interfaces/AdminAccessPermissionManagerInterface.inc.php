<?php

/* --------------------------------------------------------------
   AdminAccessPermissionManagerInterface.inc.php 2018-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessPermissionManagerInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Managers
 */
interface AdminAccessPermissionManagerInterface
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
     * Grants deleting permission to a role for a given group id.
     *
     * @param IdType $groupId Group ID to grant permission for.
     * @param IdType $roleId  Role ID to grant permission for.
     *
     * @return AdminAccessPermissionManager Returns same instance for chained method calls.
     *
     * @throws GroupNotFoundException
     */
    public function grantDeletingPermission(IdType $roleId, IdType $groupId);
    
    
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
    public function grantReadingPermission(IdType $roleId, IdType $groupId);
    
    
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
    public function grantWritingPermission(IdType $roleId, IdType $groupId);
    
    
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
    public function removeDeletingPermission(IdType $roleId, IdType $groupId);
    
    
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
    public function removeReadingPermission(IdType $roleId, IdType $groupId);
    
    
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
    public function removeWritingPermission(IdType $roleId, IdType $groupId);
    
    
    /**
     * Returns a collection of all permissions by a given role ID.
     *
     * @param IdType $roleId Role ID.
     *
     * @return AdminAccessPermissionCollection Permission collection object with all role permissions.
     */
    public function getPermissionsByRoleId(IdType $roleId);
    
    
    /**
     * Returns a collection of permissions of a role by a given group collection.
     *
     * @param IdType                     $roleId          Role ID.
     * @param AdminAccessGroupCollection $groupCollection Group collection.
     *
     * @return AdminAccessPermissionCollection Permission collection object with all role permissions.
     */
    public function getPermissionsByGroupCollection(IdType $roleId, AdminAccessGroupCollection $groupCollection);
}
