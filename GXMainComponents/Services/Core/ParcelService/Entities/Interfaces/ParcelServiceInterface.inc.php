<?php
/* --------------------------------------------------------------
   ParcelServiceInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ParcelServiceInterface
 */
interface ParcelServiceInterface
{
    /**
     * Returns the ID of the parcel service if set. Otherwise null will be returned.
     *
     * @return int|null
     */
    public function id();
    
    
    /**
     * Returns the name of the parcel service.
     *
     * @return string
     */
    public function name();
    
    
    /**
     * Checks if the parcel service is set as default.
     *
     * @return bool
     */
    public function isDefault();
    
    
    /**
     * Returns the parcel service descriptions as a ParcelServiceDescriptionCollection.
     *
     * @return \ParcelServiceDescriptionCollection
     */
    public function parcelServiceDescriptions();
}