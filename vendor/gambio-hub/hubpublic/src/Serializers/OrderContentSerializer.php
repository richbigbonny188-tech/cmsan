<?php
/* --------------------------------------------------------------
   OrderContentSerializer.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Serializers;

use DateTime;
use HubPublic\Exceptions\BadSerializerValueException;
use HubPublic\Serializers\Interfaces\SerializerInterface;
use HubPublic\ValueObjects\OrderContent;

/**
 * Class OrderContentSerializer
 *
 * @package HubPublic\Serializers
 */
class OrderContentSerializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * Customer serializer instance
     *
     * @var \HubPublic\Serializers\CustomerInformationSerializer
     */
    private $customerInformationSerializer;
    
    
    /**
     * OrderContentSerializer constructor.
     *
     * @param \HubPublic\Serializers\CustomerInformationSerializer $customerInformationSerializer
     */
    public function __construct(CustomerInformationSerializer $customerInformationSerializer)
    {
        $this->customerInformationSerializer = $customerInformationSerializer;
    }
    
    
    /**
     * Deserialize a decoded JSON string.
     *
     * @param array $decodedJson Associative array that contains the data.
     *
     * @return \HubPublic\ValueObjects\OrderContent New OrderContent instance that contains the deserialized data
     * @throws \HubPublic\Exceptions\BadSerializerValueException If provided array is malformed or provides
     *                                                          invalid values.
     */
    public function deserialize(array $decodedJson): OrderContent
    {
        
        $this->verifyArray($decodedJson);
        $this->verifyArrayKeys($decodedJson, ['customer']);
        
        $customer = $this->customerInformationSerializer->deserialize($decodedJson['customer']);
        
        return new OrderContent(
            $customer,
            (float)filter_var($decodedJson['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            filter_var($decodedJson['currencyCode'], FILTER_SANITIZE_STRING),
            filter_var($decodedJson['languageCode'], FILTER_SANITIZE_STRING),
            filter_var($decodedJson['paymentMethod'], FILTER_SANITIZE_STRING),
            filter_var($decodedJson['shippingMethod'], FILTER_SANITIZE_STRING),
            filter_var($decodedJson['customerNumber'], FILTER_SANITIZE_STRING),
            new DateTime($decodedJson['orderDateTime']),
            filter_var($decodedJson['orderNumber'], FILTER_SANITIZE_STRING),
            new DateTime($decodedJson['invoiceDate']),
            filter_var($decodedJson['invoiceNumber'], FILTER_SANITIZE_STRING)
        );
    }
    
    
    /**
     * Serialize a value to a JSON string or array.
     *
     * @param \HubPublic\ValueObjects\OrderContent $orderContent OrderContent to be serialized.
     * @param bool                                 $encode       Serialize to string?
     *
     * @return array|string Serialized JSON string or array of given content.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException if $orderContent is not an object or is not an instance
     *                                                          of OrderContent
     */
    public function serialize($orderContent, bool $encode = true)
    {
        if (!is_object($orderContent) || !($orderContent instanceof OrderContent)) {
            throw new BadSerializerValueException('Argument is not a OrderContent: ' . gettype($orderContent));
        }
        
        $orderDateTime   = $orderContent->getOrderDateTime();
        $invoiceDateTime = $orderContent->getInvoiceDateTime();
        
        $json = [
            'customer'       => $this->customerInformationSerializer->serialize($orderContent->getCustomer(), $encode),
            'amount'         => $orderContent->getAmount(),
            'currencyCode'   => $orderContent->getCurrencyCode(),
            'languageCode'   => $orderContent->getLanguageCode(),
            'paymentMethod'  => $orderContent->getPaymentMethod(),
            'shippingMethod' => $orderContent->getShippingMethod(),
            'customerNumber' => $orderContent->getCustomerNumber(),
            'orderDateTime'  => $orderDateTime->format('Y-m-d H:i:s'),
            'orderNumber'    => $orderContent->getOrderNumber(),
            'invoiceDate'    => $invoiceDateTime->format('Y-m-d H:i:s'),
            'invoiceNumber'  => $orderContent->getInvoiceNumber()
        ];
        
        return $encode ? json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $json;
    }
}
