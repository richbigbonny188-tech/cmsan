<?php
/* --------------------------------------------------------------
   JsonHttpControllerResponse.inc.php 2019-07-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpControllerResponse');

/**
 * Class JsonHttpControllerResponse
 *
 * @category   System
 * @package    Http
 * @subpackage ValueObjects
 * @extends    HttpControllerResponse
 */
class JsonHttpControllerResponse extends HttpControllerResponse
{
    /**
     * Initializes the json http controller response.
     *
     * @param array $contentArray         Array which will be encoded in json format.
     * @param array $responseHeadersArray Array which contains information about the http response headers.
     */
    public function __construct(array $contentArray, $responseHeadersArray = null)
    {
        $this->httpBody = json_encode($contentArray);
        
        if ($responseHeadersArray !== null) {
            $contentTypeHeaderExists = false;
            foreach ($responseHeadersArray as $header) {
                if (stripos($header, 'content-type') !== false) {
                    $contentTypeHeaderExists = true;
                }
            }
            
            if (!$contentTypeHeaderExists) {
                $responseHeadersArray[] = 'Content-Type: application/json';
            }
            
            $this->httpHeadersArray = $responseHeadersArray;
        } else {
            $this->httpHeadersArray = ['Content-Type: application/json'];
        }
    }
}