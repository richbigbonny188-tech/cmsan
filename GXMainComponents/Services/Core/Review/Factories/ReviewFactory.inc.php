<?php
/* --------------------------------------------------------------
   ReviewFactory.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewFactory
 *
 * @category   System
 * @package    Review
 * @subpackage Factories
 */
class ReviewFactory
{
    /**
     * @var \ReviewRepository
     */
    protected $repository;
    
    
    /**
     * ReviewFactory constructor.
     *
     * @param \ReviewRepositoryInterface $repository
     */
    public function __construct(ReviewRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns new instances of withdrawal entities.
     *
     * @return \Review
     */
    public function createEntity()
    {
        return new Review($this->repository);
    }
    
    
    /**
     * Returns a new instance of reviewCustomer.
     *
     * @param \IntType    $customerId
     * @param \StringType $customerName
     *
     * @return \ReviewCustomer
     */
    public function createCustomer(IntType $customerId, StringType $customerName)
    {
        return new ReviewCustomer($customerId, $customerName);
    }
    
    
    /**
     * Returns new instances of review collections.
     *
     * @return \ReviewCollection
     */
    public function createCollection()
    {
        return new ReviewCollection();
    }
}
