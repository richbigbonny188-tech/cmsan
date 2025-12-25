<?php
/* --------------------------------------------------------------
   TaxClassesApiV2Controller.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class TaxClassesApiV2Controller
 *
 * Provides tax class information.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class TaxClassesApiV2Controller extends HttpApiV2Controller
{
    /**
     * @api             {get} /tax_classes Get Tax Classes
     * @apiVersion      2.4.0
     * @apiName         GetTaxClasses
     * @apiGroup        TaxClasses
     *
     * @apiDescription
     * Get all tax classes or a single tax class including tax rates.
     *
     * @apiExample {curl} Get All Tax Classes
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/tax_classes
     *
     * @apiSuccessExample {json} Success-Response
     * [
     *   {
     *     "id": "1",
     *     "title": "Standardsatz",
     *     "description": "",
     *     "taxRates": [
     *       {
     *         "id": "1",
     *         "taxZoneId": "5",
     *         "taxClassId": "1",
     *         "taxRate": "19.0000",
     *         "description": "19% MwSt."
     *       },
     *       {
     *         "id": "3",
     *         "taxZoneId": "6",
     *         "taxClassId": "1",
     *         "taxRate": "0.0000",
     *         "description": "EU-AUS-UST 0%"
     *       }
     *     ]
     *   },
     *   {
     *     "id": "2",
     *     "title": "erm\u00e4\u00dfigter Steuersatz",
     *     "description": "",
     *     "taxRates": [
     *       {
     *         "id": "2",
     *         "taxZoneId": "5",
     *         "taxClassId": "2",
     *         "taxRate": "7.0000",
     *         "description": "7% MwSt."
     *       },
     *       {
     *         "id": "4",
     *         "taxZoneId": "6",
     *         "taxClassId": "2",
     *         "taxRate": "0.0000",
     *         "description": "EU-AUS-UST 0%"
     *       }
     *     ]
     *   },
     *   {
     *     "id": "3",
     *     "title": "elektronisch erbrachte Leistung",
     *     "description": "EEL",
     *     "taxRates": [
     *       {
     *         "id": "5",
     *         "taxZoneId": "11",
     *         "taxClassId": "3",
     *         "taxRate": "19.0000",
     *         "description": "19% MwSt. (Deutschland)"
     *       },
     *       {
     *         "id": "6",
     *         "taxZoneId": "12",
     *         "taxClassId": "3",
     *         "taxRate": "20.0000",
     *         "description": "20% MwSt. (\u00d6sterreich)"
     *       },
     *       {
     *         "id": "7",
     *         "taxZoneId": "6",
     *         "taxClassId": "3",
     *         "taxRate": "0.0000",
     *         "description": "EU-AUS-UST 0%"
     *       }
     *     ]
     *   }
     * ]
     *
     * @apiExample {curl} Get Tax Class With ID = 1
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/tax_classes/1
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "id": "1",
     *   "title": "Standardsatz",
     *   "description": "",
     *   "taxRates": [
     *     {
     *       "id": "1",
     *       "taxZoneId": "5",
     *       "taxClassId": "1",
     *       "taxRate": "19.0000",
     *       "description": "19% MwSt."
     *     },
     *     {
     *       "id": "3",
     *       "taxZoneId": "6",
     *       "taxClassId": "1",
     *       "taxRate": "0.0000",
     *       "description": "EU-AUS-UST 0%"
     *     }
     *   ]
     * }
     *
     * @apiError        404-NotFound Tax class record could not be found.
     * @apiError        400-BadRequest Invalid rax class id provided (expected integer).
     *
     * @apiErrorExample Error-Response (Tax Class Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Tax class record could not be found."
     * }
     *
     * @apiErrorExample Error-Response (Invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Provided tax class ID is invalid, integer expected."
     * }
     */
    public function get()
    {
        if (!isset($this->uri[1])) {
            $allTaxRates = TaxRatesApiV2Controller::getTaxRates();
            
            $db     = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $result = $db->select(['tax_class_id', 'tax_class_title', 'tax_class_description'])
                ->order_by('tax_class_id')
                ->get('tax_class');
            
            $taxClasses = [];
            foreach ($result->result_array() as $taxClass) {
                $taxRates = [];
                
                foreach ($allTaxRates as $taxRate) {
                    if ($taxRate['taxClassId'] === $taxClass['tax_class_id']) {
                        $taxRates[] = $taxRate;
                    }
                }
                
                $this->_linkResponse($taxRates);
                
                $taxClasses[] = [
                    'id'          => $taxClass['tax_class_id'],
                    'title'       => $taxClass['tax_class_title'],
                    'description' => $taxClass['tax_class_description'],
                    'taxRates'    => $taxRates
                ];
            }
        } elseif (!is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Provided tax class ID is invalid, integer expected: '
                                         . gettype($this->uri[1]), 400);
        } else {
            $allTaxRates = TaxRatesApiV2Controller::getTaxRates();
            
            $db     = StaticGXCoreLoader::getDatabaseQueryBuilder();
            $result = $db->select(['tax_class_id', 'tax_class_title', 'tax_class_description'])
                ->where('tax_class_id',
                        (int)$this->uri[1])
                ->get('tax_class');
            
            if ($result->num_rows()) {
                $taxRates = [];
                $taxClass = $result->row_array();
                
                foreach ($allTaxRates as $taxRate) {
                    if ($taxRate['taxClassId'] === $taxClass['tax_class_id']) {
                        $taxRates[] = $taxRate;
                    }
                }
                
                $this->_linkResponse($taxRates);
                
                $taxClasses = [
                    'id'          => $taxClass['tax_class_id'],
                    'title'       => $taxClass['tax_class_title'],
                    'description' => $taxClass['tax_class_description'],
                    'taxRates'    => $taxRates
                ];
            } else {
                throw new HttpApiV2Exception('Tax class record could not be found.', 404);
            }
        }
        
        $this->_writeResponse($taxClasses);
    }
}