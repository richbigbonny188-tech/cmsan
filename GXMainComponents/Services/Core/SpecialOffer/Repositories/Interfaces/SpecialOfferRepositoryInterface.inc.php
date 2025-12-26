<?php
/* --------------------------------------------------------------
   SpecialOfferRepositoryInterface.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SpecialOfferRepositoryInterface
 */
interface SpecialOfferRepositoryInterface
{
    /**
     * Returns all special offers.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \SpecialOfferCollection List of special offers.
     */
    public function getAll(\Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns all special offers that matches the given search condition.
     *
     * @param \SpecialOfferSearchCondition $searchCondition Condition that must match for found items.
     * @param \Pager|null                  $pager           (Optional) Pager object with pagination information
     * @param array                        $sorters         (Optional) array of Sorter objects with data sorting
     *                                                      information
     *
     * @return \SpecialOfferCollection List of special offers by given conditions.
     */
    public function getBy(SpecialOfferSearchCondition $searchCondition, \Pager $pager = null, array $sorters = []);
    
    
    /**
     * Returns a special offer by the given id.
     *
     * @param \SpecialOfferIdInterface $specialOfferId Id of expected special offer.
     *
     * @return \SpecialOfferInterface Special offer entity by given id.
     * @throws \SpecialOfferNotFoundException
     */
    public function getById(SpecialOfferIdInterface $specialOfferId);
    
    
    /**
     * Saves or updates the given special offer in the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be saved.
     *
     * @return SpecialOfferInterface
     */
    public function save(SpecialOfferInterface $specialOffer);
    
    
    /**
     * Deletes the given special offer from the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be removed.
     *
     * @return void
     */
    public function delete(SpecialOfferInterface $specialOffer);
}

