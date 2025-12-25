<?php

/* --------------------------------------------------------------
   AdminAccessPermissionDeleterInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessPermissionDeleterInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Deleters
 */
interface AdminAccessPermissionDeleterInterface
{
    /**
     * Deletes a permission by given AdminAccessRole and AdminAccessGroup objects.
     *
     * @param AdminAccessRoleInterface  $accessRole  Role object.
     * @param AdminAccessGroupInterface $accessGroup Group object.
     *
     * @return AdminAccessPermissionDeleterInterface Returns same instance for chained method calls.
     */
    public function delete(AdminAccessRoleInterface $accessRole, AdminAccessGroupInterface $accessGroup);
    
    
    /**
     * Deletes a permission by given AdminAccessRole id and AdminAccessGroup id.
     *
     * @param IdType $roleId  Role ID.
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessPermissionDeleterInterface Returns same instance for chained method calls.
     */
    public function deleteByIds(IdType $roleId, IdType $groupId);
}
