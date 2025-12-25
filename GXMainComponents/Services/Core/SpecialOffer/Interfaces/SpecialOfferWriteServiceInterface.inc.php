<?php
/* --------------------------------------------------------------
   SpecialOfferWriteServiceInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferWriteServiceInterface
 */
interface SpecialOfferWriteServiceInterface
{
    /**
     * Saves the given special offer in the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be saved.
     *
     * @return SpecialOfferInterface
     */
    public function save(SpecialOfferInterface $specialOffer);
    
    
    /**
     * Updates the given special offer.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be updated.
     *
     * @return SpecialOfferInterface
     */
    public function update(SpecialOfferInterface $specialOffer);
    
    
    /**
     * Deletes the given special offer from the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be removed.
     *
     * @return void
     */
    public function delete(SpecialOfferInterface $specialOffer);
}
