<?php
/* --------------------------------------------------------------
 VpeApiV2Controller.inc.php 2022-04-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2022 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Class VpeApiV2Controller
 *
 * @category System
 * @package  ApiV2Controllers
 */
class VpeApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var VPEReadService
     */
    protected $readService;
    
    /**
     * @var VPEWriteService
     */
    protected $writeService;
    
    
    /**
     * Initialize controller components.
     */
    protected function init()
    {
        $this->readService  = StaticGXCoreLoader::getService('VPERead');
        $this->writeService = StaticGXCoreLoader::getService('VPEWrite');
    }
    
    
    /**
     * @api             {get} /vpe/:id Get Customers
     * @apiVersion      2.5.0
     * @apiName         GetVPE
     * @apiGroup        VPE
     *
     * @apiDescription
     * Get multiple or a single vpe record through the GET method
     *
     * @apiExample {curl} Get All VPE records
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/vpe
     *
     * @apiExample {curl} Get VPE record With ID = 982
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/vpe/982
     *
     * @apiError        404-NotFound VPE record could not be found.
     *
     * @apiErrorExample Error-Response (VPE Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *   "code": 404,
     *   "status": "error",
     *   "message": "Vpe record could not be found."
     * }
     */
    public function get()
    {
        if (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            try {
                $vpe = $this->readService->getById(new IdType((int)$this->uri[1]));
            } catch (EntityNotFoundException $e) {
                throw new HttpApiV2Exception('VPE does not exist.', 404);
            }
            $response = $this->_serializeVpe($vpe);
            
            $this->_writeResponse($response);
            
            return;
        }
        
        $vpeCollection = $this->readService->getAll();
        $response      = $this->_serializeVpeCollection($vpeCollection);
        $searchTerm    = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
        
        if ($searchTerm !== null) {
            $this->_searchResponse($response, $searchTerm);
        }
        
        $this->_sortResponse($response);
        $this->_paginateResponse($response);
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * @api             {patch} /vpe/:id Updates VPE
     * @apiVersion      2.5.0
     * @apiName         UpdateVPE
     * @apiGroup        VPE
     *
     * @apiDescription
     * Use this method if you want to update an existing vpe record.
     *
     * @apiParamExample {json} VPE entity
     * {
     *      "name": {
     *          "EN": "API Packing unit",
     *          "DE": "API Verpackungseinheit"
     *      }
     * }
     *
     * @apiParam {Object} name Object that contains the language codes as key and the vpe name as value.
     *
     * @apiSuccess (200) Request-Body If successful, this method returns the complete vpe resource
     * in the response body.
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *      "id": 4,
     *      "name": {
     *          "EN": "API Packing unit",
     *          "DE": "API Verpackungseinheit"
     *      }
     * }
     *
     * @apiError        400-BadRequest VPE data were not provided or vpe record ID was not provided or is invalid.
     *
     * @apiErrorExample Error-Response (Empty request body)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "VPE data were not provided."
     * }
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "VPE record ID was not provided or is invalid."
     * }
     */
    public function patch()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('VPE record ID was not provided or is invalid in the Request-URI');
        }
        
        $jsonString = json_encode($this->request->getParsedBody());
        if (empty($jsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('VPE data were not provided.', 400);
        }
        
        $vpeData = json_decode($jsonString, true);
        $vpe     = $this->readService->getById(new IdType((int)$this->uri[1]));
        $this->_saveVpe($vpeData, $vpe);
        
        $this->_writeResponse($this->_serializeVpe($vpe));
    }
    
    
    /**
     * @api        {delete} /vpe/:id Delete VPE
     * @apiVersion 2.5.0
     * @apiName    DeleteVPE
     * @apiGroup   VPE
     *
     * @apiDescription
     * Removes a vpe record from the system. This method will always return success
     * even if the vpe does not exist (due to internal VPEWriteService architecture
     * decisions, which strive to avoid unnecessary failures).
     *
     * @apiExample {curl} Delete VPE with ID = 84
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/vpe/84
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "code": 200,
     *   "status": "success",
     *   "action": "delete",
     *   "vpeId": 84
     * }
     */
    public function delete()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('VPE record ID was not provided or is invalid in the Request-URI');
        }
        
        $vpe = $this->readService->getById(new IdType($this->uri[1]));
        $this->writeService->delete($vpe);
        
        $this->_writeResponse([
                                  'code'   => 200,
                                  'status' => 'success',
                                  'action' => 'delete',
                                  'vpeId'  => $vpe->getId(),
                              ]);
    }
    
    
    /**
     * @api             {post} /vpe Create VPE
     * @apiVersion      2.5.0
     * @apiName         CreateVPE
     * @apiGroup        VPE
     *
     * @apiDescription
     * This method enables the creation of a new VPE into the system.
     *
     * @apiParamExample {json} Request-Body
     * {
     *      "name": {
     *          "EN": "API Packing unit",
     *          "DE": "API Verpackungseinheit"
     *      }
     * }
     *
     * @apiParam {Object} name Object that contains the language codes as key and the vpe name as value.
     *
     * @apiSuccess (201) Request-Body If successful, this method returns the complete vpe resource
     * in the response body.
     *
     * @apiError        400-BadRequest The body of the request was empty.
     * @apiErrorExample Error-Response
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "VPE data were not provided."
     * }
     */
    public function post()
    {
        $jsonString = json_encode($this->request->getParsedBody());
        if (empty($jsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('VPE data were not provided.', 400);
        }
        $vpeData = json_decode($jsonString, true);
        $vpe     = $this->writeService->createVPE();
        $this->_saveVpe($vpeData, $vpe);
        
        $this->_writeResponse($this->_serializeVpe($vpe), 201);
    }
    
    
    /**
     * Saves the given vpe entity with the given data in the database.
     *
     * @param array        $vpeData VPE entity data to save.
     * @param VPEInterface $vpe     VPE entity to be saved.
     *
     * @return $this|VpeApiV2Controller Same instance for chained method calls.
     */
    protected function _saveVpe(array $vpeData, VPEInterface $vpe)
    {
        foreach ($vpeData['name'] ?? $vpeData['names'] ?? [] as $languageCode => $name) {
            $vpe->setName(new StringType($name), MainFactory::create('LanguageCode', new StringType($languageCode)));
        }
        if (array_key_exists('default', $vpeData)) {
            $vpe->setDefault(new BoolType($vpeData['default']));
        }
        $this->writeService->save($vpe);
        
        return $this;
    }
    
    
    /**
     * Serializes a vpe collection to an array.
     *
     * @param VPECollection $vpeCollection VPECollection to be serialized.
     *
     * @return array Serialized vpe collection.
     */
    protected function _serializeVpeCollection(VPECollection $vpeCollection)
    {
        $data = [];
        foreach ($vpeCollection->getArray() as $vpe) {
            $data[] = $this->_serializeVpe($vpe);
        }
        
        return $data;
    }
    
    
    /**
     * Serializes a single vpe entity to an array.
     *
     * @param VPEInterface $vpe VPE entity to be serialized.
     *
     * @return array Serialized vpe entity.
     */
    protected function _serializeVpe(VPEInterface $vpe)
    {
        return [
            'id'      => $vpe->getId(),
            'default' => $vpe->isDefault(),
            'names'   => $vpe->getNames(),
        ];
    }
}