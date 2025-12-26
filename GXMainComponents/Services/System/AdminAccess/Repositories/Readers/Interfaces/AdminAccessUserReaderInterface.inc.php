<?php

/* --------------------------------------------------------------
  AdminAccessUserReaderInterface.inc.php 2018-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface AdminAccessUserReaderInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Readers
 */
interface AdminAccessUserReaderInterface
{
    /**
     * Returns an user by a given customer ID.
     *
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessUserInterface User object.
     *
     * @throws \RoleNotFoundException
     */
    public function getByCustomerId(IdType $customerId);
}
