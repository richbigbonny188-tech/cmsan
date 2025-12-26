<?php
/* --------------------------------------------------------------
   QuantityUnitsApiV2Controller.inc.php 2022-02-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class QuantityUnitsApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var QuantityUnitReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var QuantityUnitWriteServiceInterface
     */
    protected $writeService;
    
    
    /**
     * Initializes the quantity unit api controller.
     */
    protected function __initialize()
    {
        $this->readService  = StaticGXCoreLoader::getService('QuantityUnitRead');
        $this->writeService = StaticGXCoreLoader::getService('QuantityUnitWrite');
    }
    
    
    /**
     * @api             {get} /quantity_units/:id Get quantity units
     * @apiVersion      2.5.0
     * @apiName         GetQuantityUnits
     * @apiGroup        QuantityUnits
     *
     * @apiDescription
     * Get multiple or a single quantity units record through the GET method
     *
     * @apiExample {curl} Get All QuantityUnits records
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/quantity_units
     *
     * @apiExample {curl} Get QuantityUnits record With ID = 982
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/quantity_units/982
     *
     * @apiError        400-BadRequest QuantityUnits data were not provided or quantity units record ID was not
     *                  provided or is invalid.
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "QuantityUnits record ID was not provided or is invalid."
     * }
     *
     * @apiError        404-NotFound QuantityUnits record could not be found.
     *
     * @apiErrorExample Error-Response (QuantityUnits Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "QuantityUnits record could not be found."
     * }
     */
    public function get()
    {
        if (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            try {
                $quantityUnit = $this->readService->getById(new IdType((int)$this->uri[1]));
                
                return $this->_writeResponse($this->_serializeQuantityUnit($quantityUnit));
            } catch (EntityNotFoundException $e) {
                throw new HttpApiV2Exception($e->getMessage(), 404, $e);
            }
        }
        
        if (isset($this->uri[1]) && !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Quantity unit record is was not provided', 400);
        }
        
        $response   = $this->_serializeQuantityUnitCollection($this->readService->getAll());
        $searchTerm = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
        
        if ($searchTerm !== null) {
            $this->_searchResponse($response, $searchTerm);
        }
        
        $this->_sortResponse($response);
        $this->_paginateResponse($response);
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        
        return $this->_writeResponse($response);
    }
    
    
    /**
     * @api             {post} /quantity_units Create QuantityUnits
     * @apiVersion      2.5.0
     * @apiName         CreateQuantityUnits
     * @apiGroup        QuantityUnits
     *
     * @apiDescription
     * This method enables the creation of a new QuantityUnits into the system.
     *
     * @apiParamExample {json} QuantityUnits entity
     *  {
     *      "names": {
     *          "EN": "piece",
     *          "DE": "Stück"
     *      }
     *  }
     *
     * @apiParam {Object} names Object with language code as keys and name as values.
     *
     * @apiSuccess (201) Request-Body If successful, this method returns the complete quantity units resource
     * in the response body.
     *
     * @apiSuccessExample {json} Success-Response
     *  {
     *      "id": 2,
     *      "names": {
     *          "EN": "piece",
     *          "DE": "Stück"
     *      }
     *  }
     *
     * @apiError        400-BadRequest The body of the request was empty.
     * @apiErrorExample Error-Response
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "QuantityUnits data were not provided."
     * }
     */
    public function post()
    {
        $quantityUnit = $this->_deserializeQuantityUnit(json_encode($this->request->getParsedBody()));
        $this->writeService->save($quantityUnit);
        
        $this->_writeResponse($this->_serializeQuantityUnit($quantityUnit));
    }
    
    
    /**
     * @api             {patch} /quantity_units/:id Updates QuantityUnit entity
     * @apiVersion      2.5.0
     * @apiName         UpdateQuantityUnits
     * @apiGroup        QuantityUnits
     *
     * @apiDescription
     * Use this method if you want to update an existing quantity units record.
     *
     * @apiParamExample {json} QuantityUnits entity
     *  {
     *      "names": {
     *          "EN": "piece",
     *          "DE": "Stück"
     *      }
     *  }
     *
     * @apiParam {Object} names Object with language code as keys and name as values.
     *
     * @apiSuccess (200) Request-Body If successful, this method returns the complete quantity units resource
     * in the response body.
     *
     * @apiSuccessExample {json} Success-Response
     *  {
     *      "id": 2,
     *      "names": {
     *          "EN": "piece",
     *          "DE": "Stück"
     *      }
     *  }
     *
     * @apiError        400-BadRequest QuantityUnits data were not provided or quantityUnits record ID was not provided
     *                  or is invalid.
     *
     * @apiErrorExample Error-Response (Empty request body)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "QuantityUnits data were not provided."
     * }
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "QuantityUnits record ID was not provided or is invalid."
     * }
     *
     * @apiError        404-NotFoundRequest QuantityUnits data were not provided or quantity unit record ID was not
     *                  provided or is invalid.
     *
     * @apiErrorExample Error-Response (Entity not found)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Quantity unit entity was not found with provided id [ID]"
     * }
     */
    public function patch()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Quantity unit record ID was not provided or is invalid in the Request-URI');
        }
        
        $requestBody = json_encode($this->request->getParsedBody());
        if (empty($requestBody) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Quantity unit data were not provided.', 400);
        }
        
        try {
            $quantityUnit = $this->_deserializeQuantityUnit($requestBody, $this->uri[1]);
            $this->writeService->save($quantityUnit);
            $this->_writeResponse($this->_serializeQuantityUnit($quantityUnit));
        } catch (EntityNotFoundException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 404, $e);
        }
    }
    
    
    /**
     * @api        {delete} /quantity_units/:id Delete QuantityUnits
     * @apiVersion 2.5.0
     * @apiName    DeleteQuantityUnits
     * @apiGroup   QuantityUnits
     *
     * @apiDescription
     * Removes a quantity units record from the system. This method will always return success
     * even if the quantity unit does not exist.
     *
     * @apiExample {curl} Delete QuantityUnits with ID = 84
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/quantity_units/84
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "code": 200,
     *   "status": "success",
     *   "action": "delete",
     *   "quantityUnitId": 84
     * }
     */
    public function delete()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Quantity unit record ID was not provided or is invalid in the Request-URI');
        }
        
        $quantityUnit = $this->readService->getById(new IdType($this->uri[1]));
        $this->writeService->delete($quantityUnit);
        
        $this->_writeResponse([
                                  'code'           => 200,
                                  'status'         => 'success',
                                  'action'         => 'delete',
                                  'quantityUnitId' => $quantityUnit->getId(),
                              ]);
    }
    
    
    /**
     * Deserialize a json string to a quantity unit entity.
     *
     * @param string   $quantityUnitJson Quantity unit json to be serialized.
     * @param int|null $quantityUnitId   (Optional) Id of entity to be deserialize.
     *
     * @return QuantityUnitInterface Deserialize quantity unit entity.
     */
    protected function _deserializeQuantityUnit($quantityUnitJson, $quantityUnitId = null)
    {
        $quantityUnitData = json_decode($quantityUnitJson, true);
        
        $quantityUnit = $quantityUnitId ? $this->readService->getById(new IdType($quantityUnitId)) : $this->writeService->createQuantityUnit();
        
        foreach ($quantityUnitData['names'] as $languageCode => $name) {
            $quantityUnit->setName(new StringType($name), new LanguageCode(new StringType($languageCode)));
        }
        
        return $quantityUnit;
    }
    
    
    /**
     * Serializes a quantity unit entity collection.
     *
     * @param QuantityUnitCollection $quantityUnitCollection Collection to be serialized.
     *
     * @return array Serialized quantity unit collection array.
     */
    protected function _serializeQuantityUnitCollection(QuantityUnitCollection $quantityUnitCollection)
    {
        $data = [];
        foreach ($quantityUnitCollection->getArray() as $quantityUnit) {
            $data[] = $this->_serializeQuantityUnit($quantityUnit);
        }
        
        return $data;
    }
    
    
    /**
     * Serializes a quantity unit entity.
     *
     * @param QuantityUnitInterface $quantityUnit Quantity unit to be serialized.
     *
     * @return array Serialized quantity unit array.
     */
    protected function _serializeQuantityUnit(QuantityUnitInterface $quantityUnit)
    {
        return [
            'id'    => $quantityUnit->getId(),
            'names' => $quantityUnit->getNames(),
        ];
    }
}