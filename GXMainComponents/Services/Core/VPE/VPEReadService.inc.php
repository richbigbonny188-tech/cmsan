<?php

/* --------------------------------------------------------------
   VPEReadService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class VPEReadService
 *
 * @category   System
 * @package    VPE
 */
class VPEReadService implements VPEReadServiceInterface
{
    /**
     * @var \VPERepositoryInterface
     */
    protected $repository;
    
    
    /**
     * VPEReadService constructor.
     *
     * @param \VPERepositoryInterface $repository
     */
    public function __construct(VPERepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns VPE entity by the given id.
     *
     * @param \IdType $vpeId VPE entity to be given.
     *
     * @return \VPEInterface
     * @throws EntityNotFoundException
     */
    public function getById(IdType $vpeId)
    {
        return $this->repository->getById($vpeId);
    }
    
    
    /**
     * Returns als VPE entities as collection.
     *
     * @return \VPECollection
     */
    public function getAll()
    {
        return $this->repository->getAll();
    }
}