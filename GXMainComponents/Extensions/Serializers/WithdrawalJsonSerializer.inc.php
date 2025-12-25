<?php

/* --------------------------------------------------------------
   WithdrawalJsonSerializer.inc.php 2020-01-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class WithdrawalJsonSerializer
 *
 * This class will serialize and deserialize a Withdrawal entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communications.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class WithdrawalJsonSerializer extends AbstractJsonSerializer
{
    /**
     * Withdrawals write service.
     *
     * @var WithdrawalWriteServiceInterface
     */
    protected $withdrawalWriteService;
    
    
    /**
     * WithdrawalJsonSerializer constructor.
     *
     * @param \WithdrawalWriteServiceInterface $withdrawalWriteService
     */
    public function __construct(\WithdrawalWriteServiceInterface $withdrawalWriteService)
    {
        parent::__construct();
        
        $this->withdrawalWriteService = $withdrawalWriteService;
    }
    
    
    /**
     * Serialize a Withdrawal object to a JSON string.
     *
     * @param \WithdrawalInterface $object     Object instance to be serialized.
     * @param bool                 $encode     (optional) Whether to json_encode the result of the method (default
     *                                         true).
     *
     * @return string|array Returns the json encoded withdrawal (string) or an array that can be easily encoded
     *                      into a JSON string.
     * @throws InvalidArgumentException If the provided object type is invalid.
     */
    public function serialize($object, $encode = true)
    {
        if (!is_a($object, 'WithdrawalInterface')) {
            throw new InvalidArgumentException('Invalid argument provided, WithdrawalInterface object required: '
                                               . get_class($object));
        }
        
        $withdrawal = [
            'id'             => $object->getWithdrawalId(),
            'withdrawalDate' => $object->getWithdrawalDate()->format('Y-m-d H:i:s'),
            'content'        => $object->getWithdrawalContent(),
            'order'          => $this->_serializeWithdrawalOrder($object->getWithdrawalOrder()),
            'dateCreated'    => $object->getDateCreated()->format('Y-m-d H:i:s'),
            'createdByAdmin' => $object->getCreatedByAdmin()
        ];
        
        return ($encode) ? $this->jsonEncode($withdrawal) : $withdrawal;
    }
    
    
    /**
     * Deserialize a Product JSON String.
     *
     * @param string               $string     JSON string that contains the data of the slider.
     * @param \WithdrawalInterface $baseObject (optional) If provided, this will be the base object to be updated
     *                                         and no new instance will be created.
     *
     * @return \WithdrawalInterface
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
            $withdrawal = $this->withdrawalWriteService->createWithdrawal();
        } else {
            $withdrawal = $baseObject;
        }
        
        // Deserialize Json String
        
        if ($json->withdrawalDate !== null) {
            $withdrawal->setWithdrawalDate(new DateTime($json->withdrawalDate));
        }
        
        if ($json->content !== null) {
            $withdrawal->setWithdrawalContent(new StringType($json->content));
        }
        
        if ($json->createdByAdmin !== null) {
            $withdrawal->setCreatedByAdmin(new BoolType($json->createdByAdmin));
        }
        
        if ($json->order === null) {
            throw new RuntimeException('Required property \'order\' is missing in request body.', 400);
        }
        $withdrawal->setWithdrawalOrder($this->_deserializeWithdrawalOrder($json->order));
        
        return $withdrawal;
    }
    
    
    /**
     * Serializes withdrawal collections.
     *
     * @param WithdrawalCollection $withdrawalCollection Withdrawal collection to be serialized.
     * @param bool                 $encode               (optional) Whether to json_encode the result of the method
     *                                                   (default true).
     *
     * @return string|array Returns the json encoded withdrawal collection(string) or an array that can be easily
     *                      encoded into a JSON string.
     */
    public function serializeWithdrawalCollection(WithdrawalCollection $withdrawalCollection, $encode = true)
    {
        $data = [];
        foreach ($withdrawalCollection->getArray() as $withdrawal) {
            $data[] = $this->serialize($withdrawal, false);
        }
        
        return ($encode) ? $this->jsonEncode($data) : $data;
    }
    
    
    /**
     * Serializes withdrawal order entities.
     *
     * @param \WithdrawalOrderInterface $withdrawalOrder withdrawal order entity to be serialized.
     *
     * @return array Serialized withdrawal order array.
     */
    protected function _serializeWithdrawalOrder(WithdrawalOrderInterface $withdrawalOrder)
    {
        return [
            'orderId'               => $withdrawalOrder->getOrderId(),
            'customerId'            => $withdrawalOrder->getCustomerId(),
            'customerGender'        => $this->_mapGender($withdrawalOrder->getCustomerGender()),
            'customerFirstName'     => $withdrawalOrder->getCustomerFirstName(),
            'customerLastName'      => $withdrawalOrder->getCustomerLastName(),
            'customerStreetAddress' => $withdrawalOrder->getCustomerStreetAddress(),
            'customerPostCode'      => (string)$withdrawalOrder->getCustomerPostCode(),
            'customerCity'          => $withdrawalOrder->getCustomerCity(),
            'customerCountry'       => $withdrawalOrder->getCustomerCountry(),
            'customerEmail'         => $withdrawalOrder->getCustomerEmail(),
            'orderDate'             => $withdrawalOrder->getOrderDate()->format('Y-m-d H:i:s'),
            'deliveryDate'          => $withdrawalOrder->getDeliveryDate()->format('Y-m-d H:i:s')
        ];
    }
    
    
    /**
     * Deserialize withdrawal order entities.
     *
     * @param stdClass $withdrawalOrderData withdrawal order entity.
     *
     * @return WithdrawalOrderInterface Deserialize withdrawal order entity.
     */
    protected function _deserializeWithdrawalOrder(stdClass $withdrawalOrderData)
    {
        
        
        /** @var \WithdrawalOrderInterface $withdrawalOrder */
        return $this->withdrawalWriteService->createOrder(new IdType($withdrawalOrderData->orderId),
                                                          new IdType($withdrawalOrderData->customerId),
                                                          new StringType($this->_mapGender($withdrawalOrderData->customerGender)),
                                                          new StringType($withdrawalOrderData->customerFirstName),
                                                          new StringType($withdrawalOrderData->customerLastName),
                                                          new StringType($withdrawalOrderData->customerStreetAddress),
                                                          new IntType($withdrawalOrderData->customerPostCode),
                                                          new StringType($withdrawalOrderData->customerCity),
                                                          new StringType($withdrawalOrderData->customerCountry),
                                                          new StringType($withdrawalOrderData->customerEmail),
                                                          new DateTime($withdrawalOrderData->orderDate),
                                                          new DateTime($withdrawalOrderData->deliveryDate));
    }
    
    
    /**
     * Maps provided gender to a valid value for the new withdrawals domain used in the API v3.
     *
     * @param string $gender
     *
     * @return string
     */
    protected function _mapGender(string $gender): string {
        if(in_array(strtolower($gender), ['m', 'f', 'd'])){
            return strtolower($gender);
        }
    
        if (in_array(strtolower($gender), ['herr', 'mr'])) {
            return 'm';
        }
    
        if(in_array(strtolower($gender), ['frau', 'miss/ms/mrs'])) {
            return 'f';
        }
        
        return 'd';
    }
}
