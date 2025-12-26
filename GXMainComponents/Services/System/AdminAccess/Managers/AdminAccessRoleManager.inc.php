<?php

/* --------------------------------------------------------------
   AdminAccessRoleManager.inc.php 2018-01-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessRoleManager
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Managers
 */
class AdminAccessRoleManager implements AdminAccessRoleManagerInterface
{
    /**
     * @var AdminAccessRoleFactoryInterface
     */
    protected $roleFactory;
    
    /**
     * @var AdminAccessRoleReaderInterface
     */
    protected $roleReader;
    
    
    /**
     * AdminAccessRoleManager constructor.
     *
     * @param AdminAccessRoleFactoryInterface $roleFactory Role factory.
     * @param AdminAccessRoleReaderInterface  $roleReader  Role reader.
     */
    public function __construct(
        AdminAccessRoleFactoryInterface $roleFactory,
        AdminAccessRoleReaderInterface $roleReader
    ) {
        $this->roleFactory = $roleFactory;
        $this->roleReader  = $roleReader;
    }
    
    
    /**
     * Returns a collection of all roles.
     *
     * @return AdminAccessRoleCollection Role collection with all available roles.
     */
    public function getAllRoles()
    {
        return $this->roleReader->getAll();
    }
    
    
    /**
     * Creates a new role.
     *
     * @param KeyValueCollection $name                    Collection with the role names. Index of a role name must be
     *                                                    his language code.
     * @param KeyValueCollection $description             Collection with the role descriptions. Index of a role name
     *                                                    must be his language code.
     * @param IntType            $sortOrder               Roles sort order.
     * @param BoolType           $unknownReadingGranted   Value of the reading permission for unknown groups.
     * @param BoolType           $unknownWritingGranted   Value of the writing permission for unknown groups.
     * @param BoolType           $unknownDeletingGranted  Value of the deleting permission for unknown groups.
     *
     * @return AdminAccessRoleInterface Returns the create role.
     */
    public function createNewRole(
        KeyValueCollection $name,
        KeyValueCollection $description,
        IntType $sortOrder,
        BoolType $unknownReadingGranted,
        BoolType $unknownWritingGranted,
        BoolType $unknownDeletingGranted
    ) {
        // get new role object
        $role = $this->roleFactory->createAdminAccessRole();
        
        // set data for new role
        $role->setName($name);
        $role->setDescription($description);
        $role->setSortOrder($sortOrder);
        $role->setReadingUnknownGroupGranted($unknownReadingGranted);
        $role->setWritingUnknownGroupGranted($unknownWritingGranted);
        $role->setDeletingUnknownGroupGranted($unknownDeletingGranted);
        $role->store();
        
        return $role;
    }
    
    
    /**
     * Updates a role by a given role ID.
     *
     * @param IdType             $id                      Role ID to remove permission from.
     * @param KeyValueCollection $newName                 Collection with the new role names. Index of a role name must
     *                                                    be his language code.
     * @param KeyValueCollection $newDescription          Collection with the new role descriptions. Index of a role
     *                                                    name must be his language code.
     * @param IntType            $newSortOrder            New roles sort order.
     * @param BoolType           $unknownReadingGranted   Value of the reading permission for unknown groups.
     * @param BoolType           $unknownWritingGranted   Value of the writing permission for unknown groups.
     * @param BoolType           $unknownDeletingGranted  Value of the deleting permission for unknown groups.
     *
     * @return AdminAccessRoleManager Returns same instance for chained method calls.
     */
    public function updateRole(
        IdType $id,
        KeyValueCollection $newName,
        KeyValueCollection $newDescription,
        IntType $newSortOrder,
        BoolType $unknownReadingGranted,
        BoolType $unknownWritingGranted,
        BoolType $unknownDeletingGranted
    ) {
        // fetch role by id
        $role = $this->roleReader->getById($id);
        
        // set new data for role
        $role->setName($newName);
        $role->setDescription($newDescription);
        $role->setSortOrder($newSortOrder);
        $role->setReadingUnknownGroupGranted($unknownReadingGranted);
        $role->setWritingUnknownGroupGranted($unknownWritingGranted);
        $role->setDeletingUnknownGroupGranted($unknownDeletingGranted);
        
        $role->update();
        
        return $this;
    }
    
    
    /**
     * Deletes role by a given role ID.
     *
     * @param IdType $id ID of the role that should be deleted.
     *
     * @return AdminAccessRoleManager Returns same instance for chained method calls.
     */
    public function deleteRole(IdType $id)
    {
        $role = $this->roleReader->getById($id);
        
        $role->delete();
        
        return $this;
    }
    
    
    /**
     * Returns a role by a given role ID.
     *
     * @param IdType $id ID of the requested role.
     *
     * @return AdminAccessRoleInterface
     */
    public function getRoleById(IdType $id)
    {
        return $this->roleReader->getById($id);
    }
}
