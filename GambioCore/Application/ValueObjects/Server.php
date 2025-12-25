<?php
/* --------------------------------------------------------------
 Server.php 2021-05-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

declare(strict_types=1);

namespace Gambio\Core\Application\ValueObjects;

/**
 * Class Server
 *
 * @package Gambio\Core\Application\ValueObjects
 */
class Server
{
    /**
     * @var bool
     */
    private $sslEnabled;
    
    /**
     * @var string
     */
    private $requestUri;
    
    
    /**
     * Server constructor.
     *
     * @param bool   $sslEnabled
     * @param string $requestUri
     */
    public function __construct(bool $sslEnabled, string $requestUri)
    {
        $this->sslEnabled = $sslEnabled;
        $this->requestUri = $requestUri;
    }
    
    
    /**
     * Returns the SSL enabled state.
     *
     * @return bool
     */
    public function sslEnabled(): bool
    {
        return $this->sslEnabled;
    }
    
    
    /**
     * Returns the request URI for the current request.
     *
     * @return string
     */
    public function requestUri(): string
    {
        return $this->requestUri;
    }
}