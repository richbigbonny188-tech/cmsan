<?php
/* --------------------------------------------------------------
   ClientSessionInformationSerializer.php 2022-08-11
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
use HubPublic\ValueObjects\ClientSessionInformation;
use HubPublic\ValueObjects\HubSessionKey;

/**
 * Class ClientSessionInformationSerializer
 *
 * @package HubPublic\Serializers
 */
class ClientSessionInformationSerializer extends AbstractSerializer implements SerializerInterface
{
    /**
     * Deserialize a decoded JSON string.
     *
     * @param array $decodedJson Associative array that contains the data.
     *
     * @return \HubPublic\ValueObjects\ClientSessionInformation New ClientSessionInformation instance that contains the
     *                                                          deserialized data
     * @throws \HubPublic\Exceptions\BadSerializerValueException If provided array string is malformed.
     * @throws \HubPublic\Exceptions\InvalidUserIpException
     */
    public function deserialize(array $decodedJson): ClientSessionInformation
    {
        $this->verifyArray($decodedJson);
        
        $this->verifyArrayKeys($decodedJson, ['languageCode', 'currency', 'userAgent', 'sessionKey']);
        
        $decodedJson['languageCode'] = filter_var($decodedJson['languageCode'], FILTER_SANITIZE_STRING);
        $decodedJson['currency']     = filter_var($decodedJson['currency'], FILTER_SANITIZE_STRING);
        $decodedJson['userAgent']    = filter_var($decodedJson['userAgent'], FILTER_SANITIZE_STRING);
        
        return new ClientSessionInformation(
            new HubSessionKey($decodedJson['sessionKey']),
            $decodedJson['languageCode'],
            $decodedJson['currency'],
            $decodedJson['userIp'],
            $decodedJson['userAgent']
        );
    }
    
    
    /**
     * Serialize a value to a JSON string or array.
     *
     * @param \HubPublic\ValueObjects\ClientSessionInformation $value  Content to be serialized.
     * @param bool                                             $encode Serialize to string?
     *
     * @return array|string Serialized JSON string or array of given content.
     * @throws \HubPublic\Exceptions\BadSerializerValueException if $value is not an object or is not an instance of
     *                                                          ClientSessionInformation
     */
    public function serialize($value, bool $encode = true)
    {
        if (!is_object($value) || !($value instanceof ClientSessionInformation)) {
            throw new BadSerializerValueException('Argument is not a ClientSessionInformation: ' . gettype($value));
        }
        
        $json = [
            'sessionKey'   => $value->getHubSessionKey()->asString(),
            'languageCode' => $value->getLanguageCode(),
            'currency'     => $value->getCurrencyCode(),
            'userIp'       => $value->getUserIp(),
            'userAgent'    => $value->getUserAgent()
        ];
        
        return $encode ? json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $json;
    }
}
