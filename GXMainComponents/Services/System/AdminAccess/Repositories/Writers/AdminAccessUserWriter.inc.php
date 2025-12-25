<?php

/* --------------------------------------------------------------
   AdminAccessUserWriter.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessUserWriter
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
class AdminAccessUserWriter implements AdminAccessUserWriterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * @var string
     */
    protected $accessUsersTable;
    
    
    /**
     * AdminAccessUserWriter constructor.
     *
     * @param CI_DB_query_builder $db Query builder.
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db               = $db;
        $this->accessUsersTable = 'admin_access_users';
    }
    
    
    /**
     * Stores the user roles into the database.
     *
     * @param IdType                    $customerId Customer ID.
     * @param AdminAccessRoleCollection $roles      Roles collection.
     *
     * @return AdminAccessUserWriterInterface Returns same instance for chained method calls.
     */
    public function store(IdType $customerId, AdminAccessRoleCollection $roles)
    {
        // we don't want to loose data if something goes wrong
        $this->db->trans_start();
        
        // delete all users from admin_access_user by $customerId
        $this->db->delete($this->accessUsersTable, ['customer_id' => $customerId->asInt()]);
        
        if ($roles->count() > 0) {
            foreach ($roles->getArray() as $role) {
                $rolesDataArray = [
                    'admin_access_role_id' => $role->getId(),
                    'customer_id'          => $customerId,
                ];
                $this->db->replace($this->accessUsersTable, $rolesDataArray);
            }
        }
        
        $this->db->trans_complete();
        
        return $this;
    }
}
