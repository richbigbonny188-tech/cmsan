<?php

/* --------------------------------------------------------------
   AdminAccessRoleInterface.inc.php 2018-02-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessRoleInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
interface AdminAccessRoleInterface
{
    /**
     * Checks deleting permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if role has deleting permission, false otherwise.
     */
    public function checkDeletingPermission(AdminAccessGroupInterface $group);
    
    
    /**
     * Checks reading permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if role has reading permission, false otherwise.
     */
    public function checkReadingPermission(AdminAccessGroupInterface $group);
    
    
    /**
     * Checks writing permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if role has writing permission, false otherwise.
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
     * Deletes an access role.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function delete();
    
    
    /**
     * Returns the role id.
     *
     * @return int Role ID.
     */
    public function getId();
    
    
    /**
     * Returns the role names as a collection.
     *
     * @return KeyValueCollection Role name in all available languages.
     */
    public function getName();
    
    
    /**
     * Returns the role descriptions as a collection.
     *
     * @return KeyValueCollection Role description in all available languages.
     */
    public function getDescription();
    
    
    /**
     * Returns the role sort order.
     *
     * @return int Sort order.
     */
    public function getSortOrder();
    
    
    /**
     * Sets the role ID.
     *
     * @param IdType $id Role ID.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Sets the role names.
     *
     * @param KeyValueCollection $name Role name.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setName(KeyValueCollection $name);
    
    
    /**
     * Sets the role description.
     *
     * @param KeyValueCollection $description Role description.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setDescription(KeyValueCollection $description);
    
    
    /**
     * Sets the role sort order.
     *
     * @param IntType $sortOrder Role sort order.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setSortOrder(IntType $sortOrder);
    
    
    /**
     * Sets the deleting permission value for an unknown group.
     *
     * @param BoolType $permissionGranted Value of the deleting permission for unknown groups.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setDeletingUnknownGroupGranted(BoolType $permissionGranted);
    
    
    /**
     * Sets the reading permission value for an unknown group.
     *
     * @param BoolType $permissionGranted Value of the reading permission for unknown groups.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setReadingUnknownGroupGranted(BoolType $permissionGranted);
    
    
    /**
     * Sets the writing permission value for an unknown group.
     *
     * @param BoolType $permissionGranted Value of the writing permission for unknown groups.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setWritingUnknownGroupGranted(BoolType $permissionGranted);
    
    
    /**
     * Stores this role into the database.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function store();
    
    
    /**
     * Updates this role in the database.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function update();
    
    
    /**
     * Returns the a permission for this role by a given group.
     *
     * @param $group  AdminAccessGroupInterface Admin access group.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface|null
     *                       Returns permission found or null if permission not found.
     */
    public function getPermissionByGroup(AdminAccessGroupInterface $group);
    
    
    /**
     * Returns the a permission for this role by a given group collection.
     *
     * @param $groupCollection AdminAccessGroupCollection Collections of groups to find.
     *
     * @return AdminAccessPermissionCollection Returns permissions collection.
     */
    public function getPermissionsByGroupCollection(AdminAccessGroupCollection $groupCollection);
    
    
    /**
     * Sets the protected value.
     *
     * @param BoolType $value
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setProtected(BoolType $value);
    
    
    /**
     * Returns the protected value.
     *
     * @return bool
     */
    public function getProtected();
}
