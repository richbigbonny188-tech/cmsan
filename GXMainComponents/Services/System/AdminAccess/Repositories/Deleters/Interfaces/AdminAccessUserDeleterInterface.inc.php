<?php

/* --------------------------------------------------------------
   AdminAccessUserDeleterInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessUserDeleterInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
interface AdminAccessUserDeleterInterface
{
    /**
     * Removes an user from the database by a given user id.
     *
     * @param IdType $userId User ID.
     *
     * @return AdminAccessUserDeleterInterface Returns same instance for chained method calls.
     */
    public function deleteById(IdType $userId);
}
