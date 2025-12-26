<?php
/* --------------------------------------------------------------
   SpecialOfferMapperInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferMapperInterface
 */
interface SpecialOfferMapperInterface
{
    /**
     * Returns a special offer by the given id.
     *
     * @param \SpecialOfferIdInterface $specialOfferId Id of expected special offer.
     *
     * @return \SpecialOfferInterface|null Special offer entity by given id.
     */
    public function findById(SpecialOfferIdInterface $specialOfferId);
    
    
    /**
     * Returns all special offers that matches the given search condition.
     *
     * @param \SpecialOfferSearchCondition $searchCondition Condition that must match for found items.
     * @param \Pager|null                  $pager           (Optional) Pager object with pagination information
     * @param array                        $sorters         (Optional) array of Sorter objects with data sorting
     *                                                      information
     *
     * @return \SpecialOfferCollection|null List of special offers by given conditions.
     */
    public function findBy(SpecialOfferSearchCondition $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns all special offers.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \SpecialOfferCollection|null List of special offers.
     */
    public function findAll(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * Saves the given special offer in the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be saved.
     *
     * @return SpecialOfferInterface
     */
    public function save(SpecialOfferInterface $specialOffer);
    
    
    /**
     * Updates the given special offer in the storage.
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



