<?php
/* --------------------------------------------------------------
   JsonWebTokenParser.inc.php 2019-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JsonWebTokenParser
{
    /**
     * @param \NonEmptyStringType $token
     *
     * @param \StringType         $secret
     *
     * @return \JsonWebToken
     * @throws \JsonWebTokenException
     */
    public static function parseToken(NonEmptyStringType $token, StringType $secret)
    {
        $parts = explode('.', $token->asString());
        if (count($parts) !== 3) {
            throw new JsonWebTokenException('malformed token');
        }
        $base64urlDecodedHeader  = self::base64urlDecode($parts[0]);
        $base64urlDecodedPayload = self::base64urlDecode($parts[1]);
        $inboundSignature        = $parts[2];
        $jsonDecodedHeader       = json_decode($base64urlDecodedHeader, true);
        $jsonDecodedPayload      = json_decode($base64urlDecodedPayload, true);
        $headers                 = MainFactory::create('KeyValueCollection', $jsonDecodedHeader);
        $payload                 = MainFactory::create('KeyValueCollection', $jsonDecodedPayload);
        /** @var \JsonWebToken $jwt */
        $jwt = MainFactory::create('JsonWebToken', $headers, $payload);
        $jwt->setSecret($secret);
        $signature = $jwt->makeSignature($secret);
        /** @var \JsonWebToken $jwt */
        $stdJwt = MainFactory::create('JsonWebToken', $headers, $payload);
        $stdJwt->setIncludeSecretInSignaturePayload(false);
        $stdJwt->setUseRawHmacForSignature(true);
        $stdJwt->setSecret($secret);
        $stdSignature = $stdJwt->makeSignature($secret);
        if ($signature !== $inboundSignature && $stdSignature !== $inboundSignature) {
            throw new JsonWebTokenException('Signature validation failed!');
        }
        
        return $jwt;
    }
    
    
    protected static function base64urlDecode($input)
    {
        $input  = strtr($input, ['-' => '+', '_' => '/']);
        $output = base64_decode($input);
        
        return $output;
    }
}

