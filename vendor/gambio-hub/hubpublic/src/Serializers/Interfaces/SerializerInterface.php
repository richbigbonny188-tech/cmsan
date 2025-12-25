<?php
/* --------------------------------------------------------------
   SerializerInterface.php 2018-01-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Serializers\Interfaces;

/**
 * Interface DeserializeInterface
 *
 * @package HubPublic\Serializers\Interfaces
 */
interface SerializerInterface
{
    /**
     * Serialize a value to a JSON string, or to an array if $encode is set to false.
     *
     * @param mixed $value  Content to be serialized.
     * @param bool  $encode Serialize to string?
     */
    public function serialize($value, bool $encode = true);
    
    
    /**
     * Deserialize a [converted JSON] associative array.
     *
     * @param array $decodedJson associative array that contains the data.
     */
    public function deserialize(array $decodedJson);
}
