<?php
/* --------------------------------------------------------------
   SpecialOfferWriteService.inc.php 2018-07-02
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOfferWriteService
 */
class SpecialOfferWriteService implements SpecialOfferWriteServiceInterface
{
    /**
     * @var \SpecialOfferRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * SpecialOfferWriteService constructor.
     *
     * @param \SpecialOfferRepositoryInterface $repository
     */
    public function __construct(SpecialOfferRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Saves the given special offer in the storage.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be saved.
     *
     * @return SpecialOfferInterface
     */
    public function save(SpecialOfferInterface $specialOffer)
    {
        return $this->repository->save($specialOffer);
    }
    
    
    /**
     * Updates the given special offer.
     *
     * @param \SpecialOfferInterface $specialOffer Special offer to be updated.
     *
     * @return SpecialOfferInterface
     */
    public function update(SpecialOfferInterface $specialOffer)
    {
        return $this->repository->save($specialOffer);
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
        $this->repository->delete($specialOffer);
    }
}