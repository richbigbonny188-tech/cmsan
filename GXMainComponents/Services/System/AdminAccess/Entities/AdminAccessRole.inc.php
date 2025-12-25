<?php

/* --------------------------------------------------------------
    AdminAccessRole.inc.php 2020-02-11
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2017 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------
*/

/**
 * Class AdminAccessRole
 *
 * A role represents a collection of administration permissions that can be assigned to an user.
 * The role has also a set of descriptive attributes like a name and a description so the user can understand,
 * what the roles purpose is.
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
class AdminAccessRole implements AdminAccessRoleInterface
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var KeyValueCollection
     */
    protected $name;
    
    /**
     * @var KeyValueCollection
     */
    protected $description;
    
    /**
     * @var int
     */
    protected $sortOrder;
    
    /**
     * @var AdminAccessRoleDeleterInterface
     */
    protected $deleter;
    
    /**
     * @var AdminAccessRoleWriterInterface
     */
    protected $writer;
    
    /**
     * @var AdminAccessPermissionReaderInterface
     */
    protected $permissionReader;
    
    /**
     * @var boolean
     */
    protected $deletingUnknownGroupGranted;
    
    /**
     * @var boolean
     */
    protected $readingUnknownGroupGranted;
    
    /**
     * @var boolean
     */
    protected $writingUnknownGroupGranted;
    
    /**
     * @var bool
     */
    protected $protected;
    
    
    /**
     * AdminAccessRole constructor.
     *
     * @param AdminAccessRoleDeleterInterface      $deleter          Role deleter.
     * @param AdminAccessRoleWriterInterface       $writer           Role writer.
     * @param AdminAccessPermissionReaderInterface $permissionReader Permission reader.
     */
    public function __construct(
        AdminAccessRoleDeleterInterface $deleter,
        AdminAccessRoleWriterInterface $writer,
        AdminAccessPermissionReaderInterface $permissionReader
    ) {
        $this->deleter          = $deleter;
        $this->writer           = $writer;
        $this->permissionReader = $permissionReader;
        
        $this->id          = 0;
        $this->sortOrder   = 0;
        $this->name        = new KeyValueCollection([]);
        $this->description = new KeyValueCollection([]);
    }
    
    
    /**
     * Checks deleting permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if role has deleting permission, false otherwise.
     */
    public function checkDeletingPermission(AdminAccessGroupInterface $group)
    {
        $permission = $this->permissionReader->get($this, $group);
        
        return $permission ? $permission->isDeletingGranted() : false;
    }
    
    
    /**
     * Checks reading permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if role has reading permission, false otherwise.
     */
    public function checkReadingPermission(AdminAccessGroupInterface $group)
    {
        $permission = $this->permissionReader->get($this, $group);
        
        return $permission ? $permission->isReadingGranted() : false;
    }
    
    
    /**
     * Checks writing permission for a given group.
     *
     * @param AdminAccessGroupInterface $group Group object.
     *
     * @return bool True if role has writing permission, false otherwise.
     */
    public function checkWritingPermission(AdminAccessGroupInterface $group)
    {
        $permission = $this->permissionReader->get($this, $group);
        
        return $permission ? $permission->isWritingGranted() : false;
    }
    
    
    /**
     * Checks deleting permission for an unknown group.
     *
     * @return bool True if deleting permission for an unknown group is granted, false otherwise.
     */
    public function checkDeletingPermissionForUnknownGroup()
    {
        return $this->deletingUnknownGroupGranted;
    }
    
    
    /**
     * Checks reading permission for an unknown group.
     *
     * @return bool True if reading permission for an unknown group is granted, false otherwise.
     */
    public function checkReadingPermissionForUnknownGroup()
    {
        return $this->readingUnknownGroupGranted;
    }
    
    
    /**
     * Checks writing permission for an unknown group.
     *
     * @return bool True if writing permission for an unknown group is granted, false otherwise.
     */
    public function checkWritingPermissionForUnknownGroup()
    {
        return $this->writingUnknownGroupGranted;
    }
    
    
    /**
     * Deletes an access role.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     *
     * @throws \ProtectedRoleException
     */
    public function delete()
    {
        if ($this->protected) {
            throw new ProtectedRoleException(new IdType($this->id));
        }
        
        $this->deleter->delete(new IdType($this->id));
        
        return $this;
    }
    
    
    /**
     * Returns the role id.
     *
     * @return int Role ID.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Returns the role names as a collection.
     *
     * @return KeyValueCollection Role name in all available languages.
     */
    public function getName()
    {
        return $this->name;
    }
    
    
    /**
     * Returns the role descriptions as a collection.
     *
     * @return KeyValueCollection Role description in all available languages.
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    
    /**
     * Returns the role sort order.
     *
     * @return int Sort order.
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
    
    
    /**
     * Sets the role ID.
     *
     * @param IdType $id Role ID.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Sets the role names.
     *
     * @param KeyValueCollection $name Role name.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setName(KeyValueCollection $name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    
    /**
     * Sets the role description.
     *
     * @param KeyValueCollection $description Role description.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setDescription(KeyValueCollection $description)
    {
        $this->description = $description;
        
        return $this;
    }
    
    
    /**
     * Sets the role sort order.
     *
     * @param IntType $sortOrder Role sort order.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setSortOrder(IntType $sortOrder)
    {
        $this->sortOrder = $sortOrder->asInt();
        
        return $this;
    }
    
    
    /**
     * Sets the deleting permission value for an unknown group.
     *
     * @param BoolType $permissionGranted Value of the deleting permission for unknown groups.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setDeletingUnknownGroupGranted(BoolType $permissionGranted)
    {
        $this->deletingUnknownGroupGranted = $permissionGranted->asBool();
        
        return $this;
    }
    
    
    /**
     * Sets the reading permission value for an unknown group.
     *
     * @param BoolType $permissionGranted Value of the reading permission for unknown groups.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setReadingUnknownGroupGranted(BoolType $permissionGranted)
    {
        $this->readingUnknownGroupGranted = $permissionGranted->asBool();
        
        return $this;
    }
    
    
    /**
     * Sets the writing permission value for an unknown group.
     *
     * @param BoolType $permissionGranted Value of the writing permission for unknown groups.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setWritingUnknownGroupGranted(BoolType $permissionGranted)
    {
        $this->writingUnknownGroupGranted = $permissionGranted->asBool();
        
        return $this;
    }
    
    
    /**
     * Stores this role into the database.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     *
     * @throws \ProtectedRoleException
     */
    public function store()
    {
        if ($this->protected) {
            throw new ProtectedRoleException(new IdType($this->id));
        }
        
        if ($this->id > 0) {
            $this->update();
        }
        
        $this->id = $this->writer->insert($this);
        
        return $this;
    }
    
    
    /**
     * Updates this role in the database.
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     *
     * @throws \ProtectedRoleException
     */
    public function update()
    {
        if ($this->protected) {
            throw new ProtectedRoleException(new IdType($this->id));
        }
        
        $this->writer->update($this);
        
        return $this;
    }
    
    
    /**
     * Returns the a permission for this role by a given group.
     *
     * @param $group  AdminAccessGroupInterface Admin access group.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface|null
     *                       Returns permission found or null if permission not found.
     */
    public function getPermissionByGroup(AdminAccessGroupInterface $group)
    {
        return $this->permissionReader->get($this, $group);
    }
    
    
    /**
     * Returns the a permission for this role by a given group collection.
     *
     * @param $groupCollection AdminAccessGroupCollection Collections of groups to find.
     *
     * @return AdminAccessPermissionCollection Returns permissions collection.
     */
    public function getPermissionsByGroupCollection(AdminAccessGroupCollection $groupCollection)
    {
        return $this->permissionReader->getByGroupCollection($this, $groupCollection);
    }
    
    
    /**
     * Sets the protected value.
     *
     * @param BoolType $value
     *
     * @return AdminAccessRoleInterface Returns same instance for chained method calls.
     */
    public function setProtected(BoolType $value)
    {
        $this->protected = $value->asBool();
        
        return $this;
    }
    
    
    /**
     * Returns the protected value.
     *
     * @return bool
     */
    public function getProtected()
    {
        return $this->protected;
    }
}
