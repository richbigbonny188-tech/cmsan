<?php
/* --------------------------------------------------------------
   SpecialOffersApiV2Controller.inc.php 2020-04-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SpecialOffersApiV2Controller
 */
class SpecialOffersApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var SpecialOfferWriteServiceInterface
     */
    protected $writeService;
    
    /**
     * @var SpecialOfferReadServiceInterface
     */
    protected $readService;
    
    /**
     * @var SpecialOfferSerializer
     */
    protected $serializer;
    
    /**
     * @var SpecialOfferCollectionSerializer
     */
    protected $collectionSerializer;
    
    
    /**
     * Initializes the special offer api v2 controller.
     */
    protected function init()
    {
        $this->readService          = SpecialOffersServiceFactory::readService();
        $this->writeService         = SpecialOffersServiceFactory::writeService();
        $this->serializer           = new SpecialOfferSerializer();
        $this->collectionSerializer = new SpecialOfferCollectionSerializer($this->serializer);
        
        $this->_initializePagingAndSortingFields();
    }
    
    
    /**
     * Endpoint for REST GET-Request against /special_offers.
     */
    public function get()
    {
        if (array_key_exists(1, $this->uri)) {
            if (!is_numeric($this->uri[1])) {
                throw new HttpApiV2Exception('Special offer id must be an integer', 400);
            }
            
            $specialOfferId = SpecialOfferId::create($this->uri[1]);
            
            try {
                return $this->_writeResponse($this->serializer->serialize($this->readService->getById($specialOfferId)));
            } catch (SpecialOfferNotFoundException $e) {
                throw new HttpApiV2Exception($e->getMessage(), 404);
            }
        }
        
        try {
            $response = $this->collectionSerializer->serialize($this->readService->getAll($this->pager,
                                                                                          $this->sorters));
        } catch (SpecialOfferCollectionNotFoundException $e) {
            $response = [];
        }
        
        $this->_sortResponse($response);
        $this->_minimizeResponse($response);
        
        return $this->_writeResponse($response);
    }
    
    
    /**
     * Endpoint for REST POST-Request against /special_offers.
     */
    public function post()
    {
        if ($this->uri[1] ?? '' === 'search') {
            return $this->_search();
        }
        
        try {
            $specialOffer = $this->writeService->save($this->serializer->deserialize(json_encode($this->request->getParsedBody())));
            
            return $this->_writeResponse($this->serializer->serialize($specialOffer), 201);
        } catch (Exception $e) {
            return $this->_writeResponse($e->getTrace(), 400);
        }
    }
    
    
    /**
     * Endpoint for REST PUT-Request against /special_offers/:special_offer_id.
     *
     * @throws HttpApiV2Exception
     */
    public function put()
    {
        if (!array_key_exists(1, $this->uri)) {
            throw new HttpApiV2Exception('Special offer id must be provided!', 400);
        }
        
        if (!is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Special offer id must be an integer', 400);
        }
        
        try {
            $requestData = json_decode(json_encode($this->request->getParsedBody()), true);
            if (null === $requestData || $this->request->getParsedBody() === null) {
                throw new HttpApiV2Exception('Invalid request body provided!', 400);
            }
            
            $data         = array_merge(['id' => $this->uri[1]], $requestData);
            $specialOffer = $this->writeService->update($this->serializer->deserialize($data));
            
            return $this->_writeResponse($this->serializer->serialize($specialOffer));
        } catch (Exception $e) {
            return $this->_writeResponse($e->getTrace(), 400);
        }
    }
    
    
    /**
     * Endpoint for REST DELETE-Request against /special_offers/:special_offer_id.
     *
     * @throws HttpApiV2Exception
     * @throws EntityNotFoundException
     */
    public function delete()
    {
        if (!array_key_exists(1, $this->uri)) {
            throw new HttpApiV2Exception('Special offer id must be provided!', 400);
        }
        
        if (!is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Special offer id must be an integer', 400);
        }
        
        try {
            $specialOfferId = SpecialOfferId::create($this->uri[1]);
            $specialOffer   = $this->readService->getById($specialOfferId);
            $this->writeService->delete($specialOffer);
        } catch (EntityNotFoundException $e) {
        }
        
        $response = [
            'code'           => 200,
            'status'         => 'success',
            'action'         => 'delete',
            'resource'       => 'SpecialOffer',
            'specialOfferId' => (int)$this->uri[1]
        ];
        
        return $this->_writeResponse($response);
    }
    
    
    /**
     * Sub-Resource Special Offer Search
     *
     * This method will search all special offers with a with an given search condition as json.
     *
     * @see SpecialOffersApiV2Controller::post()
     */
    protected function _search()
    {
        $json            = json_encode($this->request->getParsedBody());
        $searchCondition = SpecialOfferSearchCondition::createByJson(new NonEmptyStringType($json));
        
        try {
            $response = $this->collectionSerializer->serialize($this->readService->getBy($searchCondition,
                                                                                         $this->pager,
                                                                                         $this->sorters));
        } catch (InvalidSearchConditionException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 400, $e);
        }
        
        $this->_sortResponse($response);
        $this->_minimizeResponse($response);
        
        return $this->_writeResponse($response);
    }
}