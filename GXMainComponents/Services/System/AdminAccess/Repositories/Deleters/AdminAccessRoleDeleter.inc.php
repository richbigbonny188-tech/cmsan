<?php

/* --------------------------------------------------------------
    AdminAccessRoleDeleter.inc.php 2018-01-22
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2017 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------
*/

/**
 * Class AdminAccessRoleDeleter
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Deleters
 */
class AdminAccessRoleDeleter implements AdminAccessRoleDeleterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected $rolesTable;
    
    /**
     * @var string
     */
    protected $rolesDescriptionsTable;
    
    /**
     * @var string
     */
    protected $permissionsTable;
    
    /**
     * @var string
     */
    protected $usersTable;
    
    
    /**
     * AdminAccessRoleDeleter constructor.
     *
     * @param CI_DB_query_builder $queryBuilder Query builder.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder           = $queryBuilder;
        $this->rolesTable             = 'admin_access_roles';
        $this->rolesDescriptionsTable = 'admin_access_role_descriptions';
        $this->permissionsTable       = 'admin_access_permissions';
        $this->usersTable             = 'admin_access_users';
    }
    
    
    /**
     * Deletes a role by a given role ID.
     *
     * @param IdType $id Role ID.
     *
     * @return AdminAccessRoleDeleterInterface Returns same instance for chained method calls.
     */
    public function delete(IdType $id)
    {
        $this->queryBuilder->delete($this->rolesTable, ['admin_access_role_id' => $id->asInt()]);
        $this->queryBuilder->delete($this->rolesDescriptionsTable, ['admin_access_role_id' => $id->asInt()]);
        $this->queryBuilder->delete($this->permissionsTable, ['admin_access_role_id' => $id->asInt()]);
        $this->queryBuilder->delete($this->usersTable, ['admin_access_role_id' => $id->asInt()]);
        
        return $this;
    }
}
