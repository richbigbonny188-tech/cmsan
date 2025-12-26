<?php

/* --------------------------------------------------------------
 TrackingCodesApiV2Controller.inc.php 2019-11-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2019 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

class TrackingCodesApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var ParcelTrackingCodeFactory
     */
    protected $factory;
    
    
    /**
     * Initializes API Controller
     */
    protected function init()
    {
        $this->factory = new ParcelTrackingCodeFactory();
        $this->_initializePagingAndSortingFields();
    }
    
    
    /**
     * @api        {get} /tracking_code Get parcel tracking codes
     * @apiVersion 2.6.0
     * @apiName    GetParcelTrackingCodes
     * @apiGroup   TrackingCode
     *
     * @apiDescription
     * Returns a list with all parcel tracking code entities. You can fetch a single resource by providing the tracking
     * code id in the request URI.
     *
     * @apiExample {curl} Get all parcel tracking codes
     *             curl --user admin@example.org:12345 https://example.org/api.php/v2/tracking_code
     *
     * @apiExample {curl} Get parcel tracking code with ID = 2
     *             curl --user admin@example.org:12345 https://example.org/api.php/v2/tracking_code/2
     *
     * @apiSuccess Response-Body If successful, this method will return the parcel tracking code resource in JSON
     *             format.
     *
     * @apiSuccessExample {json} Response-Body
     * [
     *  {
     *      "id": "1",
     *      "orderId": "400210",
     *      "trackingCode": "",
     *      "parcelServiceId": "0",
     *      "parcelServiceName": "Parcel Service",
     *      "languageId": "0",
     *      "url": "http://custom-url.com",
     *      "comment": "hello world",
     *      "creationDate": "2018-01-15 18:09:34"
     *  },
     *  {
     *      "id": "2",
     *      "orderId": "400211",
     *      "trackingCode": "",
     *      "parcelServiceId": "0",
     *      "parcelServiceName": "A Parcel Service",
     *      "languageId": "0",
     *      "url": "http://best-url.com",
     *      "comment": "Custom comment",
     *      "creationDate": "2018-01-15 18:09:52"
     *  }
     * ]
     *
     * @apiSuccessExample {json} Response-Body
     * {
     *      "id": "1",
     *      "orderId": "400210",
     *      "trackingCode": "",
     *      "parcelServiceId": "0",
     *      "parcelServiceName": "Parcel Service",
     *      "languageId": "0",
     *      "url": "http://custom-url.com",
     *      "comment": "hello world",
     *      "creationDate": "2018-01-15 18:09:34"
     *  }
     *
     * @apiError (Error 4xx) 404-NotFound If no resource could be found by the provided id.
     */
    public function get()
    {
        $finder = $this->factory->finder();
        
        if (!array_key_exists(1, $this->uri)) {
            $response = $finder->getAll($this->pager, $this->sorters);
            
            return $this->_writeResponse($this->_convertTrackingCodes($response));
        }
        
        if ($this->uri[0] === 'tracking_code' || $this->uri[0] === 'tracking_codes') {
            if (!is_numeric($this->uri[1]) || (int)$this->uri[1] != $this->uri[1]) {
                throw new HttpApiV2Exception('Tracking code ID is not an integer', 400);
            }
            
            $trackingCodeId = ParcelTrackingCodeId::create($this->uri[1]);
            $trackingCode   = $finder->find($trackingCodeId);
            
            if (!count($trackingCode)) {
                throw new HttpApiV2Exception('No tracking code found for ID ' . $this->uri[1], 404);
            }
            
            $this->_sortResponse($trackingCode);
            $this->_paginateResponse($trackingCode);
            
            return $this->_writeResponse($this->_convertTrackingCode($trackingCode));
        }
        
        $orderId       = ParcelTrackingCodeOrderId::create($this->uri[1]);
        $trackingCodes = $finder->findByOrderId($orderId);
        
        $this->_sortResponse($trackingCodes);
        $this->_paginateResponse($trackingCodes);
        
        return $this->_writeResponse($this->_convertTrackingCodes($trackingCodes));
    }
    
    
    /**
     * @api             {post} /orders/:order_id/tracking_code Add Tracking Code
     * @apiVersion      2.6.0
     * @apiName         AddTrackingCode
     * @apiGroup        Orders
     *
     * @apiDescription
     * Adds a new parcel tracking code to the order resource.
     *
     * @apiParamExample {json} Request-Body
     * {
     *      "parcelServiceId": 3,
     *      "trackingCode": "some-tracking-code"
     * }
     *
     * @apiParamExample {json} Request-Body
     * {
     *      "parcelServiceName": "My Custom Parcel Service",
     *      "url": "http://parcel-service-tracking-url.de?code=my-code"
     * }
     *
     * @apiParamExample {json} Request-Body
     * {
     *      "parcelServiceName": "My Custom Parcel Service",
     *      "url": "http://parcel-service-tracking-url.de?code=my-code",
     *      "comment": "This is a custom comment"
     * }
     *
     * @apiParam {int} parcelServiceId Id of parcel service, must be an existing parcel service ID.
     * @apiParam {String} trackingCode Parcel tracking code of order.
     * @apiParam {String} parcelServiceName Custom name of parcel service, used for lightweight entity.
     * @apiParam {String} url Parcel tracking url of order.
     * @apiParam {String} comment Optional comment for orders tracking code.
     *
     * @apiSuccess (Success 201) Response-Body If successful, this method returns a complete TrackingCode resource in
     *             the response body.
     *
     * @apiError        400-BadRequest The body of the request was empty or invalid.
     *
     * @apiErrorExample Error-Response
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Tracking code data were not provided."
     * }
     */
    public function post()
    {
        $requestBody = json_decode(json_encode($this->request->getParsedBody()), true);
        if (!$requestBody || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Invalid body provided.', 400);
        }
        try {
            $trackingCode   = $this->_deserialize($requestBody);
            $trackingCodeId = $trackingCode->save();
            
            $this->_writeResponse($this->_convertTrackingCode($this->factory->finder()->find($trackingCodeId)), 201);
        } catch (Exception $e) {
            throw new HttpApiV2Exception('Invalid body provided.', 400);
        }
    }
    
    
    /**
     * @api             {delete} /tracking_code/:id Delete tracking code
     * @apiVersion      2.6.0
     * @apiName         DeleteTrackingCode
     * @apiGroup        TrackingCode
     *
     * @apiDescription
     * Removes an order parcel tracking code entry from the database.
     *
     * @apiExample {curl} Delete Parcel Tracking Code
     *                    curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/tracking_code/4
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *      "code": 200,
     *      "status": "success",
     *      "action": "delete",
     *      "resource": "TrackingCode",
     *      "trackingCodeId": 4
     * }
     *
     * @apiError        400-BadRequest The provided tracking code id was invalid or not found.
     *
     * @apiErrorExample Error-Response
     * HTTP/1.1 400 Bad Request
     * {
     *      "code" 400,
     *      "status": "error",
     *      "message": Invalid tracking code id provided
     * }
     *
     */
    public function delete()
    {
        if (!array_key_exists(1, $this->uri)) {
            throw new HttpApiV2Exception('Invalid resource provided, tracking code id is missing in URI.', 400);
        }
        
        if (!is_numeric($this->uri[1]) || (int)$this->uri[1] != $this->uri[1]) {
            throw new HttpApiV2Exception('Invalid resource provided, tracking code id in URI is invalid.', 400);
        }
        
        $this->factory->deleteService()->delete(ParcelTrackingCodeId::create($this->uri[1]));
        
        $this->_writeResponse([
                                  'code'           => 200,
                                  'status'         => 'success',
                                  'action'         => 'delete',
                                  'resource'       => 'TrackingCode',
                                  'trackingCodeId' => $this->uri[1],
                              ]);
    }
    
    
    /**
     * @param array $orderParcelTrackingCodes
     *
     * @return array
     */
    protected function _convertTrackingCodes(array $orderParcelTrackingCodes)
    {
        $data = [];
        
        foreach ($orderParcelTrackingCodes as $orderParcelTrackingCode) {
            $data[] = $this->_convertTrackingCode($orderParcelTrackingCode);
        }
        
        return $data;
    }
    
    
    /**
     * @param array $orderParcelTrackingCode
     *
     * @return array
     */
    protected function _convertTrackingCode(array $orderParcelTrackingCode)
    {
        return [
            'id'                => $orderParcelTrackingCode['orders_parcel_tracking_code_id'],
            'orderId'           => $orderParcelTrackingCode['order_id'],
            'trackingCode'      => $orderParcelTrackingCode['tracking_code'],
            'parcelServiceId'   => $orderParcelTrackingCode['parcel_service_id'],
            'parcelServiceName' => $orderParcelTrackingCode['parcel_service_name'],
            'languageId'        => $orderParcelTrackingCode['language_id'],
            'url'               => $orderParcelTrackingCode['url'],
            'comment'           => $orderParcelTrackingCode['comment'],
            'creationDate'      => $orderParcelTrackingCode['creation_date'],
        ];
    }
    
    
    /**
     * @param array $json
     *
     * @return ActiveRecordParcelTrackingCode
     * @throws Exception
     */
    protected function _deserialize(array $json)
    {
        $this->ensureJsonIsValid($json);
        
        if (array_key_exists('parcelServiceId', $json)) {
            return $this->_deserializeEntity($json);
        }
        
        return $this->_deserializeLightweightEntity($json);
    }
    
    
    /**
     * @param array $json
     *
     * @throws Exception
     */
    protected function ensureJsonIsValid(array $json)
    {
        $error         = 'Invalid Request-Body for REST-Operation provided! ';
        $specificDummy = 'The key "%s" is set, so the other required key in the request body is "%s" and optionally "%s".';
        $generalDummy  = 'If the key "%s" exists in the request body, the other required key is "%s" and optionally "%s".';
        
        if (array_key_exists('parcelServiceId', $json)) {
            if (!array_key_exists('trackingCode', $json)) {
                $error .= sprintf($specificDummy, 'parcelServiceId', 'trackingCode', 'languageId');
                throw new InvalidArgumentException($error);
            }
            
            return;
        }
        
        if (array_key_exists('parcelServiceName', $json)) {
            if (!array_key_exists('url', $json)) {
                $error .= sprintf($specificDummy, 'parcelServiceName', 'url', 'comment');
                throw new InvalidArgumentException($error);
            }
            
            return;
        }
        
        $error .= sprintf($generalDummy, 'parcelServiceId', 'trackingCode', 'languageId') . ' ';
        $error .= sprintf($generalDummy, 'parcelServiceName', 'url', 'comment');
        
        throw new Exception($error);
    }
    
    
    /**
     * @param array $json
     *
     * @return ActiveRecordParcelTrackingCode
     */
    protected function _deserializeEntity(array $json)
    {
        $parcelServiceId = ParcelTrackingCodeServiceId::create($json['parcelServiceId']);
        $orderId         = ParcelTrackingCodeOrderId::create($this->uri[1]);
        $trackingCode    = OrderParcelTrackingCode::create($json['trackingCode']);
        $languageId      = $this->determineLanguageId($json);
        
        return $this->factory->create($parcelServiceId, $orderId, $trackingCode, $languageId);
    }
    
    
    /**
     * @param array $json
     *
     * @return ActiveRecordParcelTrackingCode
     */
    protected function _deserializeLightweightEntity(array $json)
    {
        $orderId           = ParcelTrackingCodeOrderId::create($this->uri[1]);
        $parcelServiceName = ParcelTrackingCodeServiceName::name($json['parcelServiceName']);
        $url               = ParcelTrackingCodeUrl::create($json['url']);
        $comment           = array_key_exists('comment',
                                              $json) ? ParcelTrackingCodeComment::write($json['comment']) : null;
        
        return $this->factory->createLightweight($orderId, $parcelServiceName, $url, $comment);
    }
    
    
    /**
     * @param array $json
     *
     * @return ParcelTrackingCodeLanguageId
     */
    protected function determineLanguageId(array $json)
    {
        if (array_key_exists('languageId', $json)) {
            return ParcelTrackingCodeLanguageId::create($json['languageId']);
        }
        
        $languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
        
        return ParcelTrackingCodeLanguageId::create($languageProvider->getDefaultLanguageId());
    }
}