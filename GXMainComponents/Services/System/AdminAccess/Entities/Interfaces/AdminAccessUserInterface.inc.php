<?php

/* --------------------------------------------------------------
   AdminAccessUserInterface.inc.php 2017-09-21
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessUserInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
interface AdminAccessUserInterface
{
    /**
     * AdminAccessUser constructor.
     *
     * @param AdminAccessUserWriterInterface  $writer      User writer.
     * @param AdminAccessUserDeleterInterface $userDeleter User deleter.
     * @param IdType                          $customerId  User id.
     * @param AdminAccessRoleCollection       $roles       User roles collection.
     */
    public function __construct(
        AdminAccessUserWriterInterface $writer,
        AdminAccessUserDeleterInterface $userDeleter,
        IdType $customerId,
        AdminAccessRoleCollection $roles
    );
    
    
    /**
     * Returns the user id.
     *
     * @return int User ID.
     */
    public function getId();
    
    
    /**
     * Adds a role to this user.
     *
     * @param AdminAccessRoleInterface $role Role object.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function addNewRole(AdminAccessRoleInterface $role);
    
    
    /**
     * Removes role from this user.
     *
     * @param AdminAccessRoleInterface $role Role object.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function removeRole(AdminAccessRoleInterface $role);
    
    
    /**
     * Checks deleting permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if deleting permission is granted, false otherwise.
     */
    public function checkDeletingPermission(AdminAccessGroupInterface $group);
    
    
    /**
     * Checks reading permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if reading permission is granted, false otherwise.
     */
    public function checkReadingPermission(AdminAccessGroupInterface $group);
    
    
    /**
     * Checks writing permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if writing permission is granted, false otherwise.
     */
    public function checkWritingPermission(AdminAccessGroupInterface $group);
    
    
    /**
     * Checks deleting permission for an unknown group.
     *
     * @return bool True if deleting permission for an unknown group is granted, false otherwise.
     */
    public function checkDeletingPermissionForUnknownGroup();
    
    
    /**
     * Checks reading permission for an unknown group.
     *
     * @return bool True if reading permission for an unknown group is granted, false otherwise.
     */
    public function checkReadingPermissionForUnknownGroup();
    
    
    /**
     * Checks writing permission for an unknown group.
     *
     * @return bool True if writing permission for an unknown group is granted, false otherwise.
     */
    public function checkWritingPermissionForUnknownGroup();
    
    
    /**
     * Sets the customer ID.
     *
     * @param IdType $id Customer ID.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function setCustomerId(IdType $id);
    
    
    /**
     * Sets the user roles.
     *
     * @param AdminAccessRoleCollection $roles Access roles collection.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function setRoles(AdminAccessRoleCollection $roles);
    
    
    /**
     * Returns the user roles.
     *
     * @return AbstractCollection|AdminAccessRoleCollection Cloned roles collection.
     */
    public function getRoles();
    
    
    /**
     * Stores/Updates an user into/from the database.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function update();
    
    
    /**
     * Deletes an user from the database.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function delete();
}
