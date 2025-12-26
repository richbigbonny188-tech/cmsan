<?php

/* --------------------------------------------------------------
   AdminAccessRoleReader.inc.php 2020-02-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessRoleReader
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Readers
 */
class AdminAccessRoleReader implements AdminAccessRoleReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var AdminAccessRoleFactoryInterface
     */
    protected $roleFactory;
    
    /**
     * @var string
     */
    protected $rolesTable;
    
    /**
     * @var string
     */
    protected $rolesMetaTable;
    
    /**
     * @var string
     */
    protected $customersTable;
    
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    
    /**
     * AdminAccessRoleReader constructor.
     *
     * @param CI_DB_query_builder             $queryBuilder     Query builder.
     * @param AdminAccessRoleFactoryInterface $roleFactory      Role Factory.
     * @param LanguageProviderInterface       $languageProvider Language provider.
     */
    public function __construct(
        CI_DB_query_builder $queryBuilder,
        AdminAccessRoleFactoryInterface $roleFactory,
        LanguageProviderInterface $languageProvider
    ) {
        $this->queryBuilder     = $queryBuilder;
        $this->roleFactory      = $roleFactory;
        $this->languageProvider = $languageProvider;
        
        $this->rolesTable     = 'admin_access_roles';
        $this->rolesMetaTable = 'admin_access_role_descriptions';
        $this->customersTable = 'admin_access_users';
    }
    
    
    /**
     * Returns all available roles as a role collection.
     *
     * @param IdType $roleId Role ID.
     *
     * @return AdminAccessRoleInterface Role object.
     * @throws RoleNotFoundException When role is not found in db.
     */
    public function getById(IdType $roleId)
    {
        $roleData = $this->queryBuilder->select()->from($this->rolesTable)->where('admin_access_role_id',
                                                                                  $roleId->asInt())->get()->row_array();
        
        if (empty($roleData)) {
            throw new RoleNotFoundException($roleId);
        }
        
        $roleData['metaData'] = $this->_getRoleMetaData($roleData['admin_access_role_id']);
        
        return $this->_createAdminAccessRoleByArray($roleData);
    }
    
    
    /**
     * Returns all roles of a certain user by a given customer ID.
     *
     * @return AdminAccessRoleCollection Role collection with all available roles.
     */
    public function getAll()
    {
        $result = $this->queryBuilder->select()->from($this->rolesTable)->order_by('sort_order')->get()->result_array();
        
        foreach ($result as $key => $value) {
            $result[$key]['metaData'] = $this->_getRoleMetaData($value['admin_access_role_id']);
        }
        
        $rolesArray = [];
        
        foreach ($result as $roleData) {
            $rolesArray[] = $this->_createAdminAccessRoleByArray($roleData);
        }
        
        $roleCollection = $this->roleFactory->createAdminAccessRoleCollection();
        
        foreach ($rolesArray as $role) {
            $roleCollection->add($role);
        }
        
        return $roleCollection;
    }
    
    
    /**
     * Returns a role object by a given role ID.
     *
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessRoleCollection Role collection with all roles of a certain user.
     *
     * @throws \RoleNotFoundException
     */
    public function getByCustomerId(IdType $customerId)
    {
        $resultArray = $this->queryBuilder->select()
            ->from($this->customersTable)
            ->join($this->rolesTable,
                   $this->customersTable . '.admin_access_role_id = ' . $this->rolesTable . '.admin_access_role_id',
                   'left')
            ->where('customer_id', $customerId->asInt())
            ->order_by($this->rolesTable . '.sort_order')
            ->get()
            ->result_array();
        
        $roleCollection = $this->roleFactory->createAdminAccessRoleCollection();
        
        foreach ($resultArray as $resultField) {
            $roleCollection->add($this->getById(new IdType($resultField['admin_access_role_id'])));
        }
        
        return $roleCollection;
    }
    
    
    /**
     * Returns the role descriptions from the database.
     *
     * @param int $roleId Role ID.
     *
     * @return array Array with role descriptions.
     */
    protected function _getRoleMetaData($roleId)
    {
        $roleMetaData = $this->queryBuilder->select()
            ->from($this->rolesMetaTable)
            ->where('admin_access_role_id',
                    $roleId)
            ->get()
            ->result_array();
        
        return $roleMetaData;
    }
    
    
    /**
     * Creates a role object by a given array with the role data.
     *
     * @param array $roleData Role data.
     *
     * @return AdminAccessRoleInterface Role object.
     */
    protected function _createAdminAccessRoleByArray(array $roleData)
    {
        $roleMetaData = $roleData['metaData'];
        $role         = $this->roleFactory->createAdminAccessRole();
        
        $role->setId(new IdType($roleData['admin_access_role_id']));
        $role->setSortOrder(new IntType($roleData['sort_order']));
        $role->setDeletingUnknownGroupGranted(new BoolType($roleData['deleting_unknown_group_granted']));
        $role->setReadingUnknownGroupGranted(new BoolType($roleData['reading_unknown_group_granted']));
        $role->setWritingUnknownGroupGranted(new BoolType($roleData['writing_unknown_group_granted']));
        $role->setProtected(new BoolType($roleData['protected']));
        
        $name        = [];
        $description = [];
        
        foreach ($roleMetaData as $meta) {
            $languageCode               = $this->languageProvider->getCodeById(new IdType($meta['language_id']))
                ->asString();
            $name[$languageCode]        = $meta['name'];
            $description[$languageCode] = $meta['description'];
        }
        
        $role->setName(new KeyValueCollection($name));
        $role->setDescription(new KeyValueCollection($description));
        
        return $role;
    }
}
