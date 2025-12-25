<?php
/* --------------------------------------------------------------
   ParcelServiceMapperInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ParcelServiceMapperInterface
 */
interface ParcelServiceMapperInterface
{
    /**
     * Fetches all parcel services limited by the pager.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection|null
     */
    public function findAll(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * Fetches all parcel services found by given limited search condition.
     *
     * @param \ParcelServiceSearchCondition $searchCondition
     * @param \Pager|null                   $pager   (Optional) Pager object with pagination information
     * @param array                         $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection|null
     */
    public function findBy(\ParcelServiceSearchCondition $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Fetches a parcel service found by given ID.
     *
     * @param \ParcelServiceId $parcelServiceId
     *
     * @return \ParcelServiceInterface|null
     */
    public function findById(\ParcelServiceId $parcelServiceId);
    
    
    /**
     * Inserts a given parcel service.
     *
     * @param \ParcelServiceInterface $parcelService
     *
     * @return \ParcelServiceInterface
     */
    public function insert(\ParcelServiceInterface $parcelService);
    
    
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