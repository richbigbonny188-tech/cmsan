<?php

/* --------------------------------------------------------------
   AdminAccessPermissionWriterInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessPermissionWriterInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
interface AdminAccessPermissionWriterInterface
{
    /**
     * Stores a permission into the database.
     *
     * @param AdminAccessPermission $permission Permission.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function insert(AdminAccessPermission $permission);
    
    
    /**
     * Updates a permission in the database.
     *
     * @param AdminAccessPermission $permission Permission.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function update(AdminAccessPermission $permission);
    
    
    /**
     * Updates the deleting permission flag of a permission in the database.
     *
     * @param IdType   $roleId          Role ID.
     * @param IdType   $groupId         Group ID.
     * @param BoolType $deletingGranted Value for the deleting permission flag.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function updateDeletingPermission(IdType $roleId, IdType $groupId, BoolType $deletingGranted);
    
    
    /**
     * Updates the reading permission flag of a permission in the database.
     *
     * @param IdType   $roleId         Role ID.
     * @param IdType   $groupId        Group ID.
     * @param BoolType $readingGranted Value for the reading permission flag.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function updateReadingPermission(IdType $roleId, IdType $groupId, BoolType $readingGranted);
    
    
    /**
     * Updates the writing permission flag of a permission in the database.
     *
     * @param IdType   $roleId         Role ID.
     * @param IdType   $groupId        Group ID.
     * @param BoolType $writingGranted Value for the writing permission flag.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function updateWritingPermission(IdType $roleId, IdType $groupId, BoolType $writingGranted);
}
