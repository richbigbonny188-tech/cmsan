<?php
/* --------------------------------------------------------------
  JsonHttpControllerResponseServerData.inc.php 2017-02-15
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2017 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class JsonHttpControllerResponseServerData extends JsonHttpControllerResponseServerData_parent
{
    /**
     * Initializes the json http controller response.
     *
     * @param array $contentArray         Array which will be encoded in json format.
     * @param array $responseHeadersArray Array which contains information about the http response headers.
     */
    public function __construct(array $contentArray, $responseHeadersArray = null)
    {
        parent::__construct($contentArray);
        
        if($responseHeadersArray !== null)
        {
            $this->httpHeadersArray = $responseHeadersArray;
        }
    }
}