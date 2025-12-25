<?php
/* --------------------------------------------------------------
   SpecialOfferReadService.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SpecialOfferReadService implements SpecialOfferReadServiceInterface
{
    /**
     * @var \SpecialOfferRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * SpecialOfferReadService constructor.
     *
     * @param \SpecialOfferRepositoryInterface $repository
     */
    public function __construct(SpecialOfferRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns all special offers.
     *
     * @param \Pager|null $pager   (Optional) Pager object with pagination information
     * @param array       $sorters (Optional) array of Sorter objects with data sorting information
     *
     * @return \SpecialOfferCollection List of special offers.
     */
    public function getAll(\Pager $pager = null, array $sorters = [])
    {
        return $this->repository->getAll($pager, $sorters);
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
     */
    public function getBy(SpecialOfferSearchCondition $searchCondition, \Pager $pager = null, array $sorters = [])
    {
        return $this->repository->getBy($searchCondition, $pager, $sorters);
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
        return $this->repository->getById($specialOfferId);
    }
}
