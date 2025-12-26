<?php
/* --------------------------------------------------------------
   ManufacturerWriterInterface.inc.php 2017-08-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ManufacturerWriterInterface
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Repositories
 */
interface ManufacturerWriterInterface
{
    /**
     * Saves manufacturer entity data in database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer to be saved.
     *
     * @return $this|\ManufacturerWriterInterface Same instance for chained method calls.
     */
    public function store(ManufacturerInterface $manufacturer);
    
    
    /**
     * Updates manufacturer entity data in database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer to be updated.
     *
     * @return $this|\ManufacturerWriterInterface Same instance for chained method calls.
     */
    public function update(ManufacturerInterface $manufacturer);
    
    
}