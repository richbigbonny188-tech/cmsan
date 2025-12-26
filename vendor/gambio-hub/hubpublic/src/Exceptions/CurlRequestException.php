<?php
/* --------------------------------------------------------------
   CurlRequestException.php 2016-07-14
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2017 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Exceptions;

/**
 * Class CurlRequestException
 *
 * This exception gets thrown by \HubPublic\Http\CurlRequest
 *
 * @package HubPublic\Exceptions
 */
class CurlRequestException extends HubWarningException
{
    /**
     * cURL information as returned by curl_getinfo()
     *
     * @var array
     */
    protected $curlInfo;
    
    
    /**
     * CurlRequestException constructor.
     *
     * @param string      $message  The Exception message to throw.
     * @param array|false $curlInfo The cURL information array, retrieved with "curl_getinfo".
     */
    public function __construct(string $message, $curlInfo)
    {
        $this->curlInfo = $curlInfo ? : [];
        parent::__construct($message);
    }
    
    
    /**
     * Returns detailed information regarding the outcome of a cURL request
     *
     * @return array of cURL data or empty array if unavailable
     */
    public function getCurlInfo(): array
    {
        return $this->curlInfo;
    }
}
