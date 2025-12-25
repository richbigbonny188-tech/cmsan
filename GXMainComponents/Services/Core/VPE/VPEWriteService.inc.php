<?php
/* --------------------------------------------------------------
   VPEWriteService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VPEWriteService
 *
 * @category   System
 * @package    VPE
 */
class VPEWriteService implements VPEWriteServiceInterface
{
    /**
     * @var VPERepositoryInterface
     */
    protected $repository;
    
    
    /**
     * VPEWriteService constructor.
     *
     * @param VPERepositoryInterface $repository
     */
    public function __construct(VPERepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Saves VPE entity in database.
     *
     * @param VPEInterface $vpe VPE entity to be saved.
     *
     * @return VPEWriteServiceInterface Same instance for chained method calls.
     */
    public function save(VPEInterface $vpe)
    {
        $this->repository->save($vpe);
        
        return $this;
    }
    
    
    /**
     * Deletes VPE entity from database.
     *
     * @param VPEInterface $vpe VPE entity to be deleted.
     *
     * @return VPEWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(VPEInterface $vpe)
    {
        $this->repository->delete($vpe);
        
        return $this;
    }
    
    
    /**
     * Creates VPE entity.
     *
     * @return VPE New VPE entity.
     */
    public function createVPE()
    {
        return $this->repository->createVPE();
    }
}
