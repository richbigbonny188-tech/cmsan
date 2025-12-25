<?php
/* --------------------------------------------------------------
   SpecialOfferWriterInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferWriterInterface
 */
interface SpecialOfferWriterInterface
{
    /**
     * Inserts the given special offer data to a storage.
     *
     * @param array $specialOfferData Special offer data to be inserted.
     *
     * @return int Insert id.
     */
    public function insert(array $specialOfferData);
    
    
    /**
     * Updates the given special offer data to a storage.
     *
     * @param array $specialOfferData Special offer data to be updated.
     * @param int   $specialOfferId   Id of special offer to be updated.
     *
     * @return void
     */
    public function update(array $specialOfferData, $specialOfferId);
    
    
    /**
     * Deletes a special offer from the storage by the given id.
     *
     * @param int $specialOfferId Id of special offer to be removed.
     *
     * @return void
     */
    public function delete($specialOfferId);
}