<?php
/* --------------------------------------------------------------
   HubClientKeySerializer.php 2018-01-29
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2018 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

namespace HubPublic\Serializers;

use HubPublic\Exceptions\BadSerializerValueException;
use HubPublic\Serializers\Interfaces\SerializerInterface;
use HubPublic\ValueObjects\HubClientKey;

/**
 * Class HubClientKeySerializer
 *
 * @package HubPublic\Serializers
 */
class HubClientKeySerializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * Deserialize a decoded JSON string.
     *
     * @param array $decodedJson Associative array that contains the data.
     *
     * @return \HubPublic\ValueObjects\HubClientKey Returns the deserialized client key.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException If the argument is empty or is malformed.
     */
    public function deserialize(array $decodedJson): HubClientKey
    {
        $this->verifyArray($decodedJson);
        $this->verifyArrayKeys($decodedJson, ['clientKey']);
        
        return new HubClientKey($decodedJson['clientKey']);
    }
    
    
    /**
     * Serialize a value to a JSON string or array.
     *
     * @param mixed $value  Content to be serialized.
     * @param bool  $encode Defines whether to encode into JSON or return an associative array.
     *
     * @return array|string Returns the serialized object.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException If the argument is invalid.
     */
    public function serialize($value, bool $encode = true)
    {
        if (!is_object($value) || !($value instanceof HubClientKey)) {
            throw new BadSerializerValueException('Argument is not a HubClientKey: ' . gettype($value));
        }
        
        $json = [
            'clientKey' => $value->asString()
        ];
        
        return $encode ? json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $json;
    }
}
