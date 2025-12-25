<?php

/* --------------------------------------------------------------
   QuantityUnitReadService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuantityUnitReadService
 *
 * @category   System
 * @package    QuantityUnit
 */
class QuantityUnitReadService implements QuantityUnitReadServiceInterface
{
    /**
     * @var \QuantityUnitRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * QuantityUnitReadService constructor.
     *
     * @param \QuantityUnitRepositoryInterface $repository
     */
    public function __construct(QuantityUnitRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns quantity unit entity by given id.
     *
     * @param \IdType $quantityUnitId Quantity unit entity to be given.
     *
     * @return \QuantityUnitInterface
     */
    public function getById(IdType $quantityUnitId)
    {
        return $this->repository->getById($quantityUnitId);
    }
    
    
    /**
     * Returns quantity unit entities as collection.
     *
     * @return \QuantityUnitCollection
     */
    public function getAll()
    {
        return $this->repository->getAll();
    }
}