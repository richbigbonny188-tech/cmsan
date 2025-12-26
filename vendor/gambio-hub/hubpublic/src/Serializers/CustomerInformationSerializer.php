<?php
/* --------------------------------------------------------------
   CustomerInformationSerializer.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Serializers;

use HubPublic\Exceptions\BadSerializerValueException;
use HubPublic\Serializers\Interfaces\SerializerInterface;
use HubPublic\ValueObjects\CustomerInformation;

/**
 * Class CustomerInformationSerializer
 *
 * @package HubPublic\Serializers
 */
class CustomerInformationSerializer extends AbstractSerializer implements SerializerInterface
{
    private $keys = [
        'customerNumber',
        'customerFirstName',
        'customerLastName',
        'customerTitle',
        'customerGender',
        'customerCompany',
        'customerAddress1',
        'customerAddress2',
        'customerPostalCode',
        'customerCity',
        'customerState',
        'customerCountry',
        'customerCountryCode',
        'customerDateOfBirth',
        'customerPhone',
        'customerFax',
        'customerEmail',
        //'customerB2bStatus', // Older Hub connectors will not send this property.
        'billingFirstName',
        'billingLastName',
        'billingTitle',
        'billingGender',
        'billingCompany',
        'billingAddress1',
        'billingAddress2',
        'billingPostalCode',
        'billingCity',
        'billingState',
        'billingCountry',
        'billingCountryCode',
        'billingDateOfBirth',
        'billingPhone',
        'billingFax',
        'billingEmail',
        'shippingFirstName',
        'shippingLastName',
        'shippingTitle',
        'shippingGender',
        'shippingCompany',
        'shippingAddress1',
        'shippingAddress2',
        'shippingPostalCode',
        'shippingCity',
        'shippingState',
        'shippingCountry',
        'shippingCountryCode',
        'shippingDateOfBirth',
        'shippingPhone',
        'shippingFax',
        'shippingEmail'
    ];
    
    
    /**
     * Deserialize a decoded JSON string.
     *
     * @param array $decodedJson Associative array that contains the data.
     *
     * @return \HubPublic\ValueObjects\CustomerInformation New CustomerInformation instance that contains the
     *                                                    deserialized data
     * @throws \HubPublic\Exceptions\BadSerializerValueException If the array is malformed or does not contain
     *                                                          valid data
     */
    public function deserialize(array $decodedJson): CustomerInformation
    {
        $this->verifyArray($decodedJson);
        
        $arguments = $this->getCustomerInformation($decodedJson);
        
        return new CustomerInformation(
            filter_var($arguments['customerNumber'], FILTER_SANITIZE_STRING),
            str_replace(['<', '>'], '', $arguments['customerFirstName']),
            str_replace(['<', '>'], '', $arguments['customerLastName']),
            filter_var($arguments['customerTitle'], FILTER_SANITIZE_STRING),
            filter_var($arguments['customerGender'], FILTER_SANITIZE_STRING),
            str_replace(['<', '>'], '', $arguments['customerCompany']),
            str_replace(['<', '>'], '', $arguments['customerAddress1']),
            str_replace(['<', '>'], '', $arguments['customerAddress2']),
            filter_var($arguments['customerPostalCode'], FILTER_SANITIZE_STRING),
            str_replace(['<', '>'], '', $arguments['customerCity']),
            str_replace(['<', '>'], '', $arguments['customerState']),
            str_replace(['<', '>'], '', $arguments['customerCountry']),
            filter_var($arguments['customerCountryCode'], FILTER_SANITIZE_STRING),
            filter_var($arguments['customerDateOfBirth'], FILTER_SANITIZE_STRING),
            filter_var($arguments['customerPhone'], FILTER_SANITIZE_STRING),
            filter_var($arguments['customerFax'], FILTER_SANITIZE_STRING),
            filter_var($arguments['customerEmail'], FILTER_SANITIZE_EMAIL),
            str_replace(['<', '>'], '', $arguments['customerB2bStatus'] ?? ''),
            // Older Hub connectors will not send this property.
            str_replace(['<', '>'], '', $arguments['billingFirstName']),
            str_replace(['<', '>'], '', $arguments['billingLastName']),
            filter_var($arguments['billingTitle'], FILTER_SANITIZE_STRING),
            filter_var($arguments['billingGender'], FILTER_SANITIZE_STRING),
            str_replace(['<', '>'], '', $arguments['billingCompany']),
            str_replace(['<', '>'], '', $arguments['billingAddress1']),
            str_replace(['<', '>'], '', $arguments['billingAddress2']),
            filter_var($arguments['billingPostalCode'], FILTER_SANITIZE_STRING),
            str_replace(['<', '>'], '', $arguments['billingCity']),
            str_replace(['<', '>'], '', $arguments['billingState']),
            str_replace(['<', '>'], '', $arguments['billingCountry']),
            filter_var($arguments['billingCountryCode'], FILTER_SANITIZE_STRING),
            filter_var($arguments['billingDateOfBirth'], FILTER_SANITIZE_STRING),
            filter_var($arguments['billingPhone'], FILTER_SANITIZE_STRING),
            filter_var($arguments['billingFax'], FILTER_SANITIZE_STRING),
            filter_var($arguments['billingEmail'], FILTER_SANITIZE_EMAIL),
            str_replace(['<', '>'], '', $arguments['shippingFirstName']),
            str_replace(['<', '>'], '', $arguments['shippingLastName']),
            filter_var($arguments['shippingTitle'], FILTER_SANITIZE_STRING),
            filter_var($arguments['shippingGender'], FILTER_SANITIZE_STRING),
            str_replace(['<', '>'], '', $arguments['shippingCompany']),
            str_replace(['<', '>'], '', $arguments['shippingAddress1']),
            str_replace(['<', '>'], '', $arguments['shippingAddress2']),
            filter_var($arguments['shippingPostalCode'], FILTER_SANITIZE_STRING),
            str_replace(['<', '>'], '', $arguments['shippingCity']),
            str_replace(['<', '>'], '', $arguments['shippingState']),
            str_replace(['<', '>'], '', $arguments['shippingCountry']),
            filter_var($arguments['shippingCountryCode'], FILTER_SANITIZE_STRING),
            filter_var($arguments['shippingDateOfBirth'], FILTER_SANITIZE_STRING),
            filter_var($arguments['shippingPhone'], FILTER_SANITIZE_STRING),
            filter_var($arguments['shippingFax'], FILTER_SANITIZE_STRING),
            filter_var($arguments['shippingEmail'], FILTER_SANITIZE_EMAIL)
        );
    }
    
    
    /**
     * Serialize a value to a JSON string or array.
     *
     * @param CustomerInformation $customerInformation Content to be serialized.
     * @param bool                $encode              Serialize to string?
     *
     * @return array|string Serialized JSON string or array of given content.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException If passed argument is not a CustomerInformation.
     */
    public function serialize($customerInformation, bool $encode = true)
    {
        if (!is_object($customerInformation) || !($customerInformation instanceof CustomerInformation)) {
            throw new BadSerializerValueException('Argument is not a CustomerInformation: '
                                                  . gettype($customerInformation));
        }
        
        $json = [];
        
        foreach ($this->keys as $key) {
            $json[$key] = $customerInformation->{'get' . ucfirst($key)}();
        }
        
        return $encode ? json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $json;
    }
    
    
    /**
     * Returns customer information.
     *
     * Checks if no customer information is missing and ensure the valid type of every customer information. A customer
     * information array will be returned if validation succeeds.
     *
     * @param array $json Customer information as array
     *
     * @return array Customer information
     */
    private function getCustomerInformation(array $json): array
    {
        $this->verifyArrayKeys($json, $this->keys);
        
        $customerInformation = [];
        
        foreach ($this->keys as $key) {
            $customerInformation[$key] = $json[$key];
        }
        
        return $customerInformation;
    }
}
