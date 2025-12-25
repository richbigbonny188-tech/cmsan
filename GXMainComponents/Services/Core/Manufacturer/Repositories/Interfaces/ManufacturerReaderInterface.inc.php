<?php
/* --------------------------------------------------------------
   ManufacturerReadInterface.inc.php 2019-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ManufacturerReaderInterface
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Repositories
 */
interface ManufacturerReaderInterface
{
    /**
     * Returns all manufacturer entities data as array.
     *
     * @return array
     */
    public function getAll();
    
    
    /**
     * Searches for manufacturer entities that respects the given search condition and returns their data as an array.
     *
     * @param \ManufacturerSearchCondition $searchCondition
     * @param \Pager|null                  $pager   (Optional) Pager object with pagination information
     * @param array                        $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array
     */
    public function search(ManufacturerSearchCondition $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns manufacturer entity data by the given id.
     *
     * @param \IdType $manufacturerId
     *
     * @return array
     * @throws \EntityNotFoundException If no record was found with provided manufacturer entity id.
     */
    public function getById(IdType $manufacturerId);
}