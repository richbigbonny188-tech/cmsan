<?php
/* --------------------------------------------------------------
   ParcelServiceReadService.inc.php 2018-07-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServiceReadService
 */
class ParcelServiceReadService implements ParcelServiceReadServiceInterface
{
    /**
     * @var \ParcelServiceRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ParcelServiceReadService constructor.
     *
     * @param \ParcelServiceRepositoryInterface $repository
     */
    public function __construct(\ParcelServiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns a limited collection of parcel services.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection
     * @throws \ParcelServiceCollectionNotFoundException
     */
    public function getAll(\Pager $pager = null, array $sorters = [])
    {
        $parcelServices = $this->repository->findAll($pager, $sorters);
        
        if (!$parcelServices) {
            throw new ParcelServiceCollectionNotFoundException('Parcel service resources for page "' . $pager->page()
                                                               . '" with ' . $pager->perPage() . ' entries not found!');
        }
        
        return $parcelServices;
    }
    
    
    /**
     * Returns a collection of parcel services found be the given limited search condition.
     *
     * @param \ParcelServiceSearchCondition $searchCondition
     * @param \Pager|null                   $pager   (Optional) Pager object with pagination information
     * @param array                         $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection
     */
    public function getBy(\ParcelServiceSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        return $this->repository->findBy($searchCondition, $pager, $sorters);
    }
    
    
    /**
     * Returns a parcel service identified by its ID.
     *
     * @param \ParcelServiceId $parcelServiceId
     *
     * @return \ParcelServiceInterface
     *
     * @throws \ParcelServiceNotFoundException If parcel service not found.
     */
    public function getById(\ParcelServiceId $parcelServiceId)
    {
        $parcelService = $this->repository->findById($parcelServiceId);
        
        if ($parcelService === null) {
            throw new \ParcelServiceNotFoundException('Parcel service with ID ' . $parcelServiceId->id()
                                                      . ' not found.');
        }
        
        return $parcelService;
    }
}