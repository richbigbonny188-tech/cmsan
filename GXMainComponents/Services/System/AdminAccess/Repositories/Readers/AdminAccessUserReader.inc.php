<?php

/* --------------------------------------------------------------
   AdminAccessUserReader.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessUserReader
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Readers
 */
class AdminAccessUserReader implements AdminAccessUserReaderInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var AdminAccessUserFactoryInterface
     */
    protected $userFactory;
    
    /**
     * @var \AdminAccessRoleReaderInterface
     */
    protected $roleReader;
    
    /**
     * @var string
     */
    protected $usersTable;
    
    /**
     * @var string
     */
    protected $usersRolesTable;
    
    
    /**
     * AdminAccessUserReader constructor.
     *
     * @param CI_DB_query_builder             $queryBuilder Query builder.
     * @param AdminAccessUserFactoryInterface $userFactory  User factory.
     * @param AdminAccessRoleReaderInterface  $roleReader   Role reader.
     */
    public function __construct(
        CI_DB_query_builder $queryBuilder,
        AdminAccessUserFactoryInterface $userFactory,
        AdminAccessRoleReaderInterface $roleReader
    ) {
        $this->queryBuilder = $queryBuilder;
        $this->userFactory  = $userFactory;
        $this->roleReader   = $roleReader;
        
        $this->usersTable      = 'customers';
        $this->usersRolesTable = 'admin_access_users';
    }
    
    
    /**
     * Returns an user by a given customer ID.
     *
     * @param IdType $customerId Customer ID.
     *
     * @return AdminAccessUserInterface User object.
     *
     * @throws UserNotFoundException If user not found.
     *
     * @throws \RoleNotFoundException
     */
    public function getByCustomerId(IdType $customerId)
    {
        $userData = $this->queryBuilder->select()
            ->from($this->usersTable)
            ->where(['customers_id' => $customerId->asInt()])
            ->get()
            ->row_array();
        
        if (empty($userData)) {
            throw new UserNotFoundException($customerId);
        }
        
        return $this->_createAdminUserObject($userData);
    }
    
    
    /**
     * Creates a user object by a given array with the user data.
     *
     * @param array $userData User data.
     *
     * @return AdminAccessUserInterface User object.
     *
     * @throws \RoleNotFoundException
     */
    protected function _createAdminUserObject(array $userData)
    {
        $userId = new IdType($userData['customers_id']);
        
        $user = $this->userFactory->createAdminAccessUser($userId);
        
        $roles = $this->roleReader->getByCustomerId($userId);
        $user->setRoles($roles);
        
        return $user;
    }
}
