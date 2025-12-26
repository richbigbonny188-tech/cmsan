<?php

/* --------------------------------------------------------------
   QuantityUnitReadServiceInterface.inc.php 2017-08-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface QuantityUnitReadServiceInterface
 *
 * @category   System
 * @package    QuantityUnit
 */
interface QuantityUnitReadServiceInterface
{
    /**
     * Returns quantity unit entity by given id.
     *
     * @param \IdType $quantityUnitId Quantity unit entity to be given.
     *
     * @return \QuantityUnitInterface
     */
    public function getById(IdType $quantityUnitId);
    
    
    /**
     * Returns quantity unit entities as collection.
     *
     * @return \QuantityUnitCollection
     */
    public function getAll();
}