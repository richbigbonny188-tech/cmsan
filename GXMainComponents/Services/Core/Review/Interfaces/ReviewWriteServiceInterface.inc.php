<?php
/* --------------------------------------------------------------
   ReviewWriteServiceInterface.inc.php 2018-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ReviewWriteServiceInterface
 *
 * @category   System
 * @package    Review
 * @subpackage Interfaces
 */
interface ReviewWriteServiceInterface
{
    /**
     * Returns new instances of withdrawal entities.
     *
     * @return \Review
     */
    public function createReview();
    
    
    /**
     * Returns a new instance of review customer.
     *
     * @param \IntType    $customerId
     * @param \StringType $customerName
     *
     * @return \ReviewCustomer
     */
    public function createCustomer(IntType $customerId, StringType $customerName);
    
    
    /**
     * Saves review entity in database.
     *
     * @param \ReviewInterface $review Review entity to be saved.
     *
     * @return $this|\ReviewWriteServiceInterface Same instance for chained method calls.
     */
    public function store(ReviewInterface $review);
    
    
    /**
     * Deletes review entity from database.
     *
     * @param \ReviewInterface $review Review Entity to be deleted.
     *
     * @return $this|\ReviewWriteServiceInterface Same instance for chained method calls.
     */
    public function delete(ReviewInterface $review);
}
