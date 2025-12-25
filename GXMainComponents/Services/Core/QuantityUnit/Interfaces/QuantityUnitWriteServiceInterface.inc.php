<?php

/* --------------------------------------------------------------
   QuantityUnitWriteServiceInterface.inc.php 2017-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuantityUnitWriteServiceInterface
 *
 * @category   System
 * @package    QuantityUnit
 */
interface QuantityUnitWriteServiceInterface
{
    /**
     * Saves quantity unit entity in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be saved.
     *
     * @return $this|\QuantityUnitWriteServiceInterface Same instance for chained method calls.
     */
    public function save(QuantityUnitInterface $quantityUnit);
    
    
    /**
     * Deletes quantity unit entity from database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be deleted.
     *
     * @return $this|\QuantityUnitWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(QuantityUnitInterface $quantityUnit);
    
    
    /**
     * Creates Quantity unit entity.
     *
     * @return \GXEngineQuantityUnit New quantity unit entity.
     */
    public function createQuantityUnit();
    
}