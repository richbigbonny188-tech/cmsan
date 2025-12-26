<?php
/* --------------------------------------------------------------
   JsonWebToken.php 2019-12-04
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Core\Auth\Model;

/**
 * Class JsonWebToken
 *
 * @package Gambio\Core\Auth\Model
 */
class JsonWebToken
{
    /**
     * @var string
     */
    private $secret;
    
    /**
     * @var array
     */
    private $headers;
    
    /**
     * @var array
     */
    private $payload;
    
    /**
     * @var bool
     */
    private $includeSecretInSignaturePayload;
    
    /**
     * @var bool
     */
    private $useRawHmacForSignature;
    
    
    /**
     * JsonWebToken constructor.
     *
     * @param string $secret
     * @param array  $headers
     * @param array  $payload
     * @param bool   $includeSecretInSignaturePayload
     * @param bool   $useRawHmacForSignature
     */
    private function __construct(
        string $secret,
        array $headers,
        array $payload,
        bool $includeSecretInSignaturePayload = false,
        bool $useRawHmacForSignature = true
    ) {
        $this->secret                          = $secret;
        $this->headers                         = $headers;
        $this->payload                         = $payload;
        $this->includeSecretInSignaturePayload = $includeSecretInSignaturePayload;
        $this->useRawHmacForSignature          = $useRawHmacForSignature;
    }
    
    
    /**
     * @param string $secret
     * @param array  $headers
     * @param array  $payload
     *
     * @return JsonWebToken
     */
    public static function createToken(string $secret, array $headers, array $payload): JsonWebToken
    {
        return new self($secret, $headers, $payload);
    }
    
    
    /**
     * @param string $secret
     * @param array  $headers
     * @param array  $payload
     *
     * @return JsonWebToken
     *
     * @deprecated Should only be used for compatibility use cases. Primary use `getJsonWebToken` method.
     */
    public static function createCompatibilityToken(string $secret, array $headers, array $payload): JsonWebToken
    {
        return new self($secret, $headers, $payload, true, false);
    }
    
    
    /**
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }
    
    
    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->payload;
    }
    
    
    /**
     * @return string
     */
    public function asString(): string
    {
        $header    = $this->getHeaderString();
        $payload   = $this->getPayloadString();
        $signature = $this->getSignature();
        
        return $header . '.' . $payload . '.' . $signature;
    }
    
    
    /**
     * @return string
     */
    private function getHeaderString(): string
    {
        return $this->base64UrlEncode(json_encode($this->headers));
    }
    
    
    /**
     * @return string
     */
    private function getPayloadString(): string
    {
        return $this->base64UrlEncode(json_encode($this->payload));
    }
    
    
    /**
     * @return string
     */
    public function getSignature(): string
    {
        $header  = $this->getHeaderString();
        $payload = $this->getPayloadString();
        $data    = $header . '.' . $payload;
        if ($this->includeSecretInSignaturePayload) {
            $data .= '.' . $this->secret;
        }
        $signature = hash_hmac('sha256', $data, $this->secret, $this->useRawHmacForSignature);
        $signature = $this->base64UrlEncode($signature);
        
        return $signature;
    }
    
    
    /**
     * @param $input
     *
     * @return string
     */
    private function base64UrlEncode($input): string
    {
        $output = base64_encode($input);
        $output = rtrim($output, '=');
        $output = strtr($output, ['+' => '-', '/' => '_']);
        
        return $output;
    }
    
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->asString();
    }
}