<?php

/* --------------------------------------------------------------
   ReviewJsonSerializer.inc.php 2018-01-12
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class ReviewJsonSerializer
 *
 * This class will serialize and deserialize a Review entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communications.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class ReviewJsonSerializer extends AbstractJsonSerializer
{
    /**
     * Reviews write service.
     *
     * @var ReviewWriteServiceInterface
     */
    protected $reviewWriteService;
    
    
    /**
     * ReviewJsonSerializer constructor.
     *
     * @param \ReviewWriteServiceInterface $reviewWriteService
     */
    public function __construct(\ReviewWriteServiceInterface $reviewWriteService)
    {
        parent::__construct();
        
        $this->reviewWriteService = $reviewWriteService;
    }
    
    
    /**
     * Serialize a Review object to a JSON string.
     *
     * @param \ReviewInterface $object         Object instance to be serialized.
     * @param bool             $encode         (optional) Whether to json_encode the result of the method (default
     *                                         true).
     *
     * @return string|array Returns the json encoded review (string) or an array that can be easily encoded
     *                      into a JSON string.
     * @throws InvalidArgumentException If the provided object type is invalid.
     */
    public function serialize($object, $encode = true)
    {
        if (!is_a($object, 'ReviewInterface')) {
            throw new InvalidArgumentException('Invalid argument provided, ReviewInterface object required: '
                                               . get_class($object));
        }
        
        $review = [
            'id'           => $object->getId(),
            'productId'    => $object->getProductId(),
            'rating'       => $object->getRating(),
            'readCount'    => $object->getRead(),
            'dateAdded'    => $object->getDateAdded()->format('Y-m-d H:i:s'),
            'lastModified' => $object->getLastModifiedDate()->format('Y-m-d H:i:s'),
            'languageId'   => $object->getLanguageId(),
            'text'         => $object->getText(),
            'customer'     => $this->_serializeReviewCustomer($object->getCustomer())
        ];
        
        return ($encode) ? $this->jsonEncode($review) : $review;
    }
    
    
    /**
     * Deserialize a Product JSON String.
     *
     * @param string           $string     JSON string that contains the data of the slider.
     * @param \ReviewInterface $baseObject (optional) If provided, this will be the base object to be updated
     *                                     and no new instance will be created.
     *
     * @return \ReviewInterface
     *
     * @throws InvalidArgumentException If the argument is not a string or is empty.
     */
    public function deserialize($string, $baseObject = null)
    {
        if (!is_string($string) || empty($string)) {
            throw new InvalidArgumentException('Invalid argument provided for deserialization: ' . gettype($string));
        }
        
        $json = json_decode($string); // error for malformed json strings
        
        if ($json === null && json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: ' . $string);
        }
        
        if (!$baseObject) {
            $review = $this->reviewWriteService->createReview();
        } else {
            $review = $baseObject;
        }
        
        // Deserialize Json String
        
        if ($json->productId !== null) {
            $review->setProductId(new IdType($json->productId));
        }
        
        if ($json->rating !== null) {
            $review->setRating(new IntType($json->rating));
        }
        
        if ($json->readCount !== null) {
            $review->setRead(new IntType($json->readCount));
        }
        
        if ($json->languageId !== null) {
            $review->setLanguageId(new IntType($json->languageId));
        }
        
        if ($json->text !== null) {
            $review->setText(new StringType($json->text));
        }
        
        if ($json->customer === null) {
            throw new RuntimeException('Required property \'customer\' is missing in request body.', 400);
        }
        $review->setCustomer($this->_deserializeReviewCustomer($json->customer));
        
        return $review;
    }
    
    
    /**
     * Serializes review collections.
     *
     * @param ReviewCollection $reviewCollection Review collection to be serialized.
     * @param bool             $encode           (optional) Whether to json_encode the result of the method (default
     *                                           true).
     *
     * @return string|array Returns the json encoded review collection(string) or an array that can be easily encoded
     *                      into a JSON string.
     */
    public function serializeReviewCollection(ReviewCollection $reviewCollection, $encode = true)
    {
        $data = [];
        foreach ($reviewCollection->getArray() as $review) {
            $data[] = $this->serialize($review, false);
        }
        
        return ($encode) ? $this->jsonEncode($data) : $data;
    }
    
    
    /**
     * Serializes review customer entities.
     *
     * @param \ReviewCustomerInterface $reviewCustomer review customer entity to be serialized.
     *
     * @return array Serialized review customer array.
     */
    protected function _serializeReviewCustomer(ReviewCustomerInterface $reviewCustomer)
    {
        return [
            'customerId'   => $reviewCustomer->getCustomerId(),
            'customerName' => $reviewCustomer->getCustomerName(),
        ];
    }
    
    
    /**
     * Deserialize review customer entities.
     *
     * @param stdClass $reviewCustomerData review customer entity.
     *
     * @return ReviewCustomerInterface Deserialize review customer entity.
     */
    protected function _deserializeReviewCustomer(stdClass $reviewCustomerData)
    {
        return $this->reviewWriteService->createCustomer(new IdType($reviewCustomerData->customerId),
                                                         new StringType($reviewCustomerData->customerName));
    }
}
