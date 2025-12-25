<?php
/* --------------------------------------------------------------
   TaxRatesApiV2Controller.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class TaxRatesApiV2Controller
 *
 * Provides tax rates information.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class TaxRatesApiV2Controller extends HttpApiV2Controller
{
    /**
     * @api             {get} /tax_rates Get Tax Rates
     * @apiVersion      2.4.0
     * @apiName         GetTaxRates
     * @apiGroup        TaxRates
     *
     * @apiDescription
     * Get all tax rates or a single tax rate.
     *
     * @apiExample {curl} Get All Tax Rates
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/tax_rates
     *
     * @apiSuccessExample {json} Success-Response
     * [
     *   {
     *     "id": "1",
     *     "taxZoneId": "5",
     *     "taxClassId": "1",
     *     "taxRate": "19.0000",
     *     "description": "19% MwSt."
     *   },
     *   {
     *     "id": "2",
     *     "taxZoneId": "5",
     *     "taxClassId": "2",
     *     "taxRate": "7.0000",
     *     "description": "7% MwSt."
     *   },
     *   {
     *     "id": "3",
     *     "taxZoneId": "6",
     *     "taxClassId": "1",
     *     "taxRate": "0.0000",
     *     "description": "EU-AUS-UST 0%"
     *   },
     *   {
     *     "id": "4",
     *     "taxZoneId": "6",
     *     "taxClassId": "2",
     *     "taxRate": "0.0000",
     *     "description": "EU-AUS-UST 0%"
     *   },
     *   {
     *     "id": "5",
     *     "taxZoneId": "6",
     *     "taxClassId": "3",
     *     "taxRate": "0.0000",
     *     "description": "EU-AUS-UST 0%"
     *   },
     *   {
     *     "id": "6",
     *     "taxZoneId": "11",
     *     "taxClassId": "3",
     *     "taxRate": "19.0000",
     *     "description": "19% MwSt. (Deutschland)"
     *   },
     *   {
     *     "id": "7",
     *     "taxZoneId": "12",
     *     "taxClassId": "3",
     *     "taxRate": "20.0000",
     *     "description": "20% MwSt. (\u00d6sterreich)"
     *   }
     * ]
     *
     * @apiExample {curl} Get Tax Rate With ID = 1
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/tax_rates/1
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "id": "1",
     *   "taxZoneId": "5",
     *   "taxClassId": "1",
     *   "taxRate": "19.0000",
     *   "description": "19% MwSt."
     * }
     *
     * @apiError        404-NotFound Tax rate record could not be found.
     * @apiError        400-BadRequest Invalid rax rate id provided (expected integer).
     *
     * @apiErrorExample Error-Response (Tax Rate Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Tax rate record could not be found."
     * }
     *
     * @apiErrorExample Error-Response (Invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Provided tax rate ID is invalid, integer expected."
     * }
     */
    public function get()
    {
        if (!isset($this->uri[1])) {
            $taxRates = self::getTaxRates();
        } elseif (!is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Provided tax rate ID is invalid, integer expected: ' . gettype($this->uri[1]),
                                         400);
        } else {
            $db     = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $result = $db->select(['tax_rates_id', 'tax_zone_id', 'tax_class_id', 'tax_rate', 'tax_description'])
                ->where('tax_rates_id', (int)$this->uri[1])
                ->get('tax_rates');
            
            if ($result->num_rows()) {
                $taxRate  = $result->row_array();
                $taxRates = [
                    'id'          => $taxRate['tax_rates_id'],
                    'taxZoneId'   => $taxRate['tax_zone_id'],
                    'taxClassId'  => $taxRate['tax_class_id'],
                    'taxRate'     => $taxRate['tax_rate'],
                    'description' => $taxRate['tax_description']
                ];
            } else {
                throw new HttpApiV2Exception('Tax rate record could not be found.', 404);
            }
        }
        
        $this->_linkResponse($taxRates);
        $this->_writeResponse($taxRates);
    }
    
    
    /**
     * Get tax rates
     *
     * @return array
     */
    public static function getTaxRates()
    {
        $db     = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $result = $db->select(['tax_rates_id', 'tax_zone_id', 'tax_class_id', 'tax_rate', 'tax_description'])
            ->order_by('tax_rates_id')
            ->get('tax_rates');
        
        $taxRates = [];
        foreach ($result->result_array() as $taxRate) {
            $taxRates[] = [
                'id'          => $taxRate['tax_rates_id'],
                'taxZoneId'   => $taxRate['tax_zone_id'],
                'taxClassId'  => $taxRate['tax_class_id'],
                'taxRate'     => $taxRate['tax_rate'],
                'description' => $taxRate['tax_description']
            ];
        }
        
        return $taxRates;
    }
}