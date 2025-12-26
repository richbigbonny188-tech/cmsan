<?php
/* --------------------------------------------------------------
   ReviewReadService.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewReadService
 *
 * @category   System
 * @package    Review
 * @subpackage Services
 */
class ReviewReadService implements ReviewReadServiceInterface
{
    /**
     * @var \ReviewAccessRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * ReviewReadService constructor.
     *
     * @param \ReviewAccessRepositoryInterface $repository
     */
    public function __construct(ReviewAccessRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
    
    
    /**
     * Returns all review entities as array.
     *
     * @return ReviewCollection
     */
    public function getAll()
    {
        return $this->repository->getAll();
    }
    
    
    /**
     * Returns review entity data by the given id.
     *
     * @param \IdType $id
     *
     * @return Review
     */
    public function getById(IdType $id)
    {
        return $this->repository->getById($id);
    }
    
    
    /**
     * Returns the average rating by given product id.
     *
     * @param \IdType $productId
     *
     * @return double
     */
    public function getAverageRatingByProductId(IdType $productId)
    {
        return $this->repository->getAverageRatingByProductId($productId);
    }
    
    
    /**
     * Returns the reviews by given product id.
     *
     * @param \IdType $productId
     *
     * @param \IdType $languageId
     *
     * @return \ReviewCollection
     */
    public function getReviewsByProductId(IdType $productId, IdType $languageId)
    {
        return $this->repository->getReviewsByProductId($productId, $languageId);
    }
    
    
    /**
     * Returns the reviews by given customer ID.
     *
     * @param \IdType $customerId
     *
     * @return ReviewCollection
     */
    public function getReviewsByCustomerId(IdType $customerId)
    {
        return $this->repository->getReviewsByCustomerId($customerId);
    }
}