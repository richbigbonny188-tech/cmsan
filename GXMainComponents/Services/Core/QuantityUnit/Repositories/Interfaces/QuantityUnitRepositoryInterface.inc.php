<?php

/* --------------------------------------------------------------
   QuantityUnitRepositoryInterface.inc.php 2017-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuantityUnitRepositoryInterface
 *
 * @category   System
 * @package    QuantityUnit
 * @subpackage Repositories
 */
interface QuantityUnitRepositoryInterface
{
    /**
     * Returns quantity unit entity by the given id.
     *
     * @param \IdType $quantityUnitId IdType of entity to be returned.
     *
     * @return \QuantityUnitInterface
     */
    public function getById(IdType $quantityUnitId);
    
    
    /**
     * Returns all quantity unit as collection.
     *
     * @return \QuantityUnitCollection Quantity unit collection.
     */
    public function getAll();
    
    
    /**
     * Saves quantity unit entity in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be save.
     *
     * @return \QuantityUnitRepositoryInterface Same instance for chained method calls.
     */
    public function save(QuantityUnitInterface $quantityUnit);
    
    
    /**
     * Deletes quantity unit entity from database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit entity to be deleted.
     *
     * @return \QuantityUnitRepositoryInterface Same instance for chained method calls.
     */
    public function delete(QuantityUnitInterface $quantityUnit);
    
    
    /**
     * Creates Quantity unit entity.
     *
     * @return \GXEngineQuantityUnit New quantity unit entity.
     */
    public function createQuantityUnit();
}