<?php
/* --------------------------------------------------------------
   CustomerGroupDeleter.inc.php 2018-02-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupDeleter
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
class CustomerGroupDeleter implements CustomerGroupDeleterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Deletes customer group entity data in database with personal offer table.
     *
     * @param \CustomerGroup $customerGroup Customer group entity to be delete.
     *
     * @return $this|\CustomerGroupDeleterInterface Same instance for chained method calls.
     */
    public function delete(CustomerGroup $customerGroup)
    {
        $customerGroupId = $customerGroup->getId();
        $this->_changeCustomersStatusIdToDefault($customerGroupId)
            ->_dropPersonalOffersTable($customerGroupId)
            ->_deleteCustomersStatusTableRow($customerGroupId)
            ->_dropGroupPermissionColumn($customerGroupId, 'categories')
            ->_dropGroupPermissionColumn($customerGroupId, 'products');
        
        return $this;
    }
    
    
    /**
     * Drops personal offers table by id.
     *
     * @param $customerGroupId int
     *
     * @return $this
     */
    protected function _dropPersonalOffersTable($customerGroupId)
    {
        $this->queryBuilder->query('DROP TABLE personal_offers_by_customers_status_' . $customerGroupId);
        
        return $this;
    }
    
    
    /**
     * Deletes customer status row by id.
     *
     * @param $customerGroupId int
     *
     * @return $this
     */
    protected function _deleteCustomersStatusTableRow($customerGroupId)
    {
        $this->queryBuilder->delete('customers_status', ['customers_status_id' => $customerGroupId]);
        
        return $this;
    }
    
    
    /**
     * Drops group permission column by id and given table name.
     *
     * @param $customerGroupId
     * @param $table
     *
     * @return $this
     */
    protected function _dropGroupPermissionColumn($customerGroupId, $table)
    {
        $this->queryBuilder->query('ALTER TABLE `' . $table . '` DROP group_permission_' . $customerGroupId);
        
        return $this;
    }
    
    
    /**
     * Change customers status id.
     *
     * @param $customerGroupId
     *
     * @return $this
     */
    protected function _changeCustomersStatusIdToDefault($customerGroupId)
    {
        $defaultCustomerGroupId = $this->queryBuilder->query('SELECT `value` FROM gx_configurations WHERE `key` = "configuration/DEFAULT_CUSTOMERS_STATUS_ID"')
                                      ->result_array()[0]['value'];
        $customersStatusIds     = $this->queryBuilder->query('SELECT customers_id FROM customers WHERE customers_status = '
                                                             . $customerGroupId)->result_array();
        foreach ($customersStatusIds as $customersStatusId) {
            $this->queryBuilder->query('UPDATE customers SET customers_status = ' . (int)$defaultCustomerGroupId
                                       . ' WHERE customers_id = ' . (int)$customersStatusId['customers_id']);
        }
        
        return $this;
    }
}