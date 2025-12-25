<?php

/* --------------------------------------------------------------
   AdminAccessPermissionPresentationInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessPermissionPresentationInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
interface AdminAccessPermissionPresentationInterface
{
    /**
     * Returns the group of this permission.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroup();
    
    
    /**
     * Returns the role of this permission.
     *
     * @return AdminAccessRoleInterface Role object.
     */
    public function getRole();
    
    
    /**
     * Checks if deleting is granted.
     *
     * @return bool True if deleting is granted, false otherwise.
     */
    public function isDeletingGranted();
    
    
    /**
     * Checks if reading is granted.
     *
     * @return bool True if reading is granted, false otherwise.
     */
    public function isReadingGranted();
    
    
    /**
     * Checks if writing is granted.
     *
     * @return bool True if writing is granted, false otherwise.
     */
    public function isWritingGranted();
}
