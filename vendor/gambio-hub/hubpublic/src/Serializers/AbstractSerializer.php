<?php
/* --------------------------------------------------------------
   SerializerInterface.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/
declare(strict_types=1);

namespace HubPublic\Serializers;

use HubPublic\Exceptions\BadSerializerValueException;

/**
 * Class AbstractSerializer
 *
 * Serializers should extend from this.
 *
 * @package HubPublic\Serializers
 */
abstract class AbstractSerializer
{
    /**
     * Deserializes an associative array.
     *
     * @param array $decodedJson Associative array that contains the data.
     */
    abstract public function deserialize(array $decodedJson);
    
    
    /**
     * Serializes an object to a JSON string or array.
     *
     * @param mixed $value  Value to be serialized.
     * @param bool  $encode Serialize to string?
     *
     * @return array|string Serialized JSON string or array of given content.
     */
    abstract public function serialize($value, bool $encode = true);
    
    
    /**
     * Verifies the integrity of the given associative array.
     *
     * @param array $decodedJson Associative array which was converted from JSON.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException If the array is malformed or does not contain
     *                                                          valid data.
     */
    protected function verifyArray(array $decodedJson): void
    {
        if (empty($decodedJson)) {
            throw new BadSerializerValueException('Given object is empty.');
        }
    }
    
    
    /**
     * Verifies that a set of keys is found in the given array.
     *
     * @param array $decodedJson  The decoded JSON array that is to be verified.
     * @param array $expectedKeys Array of keys that are expected to be found in the array.
     *
     * @throws BadSerializerValueException if the array does not contain expected data.
     */
    
    protected function verifyArrayKeys(array $decodedJson, array $expectedKeys): void
    {
        foreach ($expectedKeys as $key) {
            if (!array_key_exists($key, $decodedJson)) {
                throw new BadSerializerValueException('Key ' . $key . ' not found in given array.');
            }
        }
    }
}
