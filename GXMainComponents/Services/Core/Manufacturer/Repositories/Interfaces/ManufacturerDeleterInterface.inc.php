<?php
/* --------------------------------------------------------------
   ManufacturerDeleterInterface.inc.php 2017-08-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ManufacturerDeleterInterface
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Repositories
 */
interface ManufacturerDeleterInterface
{
    /**
     * Deletes manufacturer entity data in database.
     *
     * @param \ManufacturerInterface $manufacturer manufacturer entity to be delete.
     *
     * @return $this|\ManufacturerDeleterInterface Same instance for chained method calls.
     */
    public function delete(ManufacturerInterface $manufacturer);
    
}