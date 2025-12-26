<?php
/* --------------------------------------------------------------
   ReviewAccessRepository.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ReviewAccessRepository
 *
 * @category   System
 * @package    Review
 * @subpackage Repositories
 */
class ReviewAccessRepository implements ReviewAccessRepositoryInterface
{
    /**
     * @var \ReviewFactory
     */
    protected $factory;
    
    /**
     * @var \ReviewReaderInterface
     */
    protected $reader;
    
    
    /**
     * ReviewAccessRepository constructor.
     *
     * @param \ReviewFactory         $factory
     * @param \ReviewReaderInterface $reader
     */
    public function __construct(ReviewFactory $factory, ReviewReaderInterface $reader)
    {
        $this->factory = $factory;
        $this->reader  = $reader;
    }
    
    
    /**
     * Returns all review entities as array.
     *
     * @return ReviewCollection
     */
    public function getAll()
    {
        $collection = $this->factory->createCollection();
        
        $rawData = $this->reader->getAll();
        foreach ($rawData as $data) {
            $review = $this->factory->createEntity();
            $review->setId(new IdType($data['id']))
                ->setProductId(new IdType($data['productId']))
                ->setRating(new IntType($data['rating']))
                ->setDateAdded(new DateTime($data['dateAdded']))
                ->setLastModifiedDate(new DateTime($data['lastModified']))
                ->setRead(new IntType($data['read']))
                ->setLanguageId(new IntType($data['languageId']))
                ->setText(new StringType($data['text']))
                ->setCustomer($this->_createCustomer($data['customer']));
            
            $collection->addItem($review);
        }
        
        return $collection;
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
        $rawData = $this->reader->getById($id);
        
        $review = $this->factory->createEntity();
        $review->setId($id)
            ->setProductId(new IdType($rawData['productId']))
            ->setRating(new IntType($rawData['rating']))
            ->setDateAdded(new DateTime($rawData['dateAdded']))
            ->setLastModifiedDate(new DateTime($rawData['lastModified']))
            ->setRead(new IntType($rawData['read']))
            ->setLanguageId(new IntType($rawData['languageId']))
            ->setText(new StringType($rawData['text']))
            ->setCustomer($this->_createCustomer($rawData['customer']));
        
        return $review;
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
        return $this->reader->getAverageRatingByProductId($productId);
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
        $collection = $this->factory->createCollection();
        
        $rawData = $this->reader->getReviewsByProductId($productId, $languageId);
        foreach ($rawData as $data) {
            $review = $this->factory->createEntity();
            $review->setId(new IdType($data['id']))
                ->setProductId(new IdType($data['productId']))
                ->setRating(new IntType($data['rating']))
                ->setDateAdded(new DateTime($data['dateAdded']))
                ->setLastModifiedDate(new DateTime($data['lastModified']))
                ->setRead(new IntType($data['read']))
                ->setLanguageId($languageId)
                ->setText(new StringType($data['text']))
                ->setCustomer($this->_createCustomer($data['customer']));
            
            $collection->addItem($review);
        }
        
        return $collection;
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
        $collection = $this->factory->createCollection();
        
        $rawData = $this->reader->getReviewsByCustomerId($customerId);
        foreach ($rawData as $data) {
            $review = $this->factory->createEntity();
            $review->setId(new IdType($data['id']))
                ->setProductId(new IdType($data['productId']))
                ->setRating(new IntType($data['rating']))
                ->setDateAdded(new DateTime($data['dateAdded']))
                ->setLastModifiedDate(new DateTime($data['lastModified']))
                ->setRead(new IntType($data['read']))
                ->setLanguageId(new IntType($data['languageId']))
                ->setText(new StringType($data['text']))
                ->setCustomer($this->_createCustomer($data['customer']));
            
            $collection->addItem($review);
        }
        
        return $collection;
    }
    
    
    /**
     * Creates with given array an reviewCustomer.
     *
     * @param array $dataSet
     *
     * @return \ReviewCustomer
     */
    protected function _createCustomer(array $dataSet)
    {
        return $this->factory->createCustomer(new IntType($dataSet['customerId']),
                                              new StringType($dataSet['customerName']));
    }
}
