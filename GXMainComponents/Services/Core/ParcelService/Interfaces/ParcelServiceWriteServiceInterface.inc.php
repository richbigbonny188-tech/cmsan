<?php
/* --------------------------------------------------------------
   ParcelServiceWriteServiceInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

interface ParcelServiceWriteServiceInterface
{
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