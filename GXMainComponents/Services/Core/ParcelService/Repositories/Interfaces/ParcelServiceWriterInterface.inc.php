<?php
/* --------------------------------------------------------------
   ParcelServiceWriterInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ParcelServiceWriterInterface
 */
interface ParcelServiceWriterInterface
{
    /**
     * Inserts the given parcel service data.
     *
     * @param array $parcelService
     *
     * @return int parcel service ID
     */
    public function insert(array $parcelService);
    
    
    /**
     * Updates the given parcel service data.
     *
     * @param array   $parcelService   Parcel service data.
     * @param \IdType $parcelServiceId Id of updated parcel service.
     *
     * @return void
     */
    public function update(array $parcelService, IdType $parcelServiceId);
    
    
    /**
     * Deletes the parcel service identified by given ID.
     *
     * @param \IdType $parcelServiceId
     */
    public function delete(\IdType $parcelServiceId);
}