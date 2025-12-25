<?php
/* --------------------------------------------------------------
   HermesHSIRestRequest.inc.php 2020-04-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSIRestRequest extends RestRequest
{
    protected $accessToken;
    
    public function __construct($method, $url, $data = null, $headers = null)
    {
        parent::__construct($method, $url, $data, $headers);
        $this->headers[] = 'Content-Type: application/json';
        $this->headers[] = 'Expect:';
    }
    
    
    public function setAccessToken(HermesHSIAccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
        $this->headers[] = 'Authorization: Bearer ' . $accessToken->getAccessToken();
    }
    
}
