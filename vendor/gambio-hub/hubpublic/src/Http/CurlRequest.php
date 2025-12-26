<?php
/* --------------------------------------------------------------
   CurlRequest.php 2022-08-11
   Gambio GmbH
   http://www.gambio.de
   Copyright © 2022 Gambio GmbH
   Released under the MIT License
   [https://opensource.org/licenses/MIT]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace HubPublic\Http;

use HubPublic\Exceptions\CurlRequestException;
use HubPublic\Http\Interfaces\HttpRequestInterface;
use HubPublic\ValueObjects\HttpResponse;
use UnexpectedValueException;

/**
 * Class CurlRequest
 *
 * @package HubPublic\Http
 */
class CurlRequest implements HttpRequestInterface
{
    /**
     * cURL options
     *
     * @var array
     */
    private $options = [];
    
    /**
     * cURL headers
     *
     * @var array
     */
    private $headers = [];
    
    
    /**
     * CurlRequest constructor.
     */
    public function __construct()
    {
        $this->setDefaultOptions();
    }
    
    
    /**
     * Sets the url of the cURL request.
     *
     * @param string $url Target address of cURL request.
     *
     * @return \HubPublic\Http\Interfaces\HttpRequestInterface Same instance for chained method calls.
     */
    public function setUrl(string $url): HttpRequestInterface
    {
        $port = parse_url($url, PHP_URL_PORT);
        
        if (!empty($port)) {
            $url = str_replace(':' . $port, '', $url);
            $this->setOption(CURLOPT_PORT, $port);
        }
        
        $this->setOption(CURLOPT_URL, $url);
        
        return $this;
    }
    
    
    /**
     * Sets a cURL option.
     *
     * Equivalent to curl_setopt().
     *
     * @param int   $name  Name of the option. Use the PHP internal cURL-constants as argument.
     * @param mixed $value Option value.
     *
     * @return \HubPublic\Http\Interfaces\HttpRequestInterface Same instance for chained method calls.
     */
    public function setOption(int $name, $value): HttpRequestInterface
    {
        $this->options[$name] = $value;
        
        return $this;
    }
    
    
    /**
     * Adds values to an option if it exists, otherwise it will be set with given values.
     *
     * @param int   $name
     * @param array $values
     *
     * @return \HubPublic\Http\Interfaces\HttpRequestInterface Same instance for chained method calls.
     *
     * @throws \UnexpectedValueException If provided option name is not an array (thus values cannot be added).
     */
    public function addValues(int $name, array $values): HttpRequestInterface
    {
        if (array_key_exists($name, $this->options)) {
            if (!is_array($this->options[$name])) {
                throw new UnexpectedValueException('cURL option with ID ' . $name . ' does not contain an array. '
                                                    . 'Adding values is not possible.');
            }
            
            $this->options[$name] = array_merge($this->options[$name], $values);
        } else {
            $this->options[$name] = $values;
        }
        
        return $this;
    }
    
    
    /**
     * Executes a cURL request with the given options.
     *
     * @return \HubPublic\ValueObjects\HttpResponse Response of the cURL request.
     *
     * @throws \HubPublic\Exceptions\CurlRequestException If a cURL error occurred or the response code is invalid
     */
    public function execute(): HttpResponse
    {
        $curlHandle = curl_init();
        
        // Assign response headers processing method.
        $this->options[CURLOPT_HEADERFUNCTION] = [$this, '_handleResponseHeader'];
        
        curl_setopt_array($curlHandle, $this->options);
        
        $curlResponse = curl_exec($curlHandle);
        $curlInfo     = curl_getinfo($curlHandle);
        $errorNumber  = curl_errno($curlHandle);
        $errorMessage = curl_error($curlHandle);
        
        if ($errorNumber !== 0) {
            curl_close($curlHandle);
            $this->setDefaultOptions();
            $errorMessage = sprintf('Error %s (%d)', $errorMessage, $errorNumber);
            throw new CurlRequestException($errorMessage, $curlInfo);
        }
        
        $headersSize = array_key_exists(CURLOPT_HEADER, $this->options)
                       && $this->options[CURLOPT_HEADER] ? $curlInfo['header_size'] : 0;
        
        $body = substr($curlResponse, $headersSize);
        
        curl_close($curlHandle);
        
        $this->setDefaultOptions();
        
        return new HttpResponse((int)$curlInfo['http_code'], $this->headers, $body);
    }
    
    
    /**
     * Handle Response Headers
     *
     * This method must be provided as a callback to the CURLOPT_HEADERFUNCTION. It will parse the response header
     * values and add them in the $headers property of the class.
     *
     * @param resource $curlHandle The cURL request handle resource.
     * @param string   $header     The header string to be parsed.
     *
     * @return int Returns the length of the header (required by cURL extension).
     */
    protected function _handleResponseHeader($curlHandle, string $header): int
    {
        $line = preg_replace("/[\r\n]/", '', $header);
        
        if ($line === '') {
            return strlen($header);
        }
        
        if (strpos($line, ':') === false) {
            $this->headers[$line] = null;
        } else {
            $name                 = explode(':', $line)[0];
            $value                = preg_split('/(:)/', $line, PREG_SPLIT_DELIM_CAPTURE);
            $this->headers[$name] = trim(array_pop($value));
        }
        
        return strlen($header);
    }
    
    
    /**
     * Reset cURL options and set default options.
     */
    private function setDefaultOptions(): void
    {
        $this->options = [];
        
        // set default value for returning body data on true
        $this->options[CURLOPT_RETURNTRANSFER] = true;
        
        // set empty default Expect-header to avoid Expect-/Continue handling problems
        $this->options[CURLOPT_HTTPHEADER] = ['Expect:'];
        
        // set user agent for better request monitoring (requested by server admins)
        $this->options[CURLOPT_USERAGENT] = 'Gambio Hub';
        
        // set request timeout in seconds
        $this->options[CURLOPT_CONNECTTIMEOUT] = 10;
        
        // enable redirects
        $this->options[CURLOPT_FOLLOWLOCATION] = true;
        $this->options[CURLOPT_MAXREDIRS]      = 10;
        
        // reset the used port
        unset($this->options[CURLOPT_PORT]);
    }
}
