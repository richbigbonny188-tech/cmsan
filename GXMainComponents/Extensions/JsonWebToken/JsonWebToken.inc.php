<?php
/* --------------------------------------------------------------
   JsonWebToken.inc.php 2019-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class JsonWebToken
{
    protected $headers;
    
    protected $payload;
    
    /** @var \NonEmptyStringType */
    protected $secret;
    
    /** @var bool */
    protected $includeSecretInSignaturePayload;
    
    /** @var bool */
    protected $useRawHmacForSignature;
    
    
    /**
     * JsonWebToken constructor.
     *
     * @param \KeyValueCollection $headers
     * @param \KeyValueCollection $payload
     * @param \NonEmptyStringType $secret
     *
     * @throws \JsonWebTokenException
     */
    public function __construct(KeyValueCollection $headers, KeyValueCollection $payload)
    {
        $this->headers = MainFactory::create('EditableKeyValueCollection', []);
        $this->payload = MainFactory::create('EditableKeyValueCollection', []);
        $this->secret  = null;
        $this->setHeaders($headers);
        $this->setPayload($payload);
        //$this->setSecret($secret);
        $this->setIncludeSecretInSignaturePayload(true);
        $this->setUseRawHmacForSignature(false);
    }
    
    
    public function __toString()
    {
        if ($this->secret === null) {
            $token = 'ERROR: secret not set';
        } else {
            $header    = $this->getHeaderString();
            $payload   = $this->getPayloadString();
            $signature = $this->makeSignature($this->secret);
            $token     = $header . '.' . $payload . '.' . $signature;
        }
        
        return $token;
    }
    
    
    public function getHeaderString()
    {
        $header = $this->base64urlEncode(json_encode($this->headers->getArray()));
        
        return $header;
    }
    
    
    public function getPayloadString()
    {
        $payload = $this->base64urlEncode(json_encode($this->payload->getArray()));
        
        return $payload;
    }
    
    
    protected function base64urlEncode($input)
    {
        $output = base64_encode($input);
        $output = rtrim($output, '=');
        $output = strtr($output, ['+' => '-', '/' => '_']);
        
        return $output;
    }
    
    
    public function makeSignature(NonEmptyStringType $secret)
    {
        $header  = $this->getHeaderString();
        $payload = $this->getPayloadString();
        $data    = $header . '.' . $payload;
        if ($this->isIncludeSecretInSignaturePayload()) {
            $data .= '.' . $secret->asString();
        }
        $signature = hash_hmac('sha256', $data, $secret->asString(), $this->isUseRawHmacForSignature());
        $signature = $this->base64urlEncode($signature);
        
        return $signature;
    }
    
    
    /**
     * @param \KeyValueCollection $headers
     *
     * @throws \JsonWebTokenException
     */
    public function setHeaders(KeyValueCollection $headers)
    {
        try {
            if ($headers->getValue('alg') !== 'HS256' || $headers->getValue('typ') !== 'JWT') {
                throw new JsonWebTokenException('Invalid header values');
            }
            $this->headers = MainFactory::create('EditableKeyValueCollection', $headers->getArray());
        } catch (InvalidArgumentException $e) {
            throw new JsonWebTokenException('Required keys missing in header');
        }
    }
    
    
    public function getHeaders()
    {
        return $this->headers;
    }
    
    
    public function setPayload(KeyValueCollection $payload)
    {
        $this->payload = MainFactory::create('EditableKeyValueCollection', $payload->getArray());
    }
    
    
    public function getPayload()
    {
        return $this->payload;
    }
    
    
    public function setSecret(NonEmptyStringType $secret)
    {
        $this->secret = $secret;
    }
    
    
    /**
     * @return bool
     */
    public function isIncludeSecretInSignaturePayload()
    {
        return $this->includeSecretInSignaturePayload;
    }
    
    
    /**
     * @param bool $includeSecretInSignaturePayload
     */
    public function setIncludeSecretInSignaturePayload($includeSecretInSignaturePayload)
    {
        $this->includeSecretInSignaturePayload = (bool)$includeSecretInSignaturePayload;
    }
    
    
    /**
     * @return bool
     */
    public function isUseRawHmacForSignature()
    {
        return $this->useRawHmacForSignature;
    }
    
    
    /**
     * @param bool $useRawHmacForSignature
     */
    public function setUseRawHmacForSignature($useRawHmacForSignature)
    {
        $this->useRawHmacForSignature = (bool)$useRawHmacForSignature;
    }
}
