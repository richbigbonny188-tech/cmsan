<?php

/* --------------------------------------------------------------
    AdminAccessPermission.inc.php 2018-01-22
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2017 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------
*/

/**
 * Class AdminAccessPermission
 *
 * A permission is the bridge element between a group and a role.
 * It assigns these two elements a values for a reading, writing and deleting (permission).
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Entities
 */
class AdminAccessPermission
    implements AdminAccessPermissionPresentationInterface, AdminAccessPermissionPersistenceInterface, AdminAccessPermissionInterface
{
    /**
     * @var AdminAccessPermissionDeleterInterface
     */
    protected $permissionDeleter;
    
    /**
     * @var AdminAccessPermissionWriterInterface
     */
    protected $permissionWriter;
    
    /**
     * @var AdminAccessGroupReaderInterface
     */
    protected $groupReader;
    
    /**
     * @var AdminAccessPermissionInterface
     */
    protected $permissionReader;
    
    /**
     * @var AdminAccessRoleInterface
     */
    protected $role;
    
    /**
     * @var AdminAccessGroupInterface
     */
    protected $group;
    
    /**
     * @var int
     */
    protected $groupId;
    
    /**
     * @var bool
     */
    protected $readingGranted;
    
    /**
     * @var bool
     */
    protected $writingGranted;
    
    /**
     * @var bool
     */
    protected $deletingGranted;
    
    
    /**
     * AdminAccessPermission constructor.
     *
     * @param AdminAccessPermissionDeleterInterface $permissionDeleter Permission deleter.
     * @param AdminAccessPermissionWriterInterface  $permissionWriter  Permission writer.
     * @param AdminAccessGroupReaderInterface       $groupReader       Group reader.
     * @param AdminAccessPermissionReaderInterface  $permissionReader  Permission reader.
     */
    public function __construct(
        AdminAccessPermissionDeleterInterface $permissionDeleter,
        AdminAccessPermissionWriterInterface $permissionWriter,
        AdminAccessGroupReaderInterface $groupReader,
        AdminAccessPermissionReaderInterface $permissionReader
    ) {
        $this->permissionDeleter = $permissionDeleter;
        $this->permissionWriter  = $permissionWriter;
        $this->groupReader       = $groupReader;
        $this->permissionReader  = $permissionReader;
        
        $this->groupId = 0;
        
        $this->readingGranted  = false;
        $this->writingGranted  = false;
        $this->deletingGranted = false;
    }
    
    
    /**
     * Deletes a permission.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function delete()
    {
        $this->permissionDeleter->deleteByIds(new IdType($this->role->getId()), new IdType($this->groupId));
        
        return $this;
    }
    
    
    /**
     * Grants deleting access.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function grantDeleting()
    {
        $this->deletingGranted = true;
        
        return $this;
    }
    
    
    /**
     * Grants reading access.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function grantReading()
    {
        $this->readingGranted = true;
        
        return $this;
    }
    
    
    /**
     * Grants writing access.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function grantWriting()
    {
        $this->writingGranted = true;
        
        return $this;
    }
    
    
    /**
     * Removes deleting access.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function removeDeleting()
    {
        $this->deletingGranted = false;
        
        return $this;
    }
    
    
    /**
     * Removes reading access.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function removeReading()
    {
        $this->readingGranted = false;
        
        return $this;
    }
    
    
    /**
     * Removes writing access.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function removeWriting()
    {
        $this->writingGranted = false;
        
        return $this;
    }
    
    
    /**
     * Sets group ID.
     *
     * @param IdType $groupId Group ID.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function setGroupId(IdType $groupId)
    {
        $this->groupId = $groupId->asInt();
        
        return $this;
    }
    
    
    /**
     * Stores a permission into the database.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function store()
    {
        $this->permissionWriter->update($this);
        
        return $this;
    }
    
    
    /**
     * Updates a permission in the database.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function update()
    {
        $this->store();
        
        return $this;
    }
    
    
    /**
     * Returns the group of this permission.
     *
     * @return AdminAccessGroupInterface Group object.
     *
     * @throws \GroupNotFoundException
     */
    public function getGroup()
    {
        if (!isset($this->group) && $this->groupId > 0) {
            $this->group = $this->groupReader->getById(new IdType($this->groupId));
        }
        
        return $this->group;
    }
    
    
    /**
     * Sets the group of this permission.
     *
     * @param AdminAccessGroupInterface $accessGroup Group object.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function setGroup(AdminAccessGroupInterface $accessGroup)
    {
        $this->group = $accessGroup;
        
        return $this;
    }
    
    
    /**
     * Returns the role of this permission.
     *
     * @return AdminAccessRoleInterface Role object.
     */
    public function getRole()
    {
        return $this->role;
    }
    
    
    /**
     * Sets the role of this permission.
     *
     * @param AdminAccessRoleInterface $accessRole Role object.
     *
     * @return AdminAccessPermissionPresentationInterface|AdminAccessPermissionPersistenceInterface Returns same
     *                                                                                              instance for
     *                                                                                              chained method
     *                                                                                              calls.
     */
    public function setRole(AdminAccessRoleInterface $accessRole)
    {
        $this->role = $accessRole;
        
        return $this;
    }
    
    
    /**
     * Checks if deleting is granted.
     *
     * @return bool True if deleting is granted, false otherwise.
     */
    public function isDeletingGranted()
    {
        return $this->deletingGranted;
    }
    
    
    /**
     * Checks if reading is granted.
     *
     * @return bool True if reading is granted, false otherwise.
     */
    public function isReadingGranted()
    {
        return $this->readingGranted;
    }
    
    
    /**
     * Checks if writing is granted.
     *
     * @return bool True if writing is granted, false otherwise.
     */
    public function isWritingGranted()
    {
        return $this->writingGranted;
    }
}
