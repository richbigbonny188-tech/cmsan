<?php
/* --------------------------------------------------------------
   ParcelServiceDataAdapterInterface.inc.php 2018-07-03
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ParcelServiceDataAdapterInterface
 */
interface ParcelServiceDataAdapterInterface
{
    /**
     * Returns the parcel service reader.
     *
     * @return \ParcelServiceReaderInterface
     */
    public function reader();
    
    
    /**
     * Returns the parcel service writer.
     *
     * @return \ParcelServiceWriterInterface
     */
    public function writer();
}