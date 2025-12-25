<?php
/* --------------------------------------------------------------
   HubClientInformationSerializer.php 2022-08-11
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
use HubPublic\ValueObjects\HubClientInformation;
use HubPublic\ValueObjects\HubClientKey;

/**
 * Class HubClientInformationSerializer
 *
 * @package HubPublic\Serializers
 */
class HubClientInformationSerializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * Deserialize a decoded JSON string.
     *
     * @param array $decodedJson Associative array that contains the data.
     *
     * @return \HubPublic\ValueObjects\HubClientInformation New HubClientInformationString that contains the
     *                                                         deserialized data.
     * @throws \HubPublic\Exceptions\BadSerializerValueException If the argument is empty or is missing information.
     */
    public function deserialize(array $decodedJson): HubClientInformation
    {
        $this->verifyArray($decodedJson);
        $this->verifyArrayKeys($decodedJson, ['version', 'url', 'key']);
        
        $decodedJson['version'] = filter_var($decodedJson['version'], FILTER_SANITIZE_STRING);
        $decodedJson['url']     = filter_var($decodedJson['url'], FILTER_SANITIZE_URL);
        
        return new HubClientInformation(
            new HubClientKey($decodedJson['key']),
            $decodedJson['version'],
            $decodedJson['url']
        );
    }
    
    
    /**
     * Serialize a value to a JSON string or array.
     *
     * @param HubClientInformation $hubClientInformation Content to be serialized.
     * @param bool                 $encode               Serialize to string?
     *
     * @return array|string Serialized JSON string or array of given content.
     *
     * @throws \HubPublic\Exceptions\BadSerializerValueException If passed argument is not a
     *                                                          HubPaymentModuleDescriptionCollection
     */
    public function serialize($hubClientInformation, bool $encode = true)
    {
        if (!is_object($hubClientInformation) || !($hubClientInformation instanceof HubClientInformation)) {
            throw new BadSerializerValueException('Argument is not a HubClientInformation: '
                                                  . gettype($hubClientInformation));
        }
        
        $json = [
            'key'     => $hubClientInformation->getClientKey()->asString(),
            'version' => $hubClientInformation->getClientVersion(),
            'url'     => $hubClientInformation->getClientUrl()
        ];
        
        return $encode ? json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $json;
    }
}
