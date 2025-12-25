<?php
/* --------------------------------------------------------------
   ReviewDeleter.inc.php 2017-11-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewDeleter
 *
 * @category   System
 * @package    Review
 * @subpackage Repositories
 */
class ReviewDeleter implements ReviewDeleterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * ReviewDeleter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Deletes review entity data in database.
     *
     * @param \ReviewInterface $review Review entity to be delete.
     *
     * @return $this|\ReviewDeleterInterface Same instance for chained method calls.
     */
    public function delete(ReviewInterface $review)
    {
        $this->queryBuilder->delete('reviews', ['reviews_id' => $review->getId()]);
        $this->queryBuilder->delete('reviews_description', ['reviews_id' => $review->getId()]);
        
        return $this;
    }
}