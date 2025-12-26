<?php

/* --------------------------------------------------------------
   QuantityUnitStorageInterface.inc.php 2017-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuantityUnitStorageInterface
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Repositories
 */
interface QuantityUnitStorageInterface
{
    /**
     * Returns quantity unit entity data by the given id.
     *
     * @param \IdType $quantityUnitId
     *
     * @return array
     */
    public function getById(IdType $quantityUnitId);
    
    
    /**
     * Returns all quantity unit entities data as array.
     *
     * @return array
     */
    public function getAll();
    
    
    /**
     * Saves quantity unit entity data in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be saved.
     *
     * @return \QuantityUnitStorageInterface Same instance for chained method calls.
     */
    public function save(QuantityUnitInterface $quantityUnit);
    
    
    /**
     * Deletes quantity unit entity data in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be delete.
     *
     * @return \QuantityUnitStorageInterface Same instance for chained method calls.
     */
    public function delete(QuantityUnitInterface $quantityUnit);
    
    
    /**
     * Updates quantity unit entity data in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be updated.
     *
     * @return \QuantityUnitStorageInterface Same instance for chained method calls.
     */
    public function update(QuantityUnitInterface $quantityUnit);
}