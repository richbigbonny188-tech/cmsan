<?php
/* --------------------------------------------------------------
   HttpResponse.php 2016-11-17
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
 * Class HttpResponse
 *
 * @package HubPublic\ValueObjects
 */
class HttpResponse
{
    /**
     * @var int
     */
    private $statusCode;
    
    /**
     * @var array
     */
    private $headers;
    
    /**
     * @var string
     */
    private $body;
    
    
    /**
     * CurlResponse constructor.
     *
     * @param int    $statusCode The response status code.
     * @param string $body       The response body.
     */
    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
        $this->body       = $body;
    }
    
    
    /**
     * Returns the HTTP status code of the cURL response.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    
    /**
     * Returns the HTTP headers of the cURL response.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    
    /**
     * Returns the body content of the cURL response.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
