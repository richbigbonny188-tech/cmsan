<?php

/* --------------------------------------------------------------
  AdminAccessRoleReaderInterface.inc.php 2018-01-22
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface AdminAccessRoleReaderInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Readers
 */
interface AdminAccessRoleReaderInterface
{
    /**
     * Returns all available roles as a role collection.
     *
     * @return AdminAccessRoleCollection Role collection with all available roles.
     */
    public function getAll();
    
    
    /**
     * Returns all roles of a certain user by a given customer ID.
     *
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessRoleCollection Role collection with all roles of a certain user.
     *
     * @throws \RoleNotFoundException
     */
    public function getByCustomerId(IdType $customerId);
    
    
    /**
     * Returns a role object by a given role ID.
     *
     * @param IdType $roleId Role ID.
     *
     * @return AdminAccessRoleInterface Role object.
     */
    public function getById(IdType $roleId);
}
