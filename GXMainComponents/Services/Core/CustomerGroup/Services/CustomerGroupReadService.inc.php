<?php
/* --------------------------------------------------------------
   CustomerGroupReadService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CustomerGroupReadService
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Services
 */
class CustomerGroupReadService implements CustomerGroupReadServiceInterface
{
    /**
     * @var \CustomerGroupAccessRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * CustomerGroupReadService constructor.
     *
     * @param \CustomerGroupAccessRepositoryInterface $repository
     */
    public function __construct(CustomerGroupAccessRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns customer group entities as collection.
     *
     * @return \CustomerGroupCollection
     */
    public function getAll()
    {
        return $this->repository->getAll();
    }
    
    
    /**
     * Returns customer group by given id.
     *
     * @param \IntType $id Customer group id.
     *
     * @return \CustomerGroup
     */
    public function getById(IntType $id)
    {
        return $this->repository->getById($id);
    }
    
    
    /**
     * Creates customer group entity.
     *
     * @return \CustomerGroup new customer group entity.
     */
    public function create()
    {
        return $this->repository->create();
    }
}