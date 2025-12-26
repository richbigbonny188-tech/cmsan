<?php

/* --------------------------------------------------------------
   OrderStatusesApiV2Controller.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class OrderStatusesApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var OrderStatusServiceInterface
     */
    protected $orderStatusService;
    
    
    /**
     * Initialize Controller
     */
    public function __initialize()
    {
        $this->orderStatusService = StaticGXCoreLoader::getService('OrderStatus');
    }
    
    
    /**
     * @api        {get} /order_statuses/:id Get order statuses
     * @apiVersion 2.4.0
     * @apiName    GetOrderStatus
     * @apiGroup   OrderStatus
     *
     * @apiDescription
     * Get a single order status entry. This method is currently limited to
     * only fetching a single order status resource so make sure that you provide the order status ID in
     * the request URI.
     *
     * @apiExample {curl} Get all order statuses
     *             curl --user admin@example.org:12345 https://example.org/api.php/v2/order_statuses
     *
     * @apiExample {curl} Get order status with ID = 2
     *             curl --user admin@example.org:12345 https://example.org/api.php/v2/order_statuses/2
     *
     * @apiSuccess Response-Body If successful, this method will return the order status resource in JSON format.
     *
     * @apiError (Error 5xx) 500-InternalError If the record is not found or something else goes wrong the API will
     * return a 500 error status. Read the message for more info.
     *
     * @apiError (Error 4xx) 400-Bad Request If the ID is not numeric in the request URI then the API will return
     * a 400 error status because it cannot return the order status resource.
     */
    public function get()
    {
        // Get order status is not supported.
        if (isset($this->uri[1]) && !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Order status id is invalid or was not provided.', 400);
        } elseif (!isset($this->uri[1])) {
            $this->_writeResponse($this->_serializeOrderStatusCollection($this->orderStatusService->findAll()));
        } else {
            $this->_writeResponse($this->_serializeOrderStatus($this->orderStatusService->find(new IntType($this->uri[1]))));
        }
    }
    
    
    /**
     * @api        {post} /order_statuses Create Order Status
     * @apiVersion 2.4.0
     * @apiName    CreateOrderStatus
     * @apiGroup   OrderStatus
     *
     * @apiDescription
     * This method creates a new order status in the database.
     *
     * @apiExample {curl} Creates New Order Status
     *             curl -X POST --user admin@example.org:12345 https://example.org/api.php/v2/order_statuses
     *
     * @apiParamExample {json} Create new order status
     * {
     *   "names": {
     *     "EN": "new order status name",
     *     "DE": "neuer order status name"
     *   },
     *   "color": "adad1313"
     * }
     *
     * @apiParam {object} names Language related order status names. Provide the language id as object key.
     * @apiParam {string} color Label color of order status.
     *
     * @apiSuccess (Success 201) Response-Body If successful, this method returns the created order status resource in
     * the response body.
     *
     * @apiError   400-Bad Request The API will return this status code if the order status data was not provided.
     */
    public function post()
    {
        $orderStatusJson = json_encode($this->request->getParsedBody());
        
        if (empty($orderStatusJson) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Order status data were not provided.', 400);
        }
        
        $orderStatusData = json_decode($orderStatusJson, true);
        $orderStatus     = $this->_deserializeOrderStatus($orderStatusData);
        $orderStatusId   = $this->orderStatusService->create($orderStatus);
        $newOrderStatus  = $this->orderStatusService->get(new IntType($orderStatusId));
        
        $this->_writeResponse($this->_serializeOrderStatus($newOrderStatus), 201);
    }
    
    
    /**
     * @api        {put} /order_statuses/:id Update Order Status
     * @apiVersion 2.4.0
     * @apiName    UpdateOrderStatus
     * @apiGroup   OrderStatus
     *
     * @apiDescription
     * This method updates a order status in the database.
     *
     * @apiExample {curl} Update Order Status with ID = 2
     *             curl -X PUT --user admin@example.org:12345 https://example.org/api.php/v2/order_statuses/2
     *
     * @apiParamExample {json} Create new order status
     * {
     *   "names": {
     *     "EN": "updated order status name",
     *     "DE": "aktualisierter order status name"
     *   },
     *   "color": "adad1313"
     * }
     *
     * @apiParam {object} names Language related order status names. Provide the language id as object key.
     * @apiParam {string} color Label color of order status.
     *
     * @apiSuccess (Success 201) Response-Body If successful, this method returns the updated order status resource in
     * the response body.
     *
     * @apiError   400-Bad Request The API will return this status code if the order status data or order status id was
     * not provided.
     */
    public function put()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Order status id is invalid or was not provided.', 400);
        }
        $orderStatusJson = json_encode($this->request->getParsedBody());
        if (empty($orderStatusJson) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Order status data were not provided.', 400);
        }
        $orderStatusData = json_decode($orderStatusJson, true);
        $orderStatus     = $this->orderStatusService->get(new IntType($this->uri[1]));
        
        foreach ($orderStatusData['names'] as $languageCode => $name) {
            $orderStatus->setName(MainFactory::create('LanguageCode', new StringType($languageCode)),
                                  new StringType($name));
        }
        $orderStatus->setColor(new StringType($orderStatusData['color']));
        $this->orderStatusService->update($orderStatus);
        
        $this->_writeResponse($this->_serializeOrderStatus($orderStatus));
    }
    
    
    /**
     * @api        {delete} /order_statuses/:id Delete Order Status
     * @apiVersion 2.4.0
     * @apiName    DeleteOrderStatus
     * @apiGroup   OrderStatus
     *
     * @apiDescription
     * Removes a order status record from the system. This method will always return success.
     *
     * @apiExample {curl} Delete Order Status with ID = 2
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/order_statuses/2
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *   "code": 200,
     *   "status": "success",
     *   "action": "delete",
     *   "orderStatusId": 2
     * }
     */
    public function delete()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Order status id is invalid or was not provided.', 400);
        }
        $this->orderStatusService->remove(new IntType($this->uri[1]));
        
        // Return response JSON.
        $response = [
            'code'          => 200,
            'status'        => 'success',
            'action'        => 'delete',
            'orderStatusId' => (int)$this->uri[1]
        ];
        $this->_writeResponse($response);
    }
    
    
    /**
     * Serializes an order status collection.
     *
     * @param OrderStatusCollection $collection Collection of order statuses to be serialized.
     *
     * @return array Serialized order status collection data.
     */
    protected function _serializeOrderStatusCollection(OrderStatusCollection $collection)
    {
        $data = [];
        
        foreach ($collection->getArray() as $item) {
            $data[] = $this->_serializeOrderStatus($item);
        }
        
        return $data;
    }
    
    
    /**
     * Serializes an order status object.
     *
     * @param OrderStatus $orderStatus Order status object to be serialized.
     *
     * @return array Serialized order status data.
     */
    protected function _serializeOrderStatus(OrderStatus $orderStatus)
    {
        return [
            'id'    => $orderStatus->getId(),
            'names' => $orderStatus->getNames(),
            'color' => $orderStatus->getColor()
        ];
    }
    
    
    /**
     * Deserializes an order status object.
     *
     * @param array $data Order status object to be deserialized.
     *
     * @return OrderStatus Deserialized order status data.
     */
    protected function _deserializeOrderStatus(array $data)
    {
        $id          = array_key_exists('id', $data) ? $data['id'] : null;
        $orderStatus = new OrderStatus($id);
        
        foreach ($data['names'] as $languageCode => $name) {
            $orderStatus->setName(MainFactory::create('LanguageCode', new StringType($languageCode)),
                                  new StringType($name));
        }
        
        return $orderStatus->setColor(new StringType($data['color']));
    }
}