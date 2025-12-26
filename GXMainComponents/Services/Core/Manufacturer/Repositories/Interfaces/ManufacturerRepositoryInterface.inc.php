<?php
/* --------------------------------------------------------------
   ManufacturerRepositoryInterface.inc.php 2019-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ManufacturerRepositoryInterface
 *
 * @category   System
 * @package    Manufacturer
 * @subpackage Repositories
 */
interface ManufacturerRepositoryInterface
{
    /**
     * Returns all manufacturer as collection.
     *
     * @return \ManufacturerCollection Manufacturer collection.
     */
    public function getAll();
    
    
    /**
     * Searches for manufacturer entities that respects the given search condition and returns their data as a
     * collection.
     *
     * @param \ManufacturerSearchCondition $searchCondition
     * @param \Pager|null                  $pager   (Optional) Pager object with pagination information
     * @param array                        $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ManufacturerCollection Manufacturer collection.
     */
    public function search(ManufacturerSearchCondition $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns manufacturer entity by the given id.
     *
     * @param \IdType $manufacturerId IdType of entity to be returned.
     *
     * @return \ManufacturerInterface
     * @throws \EntityNotFoundException If no record was found with provided manufacturer entity id.
     */
    public function getById(IdType $manufacturerId);
    
    
    /**
     * Saves manufacturer entity in database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer entity to be saved.
     *
     * @return \ManufacturerRepositoryInterface Same instance for chained method calls.
     */
    public function save(ManufacturerInterface $manufacturer);
    
    
    /**
     * Deletes manufacturer entity from database.
     *
     * @param \ManufacturerInterface $manufacturer Manufacturer entity to be deleted.
     *
     * @return \ManufacturerRepositoryInterface Same instance for chained method calls.
     */
    public function delete(ManufacturerInterface $manufacturer);
    
    
    /**
     * creates manufacturer entity.
     *
     * @return \Manufacturer New manufacturer entity.
     */
    public function createManufacturer();
}