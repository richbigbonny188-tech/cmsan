<?php

/* --------------------------------------------------------------
   AdminAccessUserManager.inc.php 2018-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessUserManager
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Managers
 */
class AdminAccessUserManager implements AdminAccessUserManagerInterface
{
    /**
     * @var AdminAccessUserReaderInterface
     */
    protected $userReader;
    
    /**
     * @var AdminAccessRoleReaderInterface
     */
    protected $roleReader;
    
    
    /**
     * AdminAccessUserManager constructor.
     *
     * @param AdminAccessUserReaderInterface $userReader User reader.
     * @param AdminAccessRoleReaderInterface $roleReader Role reader.
     */
    public function __construct(AdminAccessUserReaderInterface $userReader, AdminAccessRoleReaderInterface $roleReader)
    {
        $this->userReader = $userReader;
        $this->roleReader = $roleReader;
    }
    
    
    /**
     * Adds a role to a user by a given role and customer ID.
     *
     * @param IdType $roleId     Role ID.
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessUserManager Returns same instance for chained method calls.
     */
    public function addRoleToUserByCustomerId(IdType $roleId, IdType $customerId)
    {
        $user = $this->userReader->getByCustomerId($customerId);
        $role = $this->roleReader->getById($roleId);
        
        $user->addNewRole($role);
        $user->update();
        
        return $this;
    }
    
    
    /**
     * Removes a role from a user by given role and customer ID.
     *
     * @param IdType $roleId     Role ID.
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessUserManager Returns same instance for chained method calls.
     */
    public function removeRoleFromUserByCustomerId(IdType $roleId, IdType $customerId)
    {
        $user = $this->userReader->getByCustomerId($customerId);
        $role = $this->roleReader->getById($roleId);
        
        $user->removeRole($role);
        $user->update();
        
        return $this;
    }
    
    
    /**
     * Returns all roles of certain user by a given user ID.
     *
     * @param idType $id User ID.
     *
     * @return AdminAccessRoleCollection Collection of all roles that certain user has.
     */
    public function getRolesByCustomerId(IdType $id)
    {
        $user = $this->userReader->getByCustomerId($id);
        
        return $user->getRoles();
    }
    
    
    /**
     * Deletes an admin access user by a given customer ID.
     *
     * @param IdType $customerId ID of the user that should be deleted.
     *
     * @return AdminAccessUserManager Returns same instance for chained method calls.
     */
    public function deleteUserByCustomerId(IdType $customerId)
    {
        $user = $this->userReader->getByCustomerId($customerId);
        $user->delete();
        
        return $this;
    }
}
