<?php

/* --------------------------------------------------------------
   Review.inc.php 2018-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Review
 *
 * @category   System
 * @package    Review
 * @subpackage Entities
 */
class Review implements ReviewInterface
{
    /**
     * @var int
     */
    protected $id = 0;
    
    /**
     * @var int
     */
    protected $productId;
    
    /**
     * @var int
     */
    protected $rating = 0;
    
    /**
     * @var \DateTime
     */
    protected $dateAdded;
    
    /**
     * @var \DateTime
     */
    protected $lastModifiedDate;
    
    /**
     * @var int
     */
    protected $read = 0;
    
    /**
     * @var int
     */
    protected $languageId = 1;
    
    /**
     * @var string
     */
    protected $text = '';
    
    /**
     * @var \ReviewCustomerInterface
     */
    protected $customer;
    
    /**
     * @var \ReviewRepositoryInterface
     */
    protected $repository;
    
    
    /**
     * Review constructor.
     *
     * @param \ReviewRepositoryInterface $repository
     */
    public function __construct(ReviewRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->dateAdded  = $this->lastModifiedDate = new DateTime();
        $this->customer   = MainFactory::create('ReviewCustomer', new IntType(1), new StringType('Customer Name'));
    }
    
    
    /**
     * Saves review in database.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function store()
    {
        $this->repository->store($this);
        
        return $this;
    }
    
    
    /**
     * Deletes review from database.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function delete()
    {
        $this->repository->delete($this);
        
        return $this;
    }
    
    
    /**
     * Returns the review id.
     *
     * @return int Review id.
     */
    public function getId()
    {
        return $this->id;
    }
    
    
    /**
     * Sets the review id
     *
     * @param \IdType $id The review id to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setId(IdType $id)
    {
        $this->id = $id->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the product id.
     *
     * @return int The product id.
     */
    public function getProductId()
    {
        return $this->productId;
    }
    
    
    /**
     * Sets the product id
     *
     * @param \IdType $productId The product id to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setProductId(IdType $productId)
    {
        $this->productId = $productId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the rating.
     *
     * @return int Rating.
     */
    public function getRating()
    {
        return $this->rating;
    }
    
    
    /**
     * Sets the rating.
     *
     * @param \IntType $rating Rating to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setRating(IntType $rating)
    {
        $this->rating = $rating->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the added datetime.
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
    
    
    /**
     * Sets the added datetime.
     *
     * @param \DateTime $dateAdded Date to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setDateAdded(\DateTime $dateAdded)
    {
        $this->dateAdded = $dateAdded;
        
        return $this;
    }
    
    
    /**
     * Returns the last modified datetime.
     *
     * @return \DateTime The last modified datetime to be set.
     */
    public function getLastModifiedDate()
    {
        return $this->lastModifiedDate;
    }
    
    
    /**
     * Sets the last modified datetime.
     *
     * @param \DateTime $lastModifiedDate The last modified date to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setLastModifiedDate(DateTime $lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;
        
        return $this;
    }
    
    
    /**
     * Returns the number of read.
     *
     * @return int The number of read.
     */
    public function getRead()
    {
        return $this->read;
    }
    
    
    /**
     * Sets the number of read.
     *
     * @param \IntType $read Number of read to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setRead(IntType $read)
    {
        $this->read = $read->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the language id.
     *
     * @return int The language id.
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
    
    
    /**
     * Sets the language id.
     *
     * @param \IntType $languageId Language id to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setLanguageId(IntType $languageId)
    {
        $this->languageId = $languageId->asInt();
        
        return $this;
    }
    
    
    /**
     * Returns the review text.
     *
     * @return string The review text.
     */
    public function getText()
    {
        return $this->text;
    }
    
    
    /**
     * Sets the review text.
     *
     * @param \StringType $text The review text to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setText(StringType $text)
    {
        $this->text = $text->asString();
        
        return $this;
    }
    
    
    /**
     * Returns the review customer.
     *
     * @return \ReviewCustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }
    
    
    /**
     * Sets the review customer.
     *
     * @param \ReviewCustomerInterface $customer
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setCustomer(ReviewCustomerInterface $customer)
    {
        $this->customer = $customer;
        
        return $this;
    }
}
