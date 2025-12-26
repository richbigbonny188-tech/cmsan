<?php
/* --------------------------------------------------------------
   ManufacturerReadService.inc.php 2019-06-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ManufacturerReadService
 *
 * @category   System
 * @package    Manufacturer
 */
class ManufacturerReadService implements ManufacturerReadServiceInterface
{
    /**
     * @var \ManufacturerRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ManufacturerReadService constructor.
     *
     * @param \ManufacturerRepositoryInterface $repository
     */
    public function __construct(ManufacturerRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns manufacturer entities as collection.
     *
     * @return \ManufacturerCollection
     */
    public function getAll()
    {
        return $this->repository->getAll();
    }
    
    
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
    public function search(ManufacturerSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        return $this->repository->search($searchCondition, $pager, $sorters);
    }
    
    
    /**
     * Returns manufacturer entity by given id.
     *
     * @param \IdType $manufacturerId
     *
     * @return \ManufacturerInterface
     * @throws \EntityNotFoundException If no record was found with provided manufacturer entity id.
     */
    public function getById(IdType $manufacturerId)
    {
        return $this->repository->getById($manufacturerId);
    }
}