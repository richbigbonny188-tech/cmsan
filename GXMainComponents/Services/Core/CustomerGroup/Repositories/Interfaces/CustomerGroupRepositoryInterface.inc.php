<?php
/* --------------------------------------------------------------
   CustomerGroupRepositoryInterface.inc.php 2018-02-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupRepositoryInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
Interface CustomerGroupRepositoryInterface
{
    /**
     * Saves customer group in database.
     *
     * @param \CustomerGroup $customerGroup Customer group entity to be saved.
     *
     * @return $this|\CustomerGroupRepositoryInterface Same instance for chained method calls.
     */
    public function store(CustomerGroup $customerGroup);
    
    
    /**
     * Updates customer group in database.
     *
     * @param \CustomerGroup $customerGroup Customer group entity to be saved.
     *
     * @return $this|\CustomerGroupRepositoryInterface Same instance for chained method calls.
     */
    public function update(CustomerGroup $customerGroup);
    
    
    /**
     * Deletes customer group from database.
     *
     * @param \CustomerGroup $customerGroup Customer group to be deleted.
     *
     * @return $this|\CustomerGroupRepositoryInterface Same instance for chained method calls.
     */
    public function delete(CustomerGroup $customerGroup);
    
    
    /**
     * Create base data from chosen personal offers table.
     *
     * @param \IntType $customerGroupId
     * @param \IntType $baseId
     *
     * @return \CustomerGroupRepository
     */
    
    public function createBase(IntType $customerGroupId, IntType $baseId);
}