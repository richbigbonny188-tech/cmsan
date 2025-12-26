<?php
/* --------------------------------------------------------------
   ParcelServiceDescriptionInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ParcelServiceDescription
 */
interface ParcelServiceDescriptionInterface
{
    /**
     * Returns the parcel service ID.
     *
     * @return int|null
     */
    public function parcelServiceId();
    
    
    /**
     * Returns the language ID as int.
     *
     * @return int
     */
    public function languageId();
    
    
    /**
     * Returns the parcel service url.
     *
     * @return string
     */
    public function url();
    
    
    /**
     * Returns the parcel service comment.
     *
     * @return string
     */
    public function comment();
}