<?php

/* --------------------------------------------------------------
   AdminAccessRoleWriterInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessRoleWriterInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
interface AdminAccessRoleWriterInterface
{
    /**
     * Stores a role into the database.
     *
     * @param AdminAccessRole $role Role object.
     *
     * @return int ID of stored role.
     */
    public function insert(AdminAccessRole $role);
    
    
    /**
     * Updates a role from the database.
     *
     * @param AdminAccessRole $role Role object.
     *
     * @return AdminAccessRoleWriterInterface Returns same instance for chained method calls.
     */
    public function update(AdminAccessRole $role);
}
