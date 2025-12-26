<?php
/* --------------------------------------------------------------
   SpecialOfferDataAdapterInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferDataAdapterInterface
 */
interface SpecialOfferDataAdapterInterface
{
    /**
     * Returns an instance that can fetch special offer data from an storage.
     *
     * @return \SpecialOfferReaderInterface
     */
    public function reader();
    
    
    /**
     * Returns an instance that can write special offer data to an storage.
     *
     * @return \SpecialOfferWriterInterface
     */
    public function writer();
}