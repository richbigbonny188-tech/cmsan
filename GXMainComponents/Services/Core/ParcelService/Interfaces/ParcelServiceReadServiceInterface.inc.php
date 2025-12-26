<?php
/* --------------------------------------------------------------
   ParcelServiceReadServiceInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ParcelServiceReadService
 */
interface ParcelServiceReadServiceInterface
{
    /**
     * Returns a limited collection of parcel services.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection
     */
    public function getAll(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns a collection of parcel services found be the given limited search condition.
     *
     * @param \ParcelServiceSearchCondition $searchCondition
     * @param \Pager|null                   $pager   (Optional) Pager object with pagination information
     * @param array                         $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \ParcelServiceCollection
     */
    public function getBy(\ParcelServiceSearchCondition $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns a parcel service identified by its ID.
     *
     * @param \ParcelServiceId $parcelServiceId
     *
     * @return \ParcelServiceInterface
     */
    public function getById(\ParcelServiceId $parcelServiceId);
}