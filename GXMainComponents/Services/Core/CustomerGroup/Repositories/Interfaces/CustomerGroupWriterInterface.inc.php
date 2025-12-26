<?php
/* --------------------------------------------------------------
   CustomerGroupWriterInterface.inc.php 2018-02-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupWriterInterface
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
interface CustomerGroupWriterInterface
{
    /**
     * Saves customer group entity data in database.
     *
     * @param \CustomerGroupInterface $customerGroup Customer group to be saved.
     *
     * @return $this|\CustomerGroupWriterInterface Same instance for chained method calls.
     */
    public function store(CustomerGroupInterface $customerGroup);
    
    
    /**
     * Updates customer group entity data in database.
     *
     * @param \CustomerGroupInterface $customerGroup Customer group to be  updated.
     *
     * @return $this|\CustomerGroupWriterInterface Same instance for chained method calls.
     */
    public function update(CustomerGroupInterface $customerGroup);
    
    
    /**
     * Create base data from chosen personal offers table.
     *
     * @param \IntType $customerGroupId
     * @param \IntType $baseId
     *
     * @return \CustomerGroupWriter
     */
    public function createBase(IntType $customerGroupId, IntType $baseId);
    
}