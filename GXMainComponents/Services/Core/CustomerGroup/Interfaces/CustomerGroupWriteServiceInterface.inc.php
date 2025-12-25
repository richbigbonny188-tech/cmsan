<?php
/* --------------------------------------------------------------
   CustomerGroupWriteServiceInterface.inc.php 2018-02-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupWriteServiceInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Interfaces
 */
interface CustomerGroupWriteServiceInterface
{
    /**
     * Saves customer group entity in database.
     *
     * @param \CustomerGroupInterface $customerGroup Customer group unit to be saved.
     *
     * @return $this|\CustomerGroupWriteServiceInterface Same instance for chained method calls.
     */
    public function store(CustomerGroupInterface $customerGroup);
    
    
    /**
     * Deletes customer group entity data in database with personal offer table.
     *
     * @param \CustomerGroupInterface $customerGroup Customer group to be deleted.
     *
     * @return $this|\CustomerGroupWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(CustomerGroupInterface $customerGroup);
    
    
    /**
     * Returns an customer group factory.
     *
     * @return CustomerGroupFactory
     */
    public function getFactory();
}