<?php
/* --------------------------------------------------------------
   CurlClient.php 2018-08-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

namespace Gambio\AdminFeed;

/**
 * Class CurlClient
 *
 * @package Gambio\AdminFeed
 */
class CurlClient
{
    /**
     * @var string
     */
    private $responseContent;
    
    /**
     * @var array
     */
    private $responseInformation;
    
    
    /**
     * Executes a get request with curl.
     *
     * @param string $url
     * @param array  $options
     */
    public function executeGet($url, $options = [])
    {
        $options = $options + [
                CURLOPT_URL            => $url,
                CURLOPT_CONNECTTIMEOUT => isset($options[CURLOPT_CONNECTTIMEOUT]) ? $options[CURLOPT_CONNECTTIMEOUT] : 10,
                CURLOPT_RETURNTRANSFER => isset($options[CURLOPT_RETURNTRANSFER]) ? $options[CURLOPT_RETURNTRANSFER] : true,
            ];
        
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $options);
        $this->responseContent     = curl_exec($curlHandle);
        $this->responseInformation = curl_getinfo($curlHandle);
        curl_close($curlHandle);
    }
    
    
    /**
     * Executes a post request with curl.
     *
     * @param string $url
     * @param array  $data
     * @param array  $options
     */
    public function executePost($url, $data = [], $options = [])
    {
        $options = $options + [
                CURLOPT_URL            => $url,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $data,
                CURLOPT_CONNECTTIMEOUT => isset($options[CURLOPT_CONNECTTIMEOUT]) ? $options[CURLOPT_CONNECTTIMEOUT] : 10,
                CURLOPT_RETURNTRANSFER => isset($options[CURLOPT_RETURNTRANSFER]) ? $options[CURLOPT_RETURNTRANSFER] : true,
            ];
        
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $options);
        $this->responseContent     = curl_exec($curlHandle);
        $this->responseInformation = curl_getinfo($curlHandle);
        curl_close($curlHandle);
    }
    
    
    /**
     * Executes a head request with curl.
     *
     * @param string $url
     * @param array  $options
     */
    public function executeHead($url, $options = [])
    {
        $options = $options + [
                CURLOPT_URL            => $url,
                CURLOPT_NOBODY         => true,
                CURLOPT_CONNECTTIMEOUT => isset($options[CURLOPT_CONNECTTIMEOUT]) ? $options[CURLOPT_CONNECTTIMEOUT] : 10,
                CURLOPT_RETURNTRANSFER => isset($options[CURLOPT_RETURNTRANSFER]) ? $options[CURLOPT_RETURNTRANSFER] : true,
            ];
        
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, $options);
        curl_exec($curlHandle);
        $this->responseContent     = null;
        $this->responseInformation = curl_getinfo($curlHandle);
        curl_close($curlHandle);
    }
    
    
    /**
     * Returns the content of the curl response.
     *
     * @return string|null
     */
    public function getContent()
    {
        if (isset($this->responseContent)) {
            return $this->responseContent;
        }
        
        return null;
    }
    
    
    /**
     * Returns the status code of the curl response.
     *
     * @return int|null
     */
    public function getStatusCode()
    {
        if (isset($this->responseInformation['http_code'])) {
            return $this->responseInformation['http_code'];
        }
        
        return null;
    }
    
    
    /**
     * Returns the content type of the curl response.
     *
     * @return string|null
     */
    public function getContentType()
    {
        if (isset($this->responseInformation['content_type'])) {
            return $this->responseInformation['content_type'];
        }
        
        return null;
    }
    
    
    /**
     * Returns all or a specific information of the curl response.
     *
     * @param string|null $key
     *
     * @return array|mixed|null
     */
    public function getInformation($key = null)
    {
        if ($key === null && isset($this->responseInformation)) {
            return $this->responseInformation;
        } elseif ($key !== null && isset($this->responseInformation[$key])) {
            return $this->responseInformation[$key];
        }
        
        return null;
    }
}