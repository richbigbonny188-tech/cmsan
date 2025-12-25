<?php
/* --------------------------------------------------------------
   TaxZonesApiV2Controller.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class TaxZonesApiV2Controller
 *
 * Provides tax zone information.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class TaxZonesApiV2Controller extends HttpApiV2Controller
{
    /**
     * @api             {get} /tax_zones Get Tax Zones
     * @apiVersion      2.4.0
     * @apiName         GetTaxZones
     * @apiGroup        TaxRates
     *
     * @apiDescription
     * Get all tax zones or a single tax zone.
     *
     * @apiExample {curl} Get All Tax Zones
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/tax_zones
     *
     * @apiSuccessExample {json} Success-Response
     * [
     *   {
     *     "id": "5",
     *     "name": "Steuerzone EU",
     *     "description": "Steuerzone f\u00fcr die EU"
     *   },
     *   {
     *     "id": "6",
     *     "name": "Steuerzone EU-Ausland",
     *     "description": ""
     *   },
     *   {
     *     "id": "7",
     *     "name": "Steuerzone B2B",
     *     "description": ""
     *   },
     *   {
     *     "id": "8",
     *     "name": "Belgien",
     *     "description": ""
     *   },
     *   {
     *     "id": "9",
     *     "name": "Bulgarien",
     *     "description": ""
     *   },
     *   {
     *     "id": "10",
     *     "name": "D\u00e4nemark",
     *     "description": ""
     *   },
     *   {
     *     "id": "11",
     *     "name": "Deutschland",
     *     "description": ""
     *   },
     *   {
     *     "id": "12",
     *     "name": "\u00d6sterreich",
     *     "description": ""
     *   },
     *   {
     *     "id": "13",
     *     "name": "Estland",
     *     "description": ""
     *   },
     *   {
     *     "id": "14",
     *     "name": "Finnland",
     *     "description": ""
     *   },
     *   {
     *     "id": "15",
     *     "name": "Frankreich",
     *     "description": ""
     *   },
     *   {
     *     "id": "16",
     *     "name": "Griechenland",
     *     "description": ""
     *   },
     *   {
     *     "id": "17",
     *     "name": "Irland",
     *     "description": ""
     *   },
     *   {
     *     "id": "18",
     *     "name": "Italien",
     *     "description": ""
     *   },
     *   {
     *     "id": "19",
     *     "name": "Kroatien",
     *     "description": ""
     *   },
     *   {
     *     "id": "20",
     *     "name": "Lettland",
     *     "description": ""
     *   },
     *   {
     *     "id": "21",
     *     "name": "Litauen",
     *     "description": ""
     *   },
     *   {
     *     "id": "22",
     *     "name": "Luxemburg",
     *     "description": ""
     *   },
     *   {
     *     "id": "23",
     *     "name": "Malta",
     *     "description": ""
     *   },
     *   {
     *     "id": "24",
     *     "name": "Niederlande",
     *     "description": ""
     *   },
     *   {
     *     "id": "25",
     *     "name": "Polen",
     *     "description": ""
     *   },
     *   {
     *     "id": "26",
     *     "name": "Portugal",
     *     "description": ""
     *   },
     *   {
     *     "id": "27",
     *     "name": "Rum\u00e4nien",
     *     "description": ""
     *   },
     *   {
     *     "id": "28",
     *     "name": "Schweden",
     *     "description": ""
     *   },
     *   {
     *     "id": "29",
     *     "name": "Slowakei",
     *     "description": ""
     *   },
     *   {
     *     "id": "30",
     *     "name": "Slowenien",
     *     "description": ""
     *   },
     *   {
     *     "id": "31",
     *     "name": "Spanien",
     *     "description": ""
     *   },
     *   {
     *     "id": "32",
     *     "name": "Tschechien",
     *     "description": ""
     *   },
     *   {
     *     "id": "33",
     *     "name": "Ungarn",
     *     "description": ""
     *   },
     *   {
     *     "id": "34",
     *     "name": "Vereinigtes K\u00f6nigreich",
     *     "description": ""
     *   },
     *   {
     *     "id": "35",
     *     "name": "Zypern",
     *     "description": ""
     *   }
     * ]
     *
     * @apiExample {curl} Get Tax Class With ID = 5
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/tax_zones/5
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "id": "5",
     *   "name": "Steuerzone EU",
     *   "description": "Steuerzone f\u00fcr die EU"
     * }
     *
     * @apiError        404-NotFound Tax zone record could not be found.
     * @apiError        400-BadRequest Invalid rax class id provided (expected integer).
     *
     * @apiErrorExample Error-Response (Tax Class Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Tax zone record could not be found."
     * }
     *
     * @apiErrorExample Error-Response (Invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Provided tax zone ID is invalid, integer expected."
     * }
     */
    public function get()
    {
        if (!isset($this->uri[1])) {
            $db     = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $result = $db->select(['geo_zone_id', 'geo_zone_name', 'geo_zone_description'])
                ->order_by('geo_zone_id')
                ->get('geo_zones');
            
            $taxClasses = [];
            foreach ($result->result_array() as $taxClass) {
                $taxClasses[] = [
                    'id'          => $taxClass['geo_zone_id'],
                    'name'        => $taxClass['geo_zone_name'],
                    'description' => $taxClass['geo_zone_description']
                ];
            }
        } elseif (!is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Provided tax zone ID is invalid, integer expected: ' . gettype($this->uri[1]),
                                         400);
        } else {
            $db     = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $result = $db->select(['geo_zone_id', 'geo_zone_name', 'geo_zone_description'])
                ->where('geo_zone_id',
                        (int)$this->uri[1])
                ->get('geo_zones');
            
            if ($result->num_rows()) {
                $taxClass   = $result->row_array();
                $taxClasses = [
                    'id'          => $taxClass['geo_zone_id'],
                    'name'        => $taxClass['geo_zone_name'],
                    'description' => $taxClass['geo_zone_description']
                ];
            } else {
                throw new HttpApiV2Exception('Tax zone record could not be found.', 404);
            }
        }
        
        $this->_writeResponse($taxClasses);
    }
}