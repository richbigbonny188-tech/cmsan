<?php

/* --------------------------------------------------------------
   AdminAccessUserDeleter.inc.php 2018-01-22
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AdminAccessUserDeleter
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Writers
 */
class AdminAccessUserDeleter implements AdminAccessUserDeleterInterface
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
     * Removes an user from the database by a given user id.
     *
     * @param IdType $userId User id.
     *
     * @return AdminAccessUserDeleterInterface Returns same instance for chained method calls.
     */
    public function deleteById(IdType $userId)
    {
        $this->db->delete($this->accessUsersTable, ['customer_id' => $userId->asInt()]);
        
        return $this;
    }
}
