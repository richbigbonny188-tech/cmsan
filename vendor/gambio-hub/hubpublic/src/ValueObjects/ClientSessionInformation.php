<?php

/* --------------------------------------------------------------
   ClientSessionInformation.php 2017-02-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidUserIpException;

/**
 * Class ClientSessionInformation
 *
 * Provides information about a session of a client.
 *
 * @package HubPublic\ValueObjects
 */
class ClientSessionInformation
{
    /**
     * Hub session key
     *
     * @var HubSessionKey
     */
    private $sessionKey;
    
    /**
     * Language code
     *
     * @var string
     */
    private $languageCode;
    
    /**
     * Currency
     *
     * @var string
     */
    private $currency;
    
    /**
     * User IP
     *
     * @var string
     */
    private $userIp;
    
    /**
     * User agent
     *
     * @var string
     */
    private $userAgent;
    
    
    /**
     * ClientSessionInformation constructor.
     *
     * @param \HubPublic\ValueObjects\HubSessionKey $sessionKey   HubSessionKey instance
     * @param string                                $languageCode Language code
     * @param string                                $currency     Currency
     * @param string                                $userIp       User IP
     * @param string                                $userAgent    User agent
     *
     * @throws \HubPublic\Exceptions\InvalidUserIpException If the user IP is invalid.
     */
    public function __construct(
        HubSessionKey $sessionKey,
        string $languageCode,
        string $currency,
        string $userIp,
        string $userAgent
    ) {
        $this->sessionKey   = $sessionKey;
        $this->languageCode = $languageCode;
        $this->currency     = $currency;
        $this->userAgent    = $userAgent;
        $this->userIp       = filter_var($userIp, FILTER_VALIDATE_IP);
        
        if (!$this->userIp) {
            throw new InvalidUserIpException('Invalid IP address provided!');
        }
    }
    
    
    /**
     * Returns a HubSessionKey.
     *
     * @return \HubPublic\ValueObjects\HubSessionKey HubSessionKey instance
     */
    public function getHubSessionKey(): HubSessionKey
    {
        return $this->sessionKey;
    }
    
    
    /**
     * Returns a language code
     *
     * @return string Language code
     */
    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
    
    
    /**
     * Returns a currency code
     *
     * @return string Currency code
     */
    public function getCurrencyCode(): string
    {
        return $this->currency;
    }
    
    
    /**
     * Returns a user IP
     *
     * @return string User IP
     */
    public function getUserIp(): string
    {
        return $this->userIp;
    }
    
    
    /**
     * Returns a user agent
     *
     * @return string User agent
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }
}
