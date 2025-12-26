<?php
/* --------------------------------------------------------------
   ParcelServicesApiV2Controller.inc.php 2021-07-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ParcelServicesApiV2Controller
 */
class ParcelServicesApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var ParcelServiceReadService
     */
    protected $readService;
    
    /**
     * @var ParcelServiceWriteService
     */
    protected $writeService;
    
    /**
     * @var ParcelServiceSerializer
     */
    protected $serializer;
    
    /**
     * @var ParcelServiceCollectionSerializer
     */
    protected $collectionSerializer;
    
    
    /**
     * Initializes the parcel services api v2 controller.
     */
    protected function init()
    {
        $this->readService          = ParcelServiceServiceFactory::readService();
        $this->writeService         = ParcelServiceServiceFactory::writeService();
        $this->serializer           = new ParcelServiceSerializer(MainFactory::create(LanguageProvider::class,
                                                                                      StaticGXCoreLoader::getDatabaseQueryBuilder()));
        $this->collectionSerializer = new ParcelServiceCollectionSerializer($this->serializer);
        
        $this->_initializePagingAndSortingFields();
    }
    
    
    /**
     * Endpoint for REST GET-Request against /parcel_service/:parcel_service_id.
     *
     * @throws HttpApiV2Exception
     */
    public function get()
    {
        $response = $this->singleResourceIsRequested() ? $this->getResponseForSingleResource() : $this->getResponseForCollection();
        
        $this->_minimizeResponse($response);
        $this->_writeResponse($response);
    }
    
    
    /**
     * Endpoint for REST POST-Request against /parcel_service[/search].
     *
     * @throws HttpApiV2Exception
     */
    public function post()
    {
        if (null === $json = json_decode(json_encode($this->request->getParsedBody()), true)) {
            throw new HttpApiV2Exception('Invalid request body provided!', 400);
        }
        
        if (($this->uri[1] ?? '') === 'search') {
            return $this->_search();
        }
        
        try {
            $parcelService = $this->writeService->save($this->serializer->deserialize($json));
        } catch (Exception $e) {
            return $this->_writeResponse($e->getTrace(), 400);
        }
        
        return $this->_writeResponse($this->serializer->serialize($parcelService), 201);
    }
    
    
    /**
     * Endpoint for REST PUT-Request against /parcel_service.
     *
     * @throws HttpApiV2Exception
     */
    public function put()
    {
        if (!array_key_exists(1, $this->uri) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Parcel service id has not been provided or is invalid!', 400);
        }
        
        if (null === $json = json_decode(json_encode($this->request->getParsedBody()), true)) {
            throw new HttpApiV2Exception('Invalid request body provided!', 400);
        }
        
        try {
            $updateData    = json_encode(array_merge(['id' => $this->uri[1]], $json));
            $parcelService = $this->writeService->update($this->serializer->deserialize($updateData));
        } catch (Exception $e) {
            return $this->_writeResponse($e->getTrace(), 400);
        }
        
        return $this->_writeResponse($this->serializer->serialize($parcelService));
    }
    
    
    /**
     * Endpoint for REST DELETE-Request against /parcel_service/:parcel_service_id.
     *
     * @throws HttpApiV2Exception
     */
    public function delete()
    {
        $parcelServiceId = ParcelServiceId::create(($this->uri[1] ?? null));
        
        if (!$parcelServiceId->id()) {
            throw new HttpApiV2Exception('Parcel service id must be provided!', 400);
        }
        
        try {
            $parcelService = $this->readService->getById($parcelServiceId);
            $this->writeService->delete($parcelService);
        } catch (ParcelServiceNotFoundException $e) {
        }
        
        $response = [
            'code'            => 200,
            'status'          => 'success',
            'action'          => 'delete',
            'resource'        => 'ParcelService',
            'parcelServiceId' => $parcelServiceId->id()
        ];
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * Sub-Resource Parcel Service Search
     *
     * This method will search all parcel services with a with an given search condition.
     *
     * @see ParcelServicesApiV2Controller::post()
     */
    protected function _search()
    {
        $json            = json_encode($this->request->getParsedBody());
        $searchCondition = ParcelServiceSearchCondition::createByJson(new NonEmptyStringType($json));
        
        try {
            $parcelServices = $this->readService->getBy($searchCondition, $this->pager, $this->sorters);
        } catch (InvalidSearchConditionException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 400, $e);
        }
        
        $response = [];
        foreach ($parcelServices as $parcelService) {
            $response[] = $this->serializer->serialize($parcelService);
        }
        
        $this->_minimizeResponse($response);
        
        return $this->_writeResponse($response);
    }
    
    
    /**
     * Checks if multiple or a single parcel service resource is requested.
     * The second uri part will be checked for existence.
     *
     * @return bool True if a single parcel service resource was requested.
     */
    protected function singleResourceIsRequested()
    {
        return isset($this->uri[1]);
    }
    
    
    /**
     * Checks if a parcel service id is in the uri.
     *
     * @return bool True if parcel service id is present in uri.
     */
    protected function parcelServiceIdExists()
    {
        if (function_exists('ctype_digit')) {
            return ctype_digit($this->uri[1]);
        }
        
        return preg_match('/^[0-9]*$/', $this->uri[1]) === 1;
    }
    
    
    /**
     * Returns a serialized parcel service entity.
     *
     * @return array Serialized parcel service entity.
     * @throws HttpApiV2Exception
     */
    protected function getResponseForSingleResource()
    {
        if (!$this->parcelServiceIdExists()) {
            throw new HttpApiV2Exception('Parcel service id must be numeric!', 400);
        }
        try {
            $id      = ParcelServiceId::create((int)$this->uri[1]);
            $service = $this->readService->getById($id);
            
            return $this->serializer->serialize($service);
        } catch (ParcelServiceNotFoundException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 404);
        }
    }
    
    
    /**
     * Returns a serialized parcel service collection.
     *
     * @return array Serialized parcel service collection.
     */
    protected function getResponseForCollection()
    {
        try {
            $parcelServices = $this->readService->getAll($this->pager, $this->sorters);
            $response       = $this->collectionSerializer->serialize($parcelServices);
        } catch (ParcelServiceCollectionNotFoundException $e) {
            $response = [];
        }
    
        $searchTerm = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
        if ($searchTerm !== null) {
            $this->_searchResponse($response, $searchTerm);
        }
        
        return $response;
    }
}