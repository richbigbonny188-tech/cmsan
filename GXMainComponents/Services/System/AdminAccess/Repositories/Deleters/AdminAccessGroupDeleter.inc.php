<?php

/* --------------------------------------------------------------
    AdminAccessGroupDeleter.inc.php 2018-01-22
    Gambio GmbH
    http://www.gambio.de
    Copyright (c) 2017 Gambio GmbH
    Released under the GNU General Public License (Version 2)
    [http://www.gnu.org/licenses/gpl-2.0.html]
    --------------------------------------------------------------
*/

/**
 * Class AdminAccessGroupDeleter
 *
 * @category   System
 * @package    AdminAccess
 * @subpackage Deleters
 */
class AdminAccessGroupDeleter implements AdminAccessGroupDeleterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $queryBuilder;
    
    /**
     * @var string
     */
    protected $groupsTable;
    
    /**
     * @var string
     */
    protected $groupDescriptionsTable;
    
    /**
     * @var string
     */
    protected $groupItemsTable;
    
    /**
     * @var string
     */
    protected $permissionsTable;
    
    
    /**
     * AdminAccessGroupDeleter constructor.
     *
     * @param CI_DB_query_builder $queryBuilder Query builder.
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder           = $queryBuilder;
        $this->groupsTable            = 'admin_access_groups';
        $this->groupDescriptionsTable = 'admin_access_group_descriptions';
        $this->groupItemsTable        = 'admin_access_group_items';
        $this->permissionsTable       = 'admin_access_permissions';
    }
    
    
    /**
     * Deletes a group by a given group ID.
     *
     * @param IdType $id Group ID.
     *
     * @return AdminAccessGroupDeleterInterface Returns same instance for chained method calls.
     */
    public function delete(IdType $id)
    {
        $this->queryBuilder->delete($this->groupsTable, ['admin_access_group_id' => $id->asInt()]);
        $this->queryBuilder->delete($this->groupDescriptionsTable, ['admin_access_group_id' => $id->asInt()]);
        $this->queryBuilder->delete($this->groupItemsTable, ['admin_access_group_id' => $id->asInt()]);
        $this->queryBuilder->delete($this->permissionsTable, ['admin_access_group_id' => $id->asInt()]);
        
        return $this;
    }
}
