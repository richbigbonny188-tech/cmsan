<?php

/* --------------------------------------------------------------
   HubClientInformation.php 2016-11-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

/**
 * Class HubClientInformation
 *
 * Provides information about a client of the hub
 *
 * @package HubPublic\ValueObjects
 */
class HubClientInformation
{
    /**
     * Hub client key
     *
     * @var HubClientKey
     */
    private $key;
    
    /**
     * Client version
     *
     * @var string;
     */
    private $version;
    
    /**
     * Client URL
     *
     * @var
     */
    private $url;
    
    
    /**
     * HubClientInformation constructor.
     *
     * @param \HubPublic\ValueObjects\HubClientKey $key     HubClientKey instance
     * @param string                               $version Client version
     * @param string                               $url     Client URL
     */
    public function __construct(HubClientKey $key, string $version, string $url)
    {
        $this->key     = $key;
        $this->version = $version;
        $this->url     = $url;
    }
    
    
    /**
     * Returns a hub client key.
     *
     * @return HubClientKey Hub client key
     */
    public function getClientKey(): HubClientKey
    {
        return $this->key;
    }
    
    
    /**
     * Returns the client version.
     *
     * @return string Client version
     */
    public function getClientVersion(): string
    {
        return $this->version;
    }
    
    
    /**
     * Returns the client URL.
     *
     * @return string Client URL
     */
    public function getClientUrl(): string
    {
        return $this->url;
    }
}
