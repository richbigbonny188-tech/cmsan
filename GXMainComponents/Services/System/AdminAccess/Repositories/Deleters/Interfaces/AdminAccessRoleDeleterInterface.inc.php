<?php

/* --------------------------------------------------------------
   AdminAccessRoleDeleterInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessRoleDeleterInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Deleters
 */
interface AdminAccessRoleDeleterInterface
{
    /**
     * Deletes a role by a given role ID.
     *
     * @param IdType $id Role ID.
     *
     * @return AdminAccessRoleDeleterInterface Returns same instance for chained method calls.
     */
    public function delete(IdType $id);
}
