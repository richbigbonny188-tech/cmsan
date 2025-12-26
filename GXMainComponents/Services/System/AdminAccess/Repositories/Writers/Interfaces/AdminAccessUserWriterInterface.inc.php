<?php

/* --------------------------------------------------------------
   AdminAccessUserWriterInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessUserWriterInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
interface AdminAccessUserWriterInterface
{
    /**
     * Stores the user roles into the database.
     *
     * @param IdType                    $customerId Customer ID.
     * @param AdminAccessRoleCollection $roles      Roles collection.
     *
     * @return AdminAccessUserWriterInterface Returns same instance for chained method calls.
     */
    public function store(IdType $customerId, AdminAccessRoleCollection $roles);
}
