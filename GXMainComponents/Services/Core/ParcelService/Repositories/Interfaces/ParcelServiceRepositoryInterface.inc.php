<?php
/* --------------------------------------------------------------
   ParcelServiceRepositoryInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ParcelServiceRepositoryInterface
 */
interface ParcelServiceRepositoryInterface
{
    /**
     * Returns a limited collection of parcel services.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection
     */
    public function findAll(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns a limited collection of parcel services found by the given search condition.
     *
     * @param \ParcelServiceSearchCondition $searchCondition
     * @param \Pager|null                   $pager   (Optional) Pager object with pagination information
     * @param array                         $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection
     */
    public function findBy(\ParcelServiceSearchCondition $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns a parcel services found by its ID.
     *
     * @param \ParcelServiceId $parcelServiceId
     *
     * @return \ParcelServiceInterface|null
     */
    public function findById(\ParcelServiceId $parcelServiceId);
    
    
    /**
     * Saves a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function save(\ParcelServiceInterface $parcelService);
    
    
    /**
     * Updates a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function update(\ParcelServiceInterface $parcelService);
    
    
    /**
     * Deletes a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     */
    public function delete(\ParcelServiceInterface $parcelService);
}