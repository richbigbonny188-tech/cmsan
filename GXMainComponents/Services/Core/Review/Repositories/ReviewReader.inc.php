<?php
/* --------------------------------------------------------------
   ReviewReader.inc.php 2018-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewReader
 *
 * @category   System
 * @package    Review
 * @subpackage Repositories
 */
class ReviewReader implements ReviewReaderInterface
{
    /**
     * @var \CI_DB_query_builder
     */
    protected $queryBuilder;
    
    
    /**
     * ReviewReader constructor.
     *
     * @param \CI_DB_query_builder $queryBuilder
     */
    public function __construct(CI_DB_query_builder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }
    
    
    /**
     * Returns all review entities as array.
     *
     * @return array
     */
    public function getAll()
    {
        $reviews = [];
        $rawData = $this->queryBuilder->select()
            ->from('reviews')
            ->join('reviews_description',
                   'reviews.reviews_id = reviews_description.reviews_id')
            ->get()
            ->result_array();
        foreach ($rawData as $row) {
            $reviews[] = $this->_buildReviewDataArray($row);
        }
        
        return $reviews;
    }
    
    
    /**
     * Returns review entity data by the given id.
     *
     * @param \IdType $id
     *
     * @return array
     * @throws \EntityNotFoundException
     *
     */
    public function getById(IdType $id)
    {
        $rawData = $this->queryBuilder->select()
            ->from('reviews')
            ->join('reviews_description',
                   'reviews.reviews_id = reviews_description.reviews_id')
            ->where('reviews.reviews_id', $id->asInt())
            ->get()
            ->row_array();
        
        if ($rawData === null) {
            throw new EntityNotFoundException('Review entity was not found with provided id "' . $id->asInt() . '"');
        }
        
        return $this->_buildReviewDataArray($rawData);
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
        $averageValue = $this->queryBuilder->select_avg('reviews_rating')
            ->from('reviews')
            ->where('products_id',
                    $productId->asInt())
            ->get()
            ->row_array();
        
        return (double)$averageValue['reviews_rating'];
    }
    
    
    /**
     * Returns the reviews by given product id.
     *
     * @param \IdType $productId
     *
     * @param \IdType $languageId
     *
     * @return array
     */
    public function getReviewsByProductId(IdType $productId, IdType $languageId)
    {
        $reviews = [];
        $rawData = $this->queryBuilder->select()
            ->from('reviews')
            ->join('reviews_description',
                   'reviews.reviews_id = reviews_description.reviews_id')
            ->where('products_id', $productId->asInt())
            ->where('languages_id', $languageId->asInt())
            ->get()
            ->result_array();
        foreach ($rawData as $data) {
            $reviews[] = $this->_buildReviewDataArray($data);
        }
        
        return $reviews;
    }
    
    
    /**
     * Returns the reviews by given customer ID.
     *
     * @param \IdType $customerId
     *
     * @return array
     */
    public function getReviewsByCustomerId(IdType $customerId)
    {
        $reviews = [];
        $rawData = $this->queryBuilder->select()
            ->from('reviews')
            ->join('reviews_description',
                   'reviews.reviews_id = reviews_description.reviews_id')
            ->where('customers_id', $customerId->asInt())
            ->get()
            ->result_array();
        foreach ($rawData as $data) {
            $reviews[] = $this->_buildReviewDataArray($data);
        }
        
        return $reviews;
    }
    
    
    /**
     * @param $rawDataArray
     *
     * @return array
     */
    protected function _buildReviewDataArray($rawDataArray)
    {
        return [
            'id'           => $rawDataArray['reviews_id'],
            'productId'    => $rawDataArray['products_id'],
            'rating'       => $rawDataArray['reviews_rating'],
            'dateAdded'    => $rawDataArray['date_added'],
            'lastModified' => $rawDataArray['last_modified'],
            'read'         => $rawDataArray['reviews_read'],
            'languageId'   => $rawDataArray['languages_id'],
            'text'         => $rawDataArray['reviews_text'],
            'customer'     => [
                'customerId'   => $rawDataArray['customers_id'],
                'customerName' => $rawDataArray['customers_name']
            ]
        ];
    }
}
