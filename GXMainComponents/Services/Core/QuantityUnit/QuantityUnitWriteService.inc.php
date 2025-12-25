<?php

/* --------------------------------------------------------------
   QuantityUnitWriteService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class QuantityUnitWriteService
 *
 * @category   System
 * @package    QuantityUnit
 */
class QuantityUnitWriteService implements QuantityUnitWriteServiceInterface
{
    /**
     * @var \QuantityUnitRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * QuantityUnitWriteService constructor.
     *
     * @param \QuantityUnitRepositoryInterface $repository
     */
    public function __construct(QuantityUnitRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Saves quantity unit entity in database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be saved.
     *
     * @return $this|\QuantityUnitWriteServiceInterface Same instance for chained method calls.
     */
    public function save(QuantityUnitInterface $quantityUnit)
    {
        $this->repository->save($quantityUnit);
        
        return $this;
    }
    
    
    /**
     * Deletes quantity unit entity from database.
     *
     * @param \QuantityUnitInterface $quantityUnit Quantity unit to be deleted.
     *
     * @return $this|\QuantityUnitWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(QuantityUnitInterface $quantityUnit)
    {
        $this->repository->delete($quantityUnit);
        
        return $this;
    }
    
    
    /**
     * Creates Quantity unit entity.
     *
     * @return \GXEngineQuantityUnit New quantity unit entity.
     */
    public function createQuantityUnit()
    {
        return $this->repository->createQuantityUnit();
    }
}