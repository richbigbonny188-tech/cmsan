<?php
/* --------------------------------------------------------------
   AbstractHttpPortalClient.php 2016-11-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Http\Interfaces;

use HubPublic\ValueObjects\HttpResponse;

/**
 * Interface HttpRequestInterface
 *
 * @package HubPublic\Http\Interfaces
 */
interface HttpRequestInterface
{
    /**
     * Sets the url of the curl request.
     *
     * @param string $url Target address of curl request.
     *
     * @return HttpRequestInterface Same instance for chained method calls.
     */
    public function setUrl(string $url): HttpRequestInterface;
    
    
    /**
     * Sets a curl option.
     *
     * Equivalent to curl_setopt().
     *
     * @param int   $name  Name of the option. Use the PHP internal curl-constants as argument.
     * @param mixed $value Option value.
     *
     * @return HttpRequestInterface Same instance for chained method calls.
     */
    public function setOption(int $name, $value): HttpRequestInterface;
    
    
    /**
     * Executes a curl request with the given options.
     *
     * @return \HubPublic\ValueObjects\HttpResponse Response of the curl request.
     */
    public function execute(): HttpResponse;
}
