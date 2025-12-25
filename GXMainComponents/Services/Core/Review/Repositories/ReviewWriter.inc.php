<?php

/* --------------------------------------------------------------
   ReviewWriter.inc.php 2018-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewWriter
 *
 * @category   System
 * @package    Review
 * @subpackage Repositories
 */
class ReviewWriter implements ReviewWriterInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * ReviewWriter constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Saves review entity data in database.
     *
     * @param \ReviewInterface $review Review entity to be saved.
     *
     * @return $this|\ReviewWriterInterface Same instance for chained method calls.
     */
    public function store(ReviewInterface $review)
    {
        $reviewDataArray = [
            'products_id'    => $review->getProductId(),
            'customers_id'   => $review->getCustomer()->getCustomerId(),
            'customers_name' => $review->getCustomer()->getCustomerName(),
            'reviews_rating' => $review->getRating(),
            'reviews_read'   => $review->getRead(),
            'date_added'     => $review->getDateAdded()->format('Y-m-d H:i:s'),
            'last_modified'  => $review->getLastModifiedDate()->format('Y-m-d H:i:s')
        ];
        
        $this->queryBuilder->insert('reviews', $reviewDataArray);
        
        $reviewId = $this->queryBuilder->insert_id();
        
        $review->setId(new IdType($reviewId));
        
        $reviewDescriptionDataArray = [
            'reviews_id'   => $review->getId(),
            'languages_id' => $review->getLanguageId(),
            'reviews_text' => $review->getText()
        ];
        
        $this->queryBuilder->insert('reviews_description', $reviewDescriptionDataArray);
        
        return $this;
    }
    
    
    /**
     * Updates review entity data in database.
     *
     * @param \ReviewInterface $review Review to be updated.
     *
     * @return $this|\ReviewWriterInterface Same instance for chained method calls.
     */
    public function update(ReviewInterface $review)
    {
        $this->queryBuilder->update('reviews',
                                    [
                                        'products_id'    => $review->getProductId(),
                                        'customers_id'   => $review->getCustomer()->getCustomerId(),
                                        'customers_name' => $review->getCustomer()->getCustomerName(),
                                        'reviews_rating' => $review->getRating(),
                                        'reviews_read'   => $review->getRead()
                                    ],
                                    ['reviews_id' => $review->getId()]);
        
        $this->queryBuilder->update('reviews_description',
                                    [
                                        'languages_id' => $review->getLanguageId(),
                                        'reviews_text' => $review->getText()
                                    ],
                                    ['reviews_id' => $review->getId()]);
        
        return $this;
    }
}
