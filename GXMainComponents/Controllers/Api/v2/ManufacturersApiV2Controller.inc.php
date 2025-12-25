<?php
/* --------------------------------------------------------------
   ManufacturersApiV2Controller.inc.php 2019-11-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ManufacturersApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var ManufacturerReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var ManufacturerWriteServiceInterface
     */
    protected $writeService;
    
    
    /**
     * Initializes the manufacturers api controller.
     */
    protected function init()
    {
        $this->readService  = StaticGXCoreLoader::getService('ManufacturerRead');
        $this->writeService = StaticGXCoreLoader::getService('ManufacturerWrite');
    }
    
    
    /**
     * @api             {get} /manufacturers/:id Get manufacturers
     * @apiVersion      2.5.0
     * @apiName         GetManufacturers
     * @apiGroup        Manufacturers
     *
     * @apiDescription
     * Get multiple or a single manufacturers record through the GET method
     *
     * @apiExample {curl} Get All Manufacturers records
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/manufacturers
     *
     * @apiExample {curl} Get Manufacturers record With ID = 982
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/manufacturers/982
     *
     * @apiError        400-BadRequest Manufacturers data were not provided or manufacturers record ID was not provided
     *                  or is invalid.
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Manufacturers record ID was not provided or is invalid."
     * }
     *
     * @apiError        404-NotFound Manufacturers record could not be found.
     *
     * @apiErrorExample Error-Response (Manufacturers Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Manufacturers record could not be found."
     * }
     */
    public function get()
    {
        if (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            try {
                $manufacturer = $this->readService->getById(new IdType((int)$this->uri[1]));
                
                return $this->_writeResponse($this->_serializeManufacturer($manufacturer));
            } catch (EntityNotFoundException $e) {
                throw new HttpApiV2Exception($e->getMessage(), 404, $e);
            }
        }
        
        if (isset($this->uri[1]) && !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Manufacturer record is was not provided', 400);
        }
        
        return $this->_writeResponse($this->_serializeManufacturerCollection($this->readService->getAll()));
    }
    
    
    /**
     * @api             {post} /manufacturers Create Manufacturers
     * @apiVersion      2.6.0
     * @apiName         CreateManufacturers
     * @apiGroup        Manufacturers
     *
     * @apiDescription
     * This method enables the creation of a new Manufacturers into the system.
     *
     * @apiParamExample {json} Manufacturers entity
     * {
     *      "name": "Breitling",
     *      "image": "manufacturers/breitling-logo.png",
     *      "urls": {
     *          "EN": "https://breitling.com",
     *          "DE": "https://breitling.de"
     *      }
     * }
     *
     * @apiParam {String} name Name of manufacturer.
     * @apiParam {String} image Path to manufacturers image.
     * @apiParam {Object} urls Object with language code as key and the language specific url as value.
     *
     * @apiSuccess (201) Request-Body If successful, this method returns the complete manufacturers resource
     * in the response body.
     *
     * @apiSuccessExample {json} Success-Response
     *  {
     *      "id": 2,
     *      "name": "Breitling",
     *      "image": "manufacturers/breitling-logo.png",
     *      "dateAdded": "2017-09-29 13:42:46",
     *      "lastModified": "2017-09-29 13:42:46",
     *      "urls": {
     *          "EN": "https://breitling.com",
     *          "DE": "https://breitling.de"
     *      }
     *  }
     *
     * @apiError        400-BadRequest The body of the request was empty.
     * @apiErrorExample Error-Response
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Manufacturers data were not provided."
     * }
     */
    public function post()
    {
        if (($this->uri[1] ?? '') === 'search') {
            return $this->_search();
        }
        
        $manufacturer = $this->_deserializeManufacturer(json_encode($this->request->getParsedBody()));
        $this->writeService->save($manufacturer);
        
        $this->_writeResponse($this->_serializeManufacturer($manufacturer));
    }
    
    
    /**
     * @api             {patch} /manufacturers/:id Updates Manufacturers
     * @apiVersion      2.6.0
     * @apiName         UpdateManufacturers
     * @apiGroup        Manufacturers
     *
     * @apiDescription
     * Use this method if you want to update an existing manufacturers record.
     *
     * @apiParamExample {json} Manufacturers entity
     * {
     *      "name": "Breitling",
     *      "image": "manufacturers/breitling-logo.png",
     *      "urls": {
     *          "EN": "https://breitling.com",
     *          "DE": "https://breitling.de"
     *      }
     * }
     *
     * @apiParam {String} name Name of manufacturer.
     * @apiParam {String} image Path to manufacturers image.
     * @apiParam {Object} urls Object with language code as key and the language specific url as value.
     *
     * @apiSuccess (200) Request-Body If successful, this method returns the complete manufacturers resource
     * in the response body.
     *
     * @apiSuccessExample {json} Success-Response
     *  {
     *      "id": 2,
     *      "name": "Breitling",
     *      "image": "manufacturers/breitling-logo.png",
     *      "dateAdded": "2017-09-29 13:42:46",
     *      "lastModified": "2017-09-29 13:42:46",
     *      "urls": {
     *          "EN": "https://breitling.com",
     *          "DE": "https://breitling.de"
     *      }
     *  }
     *
     * @apiError        400-BadRequest Manufacturers data were not provided or manufacturers record ID was not provided
     *                  or is invalid.
     *
     * @apiErrorExample Error-Response (Empty request body)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Manufacturers data were not provided."
     * }
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Manufacturers record ID was not provided or is invalid."
     * }
     *
     * @apiError        404-NotFoundRequest Manufacturers data were not provided or manufacturers record ID was not
     *                  provided or is invalid.
     *
     * @apiErrorExample Error-Response (Entity not found)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Manufacturer entity was not found with provided id [ID]"
     * }
     */
    public function patch()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Manufacturer record ID was not provided or is invalid in the Request-URI');
        }
        
        $requestBody = json_encode($this->request->getParsedBody());
        if (empty($requestBody) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Manufacturer data were not provided.', 400);
        }
        
        try {
            $manufacturer = $this->_deserializeManufacturer($requestBody, $this->uri[1]);
            $this->writeService->save($manufacturer);
            $this->_writeResponse($this->_serializeManufacturer($manufacturer));
        } catch (EntityNotFoundException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 404, $e);
        }
    }
    
    
    /**
     * @api        {delete} /manufacturers/:id Delete Manufacturers
     * @apiVersion 2.5.0
     * @apiName    DeleteManufacturers
     * @apiGroup   Manufacturers
     *
     * @apiDescription
     * Removes a manufacturers record from the system. This method will always return success
     * even if the manufacturers does not exist (due to internal ManufacturersWriteService architecture
     * decisions, which strive to avoid unnecessary failures).
     *
     * @apiExample {curl} Delete Manufacturers with ID = 84
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/manufacturers/84
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "code": 200,
     *   "status": "success",
     *   "action": "delete",
     *   "manufacturersId": 84
     * }
     */
    public function delete()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Manufacturer record ID was not provided or is invalid in the Request-URI');
        }
        
        try {
            $manufacturer = $this->readService->getById(new IdType($this->uri[1]));
            $this->writeService->delete($manufacturer);
        } catch (EntityNotFoundException $e) {
        }
        
        $this->_writeResponse([
                                  'code'           => 200,
                                  'status'         => 'success',
                                  'action'         => 'delete',
                                  'manufacturerId' => $this->uri[1]
                              ]);
    }
    
    
    /**
     * Serializes manufacturer entities.
     *
     * @param ManufacturerInterface $manufacturer Manufacturer entity to be serialized.
     *
     * @return array Serialized manufacturer array.
     */
    protected function _serializeManufacturer(ManufacturerInterface $manufacturer)
    {
        return [
            'id'           => $manufacturer->getId(),
            'name'         => $manufacturer->getName(),
            'image'        => $manufacturer->getImage(),
            'dateAdded'    => $manufacturer->getDateAdded()->format('Y-m-d H:i:s'),
            'lastModified' => $manufacturer->getLastModified()->format('Y-m-d H:i:s'),
            'urls'         => $manufacturer->getUrls(),
        ];
    }
    
    
    /**
     * Deserialize manufacturer entities.
     *
     * @param string $manufacturerJson Manufacturer entity as json string.
     *
     * @return Manufacturer Deserialize manufacturer entity.
     */
    protected function _deserializeManufacturer($manufacturerJson, $id = null)
    {
        $manufacturerData = json_decode($manufacturerJson, true);
        $manufacturer     = $id ? $this->readService->getById(new IdType($id)) : $this->writeService->createManufacturer();
        
        $manufacturerData['image'] = $manufacturerData['image'] ? $manufacturerData['image'] : '';
        
        $manufacturer->setName(new StringType($manufacturerData['name']))
            ->setImage(new StringType($manufacturerData['image']));
        
        if (empty($manufacturerData['urls'])) {
            // Set empty values for urls if they were not provided, since otherwise an error will occur when fetching the entity
            $manufacturer->setUrl(new StringType(''), new LanguageCode(new StringType('de')));
            $manufacturer->setUrl(new StringType(''), new LanguageCode(new StringType('en')));
        } else {
            foreach ($manufacturerData['urls'] as $languageCode => $url) {
                $manufacturer->setUrl(new StringType($url), new LanguageCode(new StringType($languageCode)));
            }
        }
        
        return $manufacturer;
    }
    
    
    /**
     * Serializes manufacturer collections.
     *
     * @param ManufacturerCollection $manufacturerCollection Manufacturer collection to be serialized.
     *
     * @return array Serialized manufacturer collection array.
     */
    protected function _serializeManufacturerCollection(ManufacturerCollection $manufacturerCollection)
    {
        $data = [];
        foreach ($manufacturerCollection->getArray() as $manufacturer) {
            $data[] = $this->_serializeManufacturer($manufacturer);
        }
        
        return $data;
    }
    
    
    /**
     * Sub-Resource Manufacturers Search
     *
     * This method will search all manufactures with a given search condition.
     *
     * @see CategoriesApiV2Controller::post()
     */
    protected function _search()
    {
        $json            = json_encode($this->request->getParsedBody());
        $searchCondition = ManufacturerSearchCondition::createByJson(new NonEmptyStringType($json));
        
        try {
            return $this->_writeResponse($this->_serializeManufacturerCollection($this->readService->search($searchCondition)));
        } catch (Exception $e) {
            throw new HttpApiV2Exception($e->getMessage(), 400, $e);
        }
    }
}
