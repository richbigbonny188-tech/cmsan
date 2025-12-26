<?php

/* --------------------------------------------------------------
   AdminAccessPermissionWriter.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessPermissionWriter
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
class AdminAccessPermissionWriter implements AdminAccessPermissionWriterInterface
{
    
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var AdminAccessPermissionFactoryInterface
     */
    protected $permissionFactory;
    
    /**
     * @var string
     */
    protected $permissionTable;
    
    
    /**
     * AdminAccessPermissionReader constructor.
     *
     * @param CI_DB_query_builder $queryBuilder Query builder.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        
        $this->permissionTable = 'admin_access_permissions';
    }
    
    
    /**
     * Stores a permission into the database.
     *
     * @param AdminAccessPermission $permission Permission.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function insert(AdminAccessPermission $permission)
    {
        $data = [
            'admin_access_role_id'  => $permission->getRole()->getId(),
            'admin_access_group_id' => $permission->getGroup()->getId(),
            'reading_granted'       => $permission->isReadingGranted(),
            'writing_granted'       => $permission->isWritingGranted(),
            'deleting_granted'      => $permission->isDeletingGranted(),
        ];
        $this->queryBuilder->replace($this->permissionTable, $data);
        
        return $this;
    }
    
    
    /**
     * Updates a permission in the database.
     *
     * @param AdminAccessPermission $permission Permission.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function update(AdminAccessPermission $permission)
    {
        $this->insert($permission);
        
        return $this;
    }
    
    
    /**
     * Updates the deleting permission flag of a permission in the database.
     *
     * @param IdType   $roleId          Role ID.
     * @param IdType   $groupId         Group ID.
     * @param BoolType $deletingGranted Value for the deleting permission flag.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function updateDeletingPermission(IdType $roleId, IdType $groupId, BoolType $deletingGranted)
    {
        $data = [
            'admin_access_role_id'  => $roleId->asInt(),
            'admin_access_group_id' => $groupId->asInt(),
            'deleting_granted'      => $deletingGranted->asBool(),
        ];
        $this->_updateAccessFlags($data);
        
        return $this;
    }
    
    
    /**
     * Updates the reading permission flag of a permission in the database.
     *
     * @param IdType   $roleId         Role ID.
     * @param IdType   $groupId        Group ID.
     * @param BoolType $readingGranted Value for the reading permission flag.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function updateReadingPermission(IdType $roleId, IdType $groupId, BoolType $readingGranted)
    {
        $data = [
            'admin_access_role_id'  => $roleId->asInt(),
            'admin_access_group_id' => $groupId->asInt(),
            'reading_granted'       => $readingGranted->asBool(),
        ];
        
        $this->_updateAccessFlags($data);
        
        return $this;
    }
    
    
    /**
     * Updates the writing permission flag of a permission in the database.
     *
     * @param IdType   $roleId         Role ID.
     * @param IdType   $groupId        Group ID.
     * @param BoolType $writingGranted Value for the writing permission flag.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    public function updateWritingPermission(IdType $roleId, IdType $groupId, BoolType $writingGranted)
    {
        $data = [
            'admin_access_role_id'  => $roleId->asInt(),
            'admin_access_group_id' => $groupId->asInt(),
            'writing_granted'       => $writingGranted->asBool(),
        ];
        
        $this->_updateAccessFlags($data);
        
        return $this;
    }
    
    
    /**
     * Updates the access flags of a permission in the database.
     *
     * @param array $data Permission data.
     *
     * @return AdminAccessPermissionWriterInterface Returns same instance for chained method calls.
     */
    protected function _updateAccessFlags(array $data)
    {
        $duplicate_data = [];
        
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $duplicate_data[] = sprintf("%s='%s'", $key, addslashes($value));
            }
            
            $sql = sprintf('%s ON DUPLICATE KEY UPDATE %s',
                           $this->queryBuilder->insert_string($this->permissionTable, $data),
                           implode(',', $duplicate_data));
            
            $this->queryBuilder->query($sql);
        }
        
        return $this;
    }
}
