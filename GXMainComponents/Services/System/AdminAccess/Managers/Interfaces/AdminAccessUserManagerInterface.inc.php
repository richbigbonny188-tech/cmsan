<?php

/* --------------------------------------------------------------
   AdminAccessUserManagerInterface.inc.php 2018-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface AdminAccessUserManagerInterface
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Managers
 */
interface AdminAccessUserManagerInterface
{
    /**
     * Adds role to user by customer ID.
     *
     * @param IdType $roleId     Role ID.
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessUserManagerInterface Returns same instance for chained method calls.
     */
    public function addRoleToUserByCustomerId(IdType $roleId, IdType $customerId);
    
    
    /**
     * Removes role from user by customer ID.
     *
     * @param IdType $roleId     Role ID.
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessUserManagerInterface Returns same instance for chained method calls.
     */
    public function removeRoleFromUserByCustomerId(IdType $roleId, IdType $customerId);
    
    
    /**
     * Gets collection of all roles of a certain user.
     *
     * @param IdType $id User ID.
     *
     * @return AdminAccessRoleCollection Collection of all roles that the certain user has.
     */
    public function getRolesByCustomerId(IdType $id);
    
    
    /**
     * Deletes user by customer ID.
     *
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessUserManagerInterface Returns same instance for chained method calls.
     */
    public function deleteUserByCustomerId(IdType $customerId);
}
