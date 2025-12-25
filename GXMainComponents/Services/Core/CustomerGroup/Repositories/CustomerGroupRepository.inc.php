<?php

/* --------------------------------------------------------------
  CustomerGroupRepository.inc.php 2018-02-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Interface CustomerGroupRepository
 *
 * @category   System
 * @package    CustomerGroup
 * @subpackage Repositories
 */
class CustomerGroupRepository implements CustomerGroupRepositoryInterface
{
    /**
     * @var \CustomerGroupWriterInterface
     */
    protected $writer;
    
    /**
     * @var \CustomerGroupDeleterInterface
     */
    protected $deleter;
    
    protected $nonRemovableIds = [1, 2, 3];
    
    
    public function __construct(CustomerGroupWriterInterface $writer, CustomerGroupDeleterInterface $deleter)
    {
        $this->deleter = $deleter;
        $this->writer  = $writer;
    }
    
    
    /**
     * Saves customer group in database.
     *
     * @param \CustomerGroup $customerGroup Customer group entity to be saved.
     *
     * @return $this|\CustomerGroupRepositoryInterface Same instance for chained method calls.
     */
    public function store(CustomerGroup $customerGroup)
    {
        $this->writer->store($customerGroup);
        
        return $this;
    }
    
    
    /**
     * Updates customer group in database.
     *
     * @param \CustomerGroup $customerGroup Customer group entity to be saved.
     *
     * @return $this|\CustomerGroupRepositoryInterface Same instance for chained method calls.
     */
    public function update(CustomerGroup $customerGroup)
    {
        $this->writer->update($customerGroup);
        
        return $this;
    }
    
    
    /**
     * Deletes customer group from database.
     *
     * @param \CustomerGroup $customerGroup Customer group to be deleted.
     *
     * @return $this|\CustomerGroupRepositoryInterface Same instance for chained method calls.
     */
    public function delete(CustomerGroup $customerGroup)
    {
        if (in_array($customerGroup->getId(), $this->nonRemovableIds, true)) {
            throw new InvalidArgumentException("This customer group can't be deleted!");
        }
        
        $this->deleter->delete($customerGroup);
        
        return $this;
    }
    
    
    /**
     * Create base data from chosen personal offers table.
     *
     * @param \IntType $customerGroupId
     * @param \IntType $baseId
     *
     * @return \CustomerGroupRepository
     */
    public function createBase(IntType $customerGroupId, IntType $baseId)
    {
        $this->writer->createBase($customerGroupId, $baseId);
        
        return $this;
    }
}