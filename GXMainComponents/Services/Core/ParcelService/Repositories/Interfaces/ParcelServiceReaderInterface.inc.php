<?php
/* --------------------------------------------------------------
   ParcelServiceReaderInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ParcelServiceReaderInterface
 */
interface ParcelServiceReaderInterface
{
    /**
     * Returns all parcel services limited by given limit and offset.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array
     */
    public function fetchAll(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns the parcel service search result limited by given limit and offset as an array.
     *
     * @param StringType  $searchCondition
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return array
     */
    public function fetchBy(\StringType $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns a parcel service as an array identified by its ID.
     *
     * @param \ParcelServiceId $parcelServiceId
     *
     * @return array
     */
    public function fetchById(\ParcelServiceId $parcelServiceId);
}