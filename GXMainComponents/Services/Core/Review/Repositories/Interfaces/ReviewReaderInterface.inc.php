<?php
/* --------------------------------------------------------------
   ReviewReaderInterface.inc.php 2017-11-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ReviewReaderInterface
 *
 * @category   System
 * @package    Review
 * @subpackage Repositories
 */
interface ReviewReaderInterface
{
    /**
     * Returns all review entities as array.
     *
     * @return array
     */
    public function getAll();
    
    
    /**
     * Returns review entity data by the given id.
     *
     * @param \IdType $id
     *
     * @return array
     */
    public function getById(IdType $id);
    
    
    /**
     * Returns the average rating by given product id.
     *
     * @param \IdType $productId
     *
     * @return double
     */
    public function getAverageRatingByProductId(IdType $productId);
    
    
    /**
     * Returns the reviews by given product id.
     *
     * @param \IdType $productId
     *
     * @param \IdType $languageId
     *
     * @return array
     */
    public function getReviewsByProductId(IdType $productId, IdType $languageId);
    
    
    /**
     * Returns the reviews by given customer ID.
     *
     * @param \IdType $customerId
     *
     * @return array
     */
    public function getReviewsByCustomerId(IdType $customerId);
}