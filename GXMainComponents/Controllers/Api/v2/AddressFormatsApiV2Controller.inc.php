<?php
/* --------------------------------------------------------------
   AddressFormatsApiV2Controller.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class AddressFormatsApiV2Controller
 *
 * Provides address format information.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class AddressFormatsApiV2Controller extends HttpApiV2Controller
{
    /**
     * @api             {get} /address_formats Get Address Formats
     * @apiVersion      2.4.0
     * @apiName         GetAddressFormats
     * @apiGroup        AddressFormats
     *
     * @apiDescription
     * Get all address formats or a single address format.
     *
     * @apiExample {curl} Get All Address Formats
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/address_formats
     *
     * @apiSuccessExample {json} Success-Response
     * [
     *   {
     *     "id": "1",
     *     "format": "$firstname $lastname$cr$streets$cr$city, $postcode$cr$statecomma$country",
     *     "summary": "$city / $country"
     *   },
     *   {
     *     "id": "2",
     *     "format": "$firstname $lastname$cr$streets$cr$city, $state    $postcode$cr$country",
     *     "summary": "$city, $state / $country"
     *   },
     *   {
     *     "id": "3",
     *     "format": "$firstname $lastname$cr$streets$cr$city$cr$postcode - $statecomma$country",
     *     "summary": "$state / $country"
     *   },
     *   {
     *     "id": "4",
     *     "format": "$firstname $lastname$cr$streets$cr$city ($postcode)$cr$country",
     *     "summary": "$postcode / $country"
     *   },
     *   {
     *     "id": "5",
     *     "format": "$firstname $lastname$cr$streets$cr$postcode $city$cr$country",
     *     "summary": "$city / $country"
     *   }
     * ]
     *
     * @apiExample {curl} Get Address Format With ID = 1
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/address_formats/1
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "id": "1",
     *   "format": "$firstname $lastname$cr$streets$cr$city, $postcode$cr$statecomma$country",,
     *   "summary": "$city / $country"
     * }
     *
     * @apiError        404-NotFound Tax rate record could not be found.
     * @apiError        400-BadRequest Invalid rax rate id provided (expected integer).
     *
     * @apiErrorExample Error-Response (Address Format Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Address format record could not be found."
     * }
     *
     * @apiErrorExample Error-Response (Invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Provided address format ID is invalid, integer expected."
     * }
     */
    public function get()
    {
        if (!isset($this->uri[1])) {
            $addressFormats = [];
            
            $query = 'SELECT 
							`address_format_id` AS `id`, 
							`address_format` AS `format`, 
							`address_summary` AS `summary`
						FROM `address_format`
						ORDER BY `address_format_id`';
            
            $result = xtc_db_query($query);
            
            while ($addressFormat = xtc_db_fetch_array($result)) {
                $addressFormats[] = $addressFormat;
            }
        } elseif (!is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Provided address format ID is invalid, integer expected: '
                                         . gettype($this->uri[1]), 400);
        } else {
            $query = 'SELECT 
							`address_format_id` AS `id`, 
							`address_format` AS `format`, 
							`address_summary` AS `summary`
						FROM `address_format`
						WHERE `address_format_id` = ' . (int)$this->uri[1];
            
            $result = xtc_db_query($query);
            
            if (xtc_db_num_rows($result)) {
                $addressFormats = xtc_db_fetch_array($result);
            } else {
                throw new HttpApiV2Exception('Address format record could not be found.', 404);
            }
        }
        
        $this->_writeResponse($addressFormats);
    }
}