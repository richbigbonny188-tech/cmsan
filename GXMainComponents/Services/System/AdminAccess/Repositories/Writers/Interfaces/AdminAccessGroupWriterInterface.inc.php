<?php

/* --------------------------------------------------------------
   AdminAccessGroupWriterInterface.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessGroupWriterInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
interface AdminAccessGroupWriterInterface
{
    /**
     * Stores a role into the database.
     *
     * @param AdminAccessGroup $role Group object.
     *
     * @return AdminAccessGroupWriterInterface Returns same instance for chained method calls.
     */
    public function insert(AdminAccessGroup $role);
    
    
    /**
     * Updates a role from the database.
     *
     * @param AdminAccessGroup $role Group object.
     *
     * @return AdminAccessGroupWriterInterface Returns same instance for chained method calls.
     */
    public function update(AdminAccessGroup $role);
}
