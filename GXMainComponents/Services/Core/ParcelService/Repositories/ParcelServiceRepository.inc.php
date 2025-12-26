<?php
/* --------------------------------------------------------------
   ParcelServiceRepository.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceRepository
 */
class ParcelServiceRepository implements ParcelServiceRepositoryInterface
{
    /**
     * @var \ParcelServiceMapperInterface
     */
    protected $mapper;
    
    
    /**
     * ParcelServiceRepository constructor.
     *
     * @param \ParcelServiceMapperInterface $mapper
     */
    public function __construct(\ParcelServiceMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns a limited collection of parcel services.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection
     */
    public function findAll(\Pager $pager = null, array $sorters = [])
    {
        return $this->mapper->findAll($pager, $sorters);
    }
    
    
    /**
     * Returns a limited collection of parcel services found by the given search condition.
     *
     * @param \ParcelServiceSearchCondition $searchCondition
     * @param \Pager|null                   $pager   (Optional) Pager object with pagination information
     * @param array                         $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection
     */
    public function findBy(\ParcelServiceSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        return $this->mapper->findBy($searchCondition, $pager, $sorters);
    }
    
    
    /**
     * Returns a parcel services found by its ID.
     *
     * @param \ParcelServiceId $parcelServiceId
     *
     * @return \ParcelServiceInterface|null
     */
    public function findById(\ParcelServiceId $parcelServiceId)
    {
        return $this->mapper->findById($parcelServiceId);
    }
    
    
    /**
     * Saves a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function save(\ParcelServiceInterface $parcelService)
    {
        return $this->mapper->insert($parcelService);
    }
    
    
    /**
     * Updates a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function update(\ParcelServiceInterface $parcelService)
    {
        return $this->mapper->update($parcelService);
    }
    
    
    /**
     * Deletes a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     */
    public function delete(\ParcelServiceInterface $parcelService)
    {
        $this->mapper->delete($parcelService);
    }
}