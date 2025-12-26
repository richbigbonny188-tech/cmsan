<?php
/* --------------------------------------------------------------
   HttpCode.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\ValueObjects;

use HubPublic\Exceptions\InvalidHttpCodeException;

/**
 * Represents a HTTP response code (e.g. 200 or 404)
 *
 * @package HubPublic\ValueObjects
 */
class HttpCode
{
    /**
     * the HTTP response code
     *
     * @var int
     */
    private $httpCode;
    
    
    /**
     * HttpCode constructor
     *
     * @param int $httpCode an HTTP response code in the range 100 - 599
     *
     * @throws \HubPublic\Exceptions\InvalidHttpCodeException If httpCode is out of range
     */
    public function __construct(int $httpCode)
    {
        if ($httpCode < 100 || $httpCode > 599) {
            throw new InvalidHttpCodeException();
        }
        $this->httpCode = $httpCode;
    }
    
    
    /**
     * Returns integer representation of HTTP response code.
     *
     * @return int HTTP response code
     */
    public function asInt(): int
    {
        return $this->httpCode;
    }
}
