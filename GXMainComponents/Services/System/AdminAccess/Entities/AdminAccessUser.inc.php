<?php

/* --------------------------------------------------------------
    AdminAccessUser.inc.php 2018-01-22
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2017 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------
*/

/**
 * Class AdminAccessUser
 *
 * A User is a representation of an user with administration rights. Since all users are stored in
 * the customers table of the shop system, the term "customerId" is used to identify such an
 * user. Apart from the ID the user has a collection of roles.
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
class AdminAccessUser implements AdminAccessUserInterface
{
    /**
     * @var int
     */
    protected $customerId;
    
    /**
     * @var AdminAccessRoleCollection|AbstractCollection
     */
    protected $roles;
    
    /**
     * @var AdminAccessUserWriterInterface
     */
    protected $userWriter;
    
    /**
     * @var AdminAccessUserDeleterInterface
     */
    protected $userDeleter;
    
    /**
     * @var boolean
     */
    protected $readingUnknownGroupGranted;
    
    /**
     * @var boolean
     */
    protected $writingUnknownGroupGranted;
    
    /**
     * @var boolean
     */
    protected $deletingUnknownGroupGranted;
    
    
    /**
     * AdminAccessUser constructor.
     *
     * @param AdminAccessUserWriterInterface  $writer      User writer.
     * @param AdminAccessUserDeleterInterface $userDeleter User deleter.
     * @param IdType                          $customerId  User id.
     * @param AdminAccessRoleCollection       $roles       User roles collection.
     */
    public function __construct(
        AdminAccessUserWriterInterface $writer,
        AdminAccessUserDeleterInterface $userDeleter,
        IdType $customerId,
        AdminAccessRoleCollection $roles
    ) {
        $this->userWriter  = $writer;
        $this->userDeleter = $userDeleter;
        $this->customerId  = $customerId->asInt();
        $this->roles       = $roles;
    }
    
    
    /**
     * Returns the user id.
     *
     * @return int User ID.
     */
    public function getId()
    {
        return $this->customerId;
    }
    
    
    /**
     * Adds a role to this user.
     *
     * @param AdminAccessRoleInterface $role Role object.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function addNewRole(AdminAccessRoleInterface $role)
    {
        $this->roles->add($role);
        
        return $this;
    }
    
    
    /**
     * Removes role from this user.
     *
     * @param AdminAccessRoleInterface $role Role object.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     *
     * @throws \RoleNotFoundInCollectionException
     */
    public function removeRole(AdminAccessRoleInterface $role)
    {
        $this->roles->remove($role);
        
        return $this;
    }
    
    
    /**
     * Checks deleting permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if deleting permission is granted, false otherwise.
     */
    public function checkDeletingPermission(AdminAccessGroupInterface $group)
    {
        /** @var AdminAccessRoleInterface $role */
        foreach ($this->roles->getArray() as $role) {
            if ($role->checkDeletingPermission($group)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Checks reading permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if reading permission is granted, false otherwise.
     */
    public function checkReadingPermission(AdminAccessGroupInterface $group)
    {
        /** @var AdminAccessRoleInterface $role */
        foreach ($this->roles->getArray() as $role) {
            if ($role->checkReadingPermission($group)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Checks writing permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if writing permission is granted, false otherwise.
     */
    public function checkWritingPermission(AdminAccessGroupInterface $group)
    {
        /** @var AdminAccessRoleInterface $role */
        foreach ($this->roles->getArray() as $role) {
            if ($role->checkWritingPermission($group)) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Checks deleting permission for an unknown group.
     *
     * @return bool True if deleting permission for an unknown group is granted, false otherwise.
     */
    public function checkDeletingPermissionForUnknownGroup()
    {
        /** @var AdminAccessRoleInterface $role */
        foreach ($this->roles->getArray() as $role) {
            if ($role->checkDeletingPermissionForUnknownGroup()) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Checks reading permission for an unknown group.
     *
     * @return bool True if reading permission for an unknown group is granted, false otherwise.
     */
    public function checkReadingPermissionForUnknownGroup()
    {
        /** @var AdminAccessRoleInterface $role */
        foreach ($this->roles->getArray() as $role) {
            if ($role->checkReadingPermissionForUnknownGroup()) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Checks writing permission for an unknown group.
     *
     * @return bool True if writing permission for an unknown group is granted, false otherwise.
     */
    public function checkWritingPermissionForUnknownGroup()
    {
        /** @var AdminAccessRoleInterface $role */
        foreach ($this->roles->getArray() as $role) {
            if ($role->checkWritingPermissionForUnknownGroup()) {
                return true;
            }
        }
        
        return false;
    }
    
    
    /**
     * Sets the customer ID.
     *
     * @param IdType $id Customer ID.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function setCustomerId(IdType $id)
    {
        $this->customerId = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Sets the user roles.
     *
     * @param AdminAccessRoleCollection $roles Access roles collection.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function setRoles(AdminAccessRoleCollection $roles)
    {
        $this->roles = $roles;
        
        return $this;
    }
    
    
    /**
     * Returns the user roles.
     *
     * @return AbstractCollection|AdminAccessRoleCollection Cloned roles collection.
     */
    public function getRoles()
    {
        return $this->roles->getClone();
    }
    
    
    /**
     * Stores/Updates an user into/from the database.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function update()
    {
        $this->userWriter->store(new IdType($this->customerId), $this->roles);
        
        return $this;
    }
    
    
    /**
     * Deletes an user from the database.
     *
     * @return AdminAccessUserInterface Returns same instance for chained method calls.
     */
    public function delete()
    {
        $this->userDeleter->deleteById(new IdType($this->customerId));
        
        return $this;
    }
}
