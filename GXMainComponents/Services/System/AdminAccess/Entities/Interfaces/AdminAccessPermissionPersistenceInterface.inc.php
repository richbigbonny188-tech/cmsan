<?php

/* --------------------------------------------------------------
   AdminAccessPermissionPersistenceInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessPermissionPersistenceInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
interface AdminAccessPermissionPersistenceInterface
{
    /**
     * Deletes a permission.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function delete();
    
    
    /**
     * Grants deleting access.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function grantDeleting();
    
    
    /**
     * Grants reading access.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function grantReading();
    
    
    /**
     * Grants writing access.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function grantWriting();
    
    
    /**
     * Removes deleting access.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function removeDeleting();
    
    
    /**
     * Removes reading access.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function removeReading();
    
    
    /**
     * Removes writing access.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function removeWriting();
    
    
    /**
     * Sets the group of this permission.
     *
     * @param AdminAccessGroupInterface $accessGroup Group object.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function setGroup(AdminAccessGroupInterface $accessGroup);
    
    
    /**
     * Sets group ID.
     *
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function setGroupId(IdType $groupId);
    
    
    /**
     * Sets the role of this permission.
     *
     * @param AdminAccessRoleInterface $accessRole Role object.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function setRole(AdminAccessRoleInterface $accessRole);
    
    
    /**
     * Stores a permission into the database.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function store();
    
    
    /**
     * Updates a permission in the database.
     *
     * @return AdminAccessPermissionPersistenceInterface Returns same instance for chained method calls.
     */
    public function update();
}
