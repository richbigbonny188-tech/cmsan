<?php
/* --------------------------------------------------------------
   SpecialOfferRepository.inc.php 2018-07-05
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferRepository
 */
class SpecialOfferRepository implements SpecialOfferRepositoryInterface
{
    /**
     * @var \SpecialOfferMapperInterface
     */
    protected $mapper;
    
    
    /**
     * SpecialOfferRepository constructor.
     *
     * @param \SpecialOfferMapperInterface $mapper
     */
    public function __construct(SpecialOfferMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }
    
    
    /**
     * Returns all special offers.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \SpecialOfferCollection List of special offers.
     * @throws \SpecialOfferCollectionNotFoundException
     */
    public function getAll(\Pager $pager = null, array $sorters = [])
    {
        $specialOffers = $this->mapper->findAll($pager, $sorters);
        
        if (!$specialOffers) {
            $msg = 'Special offers not found!';
            if ($pager) {
                $msg .= 'Current page is "' . $pager->page() . '" and items per page are "' . $pager->perPage() . '"';
            }
            throw new SpecialOfferCollectionNotFoundException($msg);
        }
        
        return $specialOffers;
    }
    
    
    /**
     * Returns all special offers that matches the given search condition.
     *
     * @param \SpecialOfferSearchCondition $searchCondition Condition that must match for found items.
     * @param \Pager|null                  $pager           (Optional) Pager object with pagination information
     * @param array                        $sorters         (Optional) array of Sorter objects with data sorting
     *                                                      information
     *
     * @return \SpecialOfferCollection List of special offers by given conditions.
     * @throws \SpecialOfferCollectionNotFoundException
     */
    public function getBy(SpecialOfferSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        $specialOffers = $this->mapper->findBy($searchCondition, $pager, $sorters);
        
        if (!$specialOffers) {
            $msg = 'Special offers not found!';
            if ($pager) {
                $msg .= ' Current page is "' . $pager->page() . '" and items per page are "' . $pager->perPage() . '".';
            }
            throw new SpecialOfferCollectionNotFoundException($msg);
        }
        
        return $specialOffers;
    }
    
    
    /**
     * Returns a special offer by the given id.
     *
     * @param \SpecialOfferIdInterface $specialOfferId Id of expected special offer.
     *
     * @return \SpecialOfferInterface Special offer entity by given id.
     * @throws \SpecialOfferNotFoundException
     */
    public function getById(SpecialOfferIdInterface $specialOfferId)
    {
        $specialOffer = $this->mapper->findById($specialOfferId);
        
        if (!$specialOffer) {
            throw new SpecialOfferNotFoundException('Special offer entity by id "' . $specialOfferId->specialOfferId()
                                                    . '" was not found!');
        }
        
        return $specialOffer;
    }
    
    
    /**
     * Saves or updates the given special offer in the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be saved.
     *
     * @return SpecialOfferInterface
     */
    public function save(SpecialOfferInterface $specialOffer)
    {
        if ($specialOffer->id()) {
            return $this->mapper->update($specialOffer);
        }
        
        return $this->mapper->save($specialOffer);
    }
    
    
    /**
     * Deletes the given special offer from the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be removed.
     *
     * @return void
     */
    public function delete(SpecialOfferInterface $specialOffer)
    {
        $this->mapper->delete($specialOffer);
    }
}