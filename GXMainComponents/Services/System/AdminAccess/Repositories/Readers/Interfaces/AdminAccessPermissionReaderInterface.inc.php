<?php

/* --------------------------------------------------------------
  AdminAccessPermissionReaderInterface.inc.php 2018-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface AdminAccessPermissionReaderInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Readers
 */
interface AdminAccessPermissionReaderInterface
{
    /**
     * Returns an AdminAccessPermission object by given AdminAccessRole and AdminAccessGroup objects.
     *
     * @param AdminAccessRoleInterface  $accessRole  Role object.
     * @param AdminAccessGroupInterface $accessGroup Group object.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Permission object.
     */
    public function get(AdminAccessRoleInterface $accessRole, AdminAccessGroupInterface $accessGroup);
    
    
    /**
     * Returns an AdminAccessPermissionCollection by a given AdminAccessRole and AdminAccessGroupCollection object.
     *
     * @param AdminAccessRoleInterface   $accessRole      User role to find permission for.
     * @param AdminAccessGroupCollection $groupCollection Collection of all groups in system.
     *
     * @return    AdminAccessPermissionCollection Permission collection.
     */
    public function getByGroupCollection(
        AdminAccessRoleInterface $accessRole,
        AdminAccessGroupCollection $groupCollection
    );
}
