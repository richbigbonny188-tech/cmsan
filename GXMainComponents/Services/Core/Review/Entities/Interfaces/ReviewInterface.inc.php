<?php
/* --------------------------------------------------------------
   ReviewInterface.inc.php 2018-01-10
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ReviewInterface
 *
 * @category   System
 * @package    Review
 * @subpackage Entities
 */
interface ReviewInterface
{
    /**
     * Saves review in database.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function store();
    
    
    /**
     * Deletes review from database.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function delete();
    
    
    /**
     * Returns the review id.
     *
     * @return int Review id.
     */
    public function getId();
    
    
    /**
     * Sets the review id
     *
     * @param \IdType $id The review id to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setId(IdType $id);
    
    
    /**
     * Returns the product id.
     *
     * @return int The product id.
     */
    public function getProductId();
    
    
    /**
     * Sets the product id
     *
     * @param \IdType $productId The product id to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setProductId(IdType $productId);
    
    
    /**
     * Returns the rating.
     *
     * @return int Rating.
     */
    public function getRating();
    
    
    /**
     * Sets the rating.
     *
     * @param \IntType $rating Rating to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setRating(IntType $rating);
    
    
    /**
     * Returns the added datetime.
     *
     * @return \DateTime
     */
    public function getDateAdded();
    
    
    /**
     * Sets the added datetime.
     *
     * @param \DateTime $dateAdded Date to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setDateAdded(\DateTime $dateAdded);
    
    
    /**
     * Returns the last modified datetime.
     *
     * @return \DateTime The last modified datetime to be set.
     */
    public function getLastModifiedDate();
    
    
    /**
     * Sets the last modified datetime.
     *
     * @param \DateTime $lastModifiedDate The last modified date to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setLastModifiedDate(DateTime $lastModifiedDate);
    
    
    /**
     * Returns the number of read.
     *
     * @return int The number of read.
     */
    public function getRead();
    
    
    /**
     * Sets the number of read.
     *
     * @param \IntType $read Number of read to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setRead(IntType $read);
    
    
    /**
     * Returns the language id.
     *
     * @return int The language id.
     */
    public function getLanguageId();
    
    
    /**
     * Sets the language id.
     *
     * @param \IntType $languageId Language id to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setLanguageId(IntType $languageId);
    
    
    /**
     * Returns the review text.
     *
     * @return string The review text.
     */
    public function getText();
    
    
    /**
     * Sets the review text.
     *
     * @param \StringType $text The review text to be set.
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setText(StringType $text);
    
    
    /**
     * Returns the review customer.
     *
     * @return \ReviewCustomerInterface
     */
    public function getCustomer();
    
    
    /**
     * Sets the review customer.
     *
     * @param \ReviewCustomerInterface $customer
     *
     * @return $this|\ReviewInterface Same instance for chained method calls.
     */
    public function setCustomer(ReviewCustomerInterface $customer);
}