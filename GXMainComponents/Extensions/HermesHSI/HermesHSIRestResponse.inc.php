<?php
/* --------------------------------------------------------------
   HermesHSIRestResponse.inc.php 2019-10-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class HermesHSIRestResponse extends RestResponse
{
    /**
     * @return array
     * @throws HermesHSIInvalidDataException
     */
    public function getParsedBody(): array
    {
        if (empty($this->response_body)) {
            throw new HermesHSIInvalidDataException('response body is empty');
        }
        $parsedBody = json_decode($this->response_body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HermesHSIInvalidDataException('Error decoding response: ' . json_last_error_msg());
        }
        return $parsedBody;
    }
}
