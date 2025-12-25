<?php
/* --------------------------------------------------------------
   ProductPricesApiV2Controller.inc.php 2019-11-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2019 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPricesApiV2Controller
 */
class ProductPricesApiV2Controller extends HttpApiV2Controller
{
    /**
     * @var ProductPriceReadService
     */
    protected $readService;
    
    /**
     * @var ProductPriceWriteService
     */
    protected $writeService;
    
    /**
     * @var ProductPriceSerializer
     */
    protected $serializer;
    
    
    /**
     * Initializes the special offer api v2 controller.
     */
    protected function __initialize()
    {
        $this->readService  = ProductPriceServiceFactory::readService();
        $this->writeService = ProductPriceServiceFactory::writeService();
        $this->serializer   = ProductPriceServiceFactory::serializer();
    }
    
    
    /**
     * Endpoint for REST GET-Request against
     * /product_prices/:product_id,
     * /product_prices/:product_id/customer_groups/:customer_group_id/:quantity,
     * /products/:product_id/product_prices,
     * /products/:product_id/product_prices/customer_groups/:customer_group_id/:quantity
     */
    public function get()
    {
        $this->_ensureProductIdExistsInPath();
        
        $productId = new IdType((int)$this->uri[1]);
        $uriOffset = 0;
        
        if (array_key_exists(0, $this->uri) && $this->uri[0] === 'products') {
            $uriOffset = 1;
        }
        
        if (array_key_exists(2 + $uriOffset, $this->uri) && $this->uri[2 + $uriOffset] === 'customer_groups') {
            $this->_getQuantityPrice($productId, new IntType($uriOffset));
            
            return;
        }
        
        try {
            $productPrices = $this->readService->getById($productId);
        } catch (EntityNotFoundException $e) {
            throw new HttpApiV2Exception('Can not find product by provided id "' . $productId . '".', 404, $e);
        }
        
        $response = $this->serializer->serialize($productPrices);
        
        $this->_linkResponse($response);
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * Endpoint for REST PUT-Request against /product_prices/:product_id.
     */
    public function put()
    {
        $this->_ensureProductIdExistsInPath();
        
        $productId   = new IdType($this->uri[1]);
        $requestBody = json_decode(json_encode($this->request->getParsedBody()), true);
        $requestBody = array_merge(['productId' => $productId->asInt()], $requestBody);
        
        try {
            $productPrice = $this->serializer->deserialize($requestBody);
            $productPrice = $this->writeService->save($productPrice, $productId);
            $this->_writeResponse($this->serializer->serialize($productPrice));
        } catch (EntityNotFoundException $e) {
            throw new HttpApiV2Exception('Product by provided id "' . $productId->asInt() . '" was not found!', 404);
        }
    }
    
    
    /**
     * Endpoint for REST PUT-Request against /product_prices/:product_id.
     */
    public function delete()
    {
        $this->_ensureProductIdExistsInPath();
        
        $productId = (int)$this->uri[1];
        
        try {
            $productPrice = $this->readService->getById(new IdType($productId));
            $this->writeService->delete($productPrice);
        } catch (EntityNotFoundException $e) {
        }
        
        $response = [
            'code'      => 200,
            'status'    => 'success',
            'action'    => 'delete',
            'resource'  => 'ProductPrices',
            'productId' => $productId
        ];
        $this->_writeResponse($response);
    }
    
    
    /**
     * Endpoint for REST GET-Request against /product_prices/:product_id/customer_groups/:customer_group_id/:quantity.
     *
     * @param IdType  $productId
     * @param IntType $uriOffset
     *
     * @throws HttpApiV2Exception
     * @throws ProductQuantityPriceNotFoundException
     */
    protected function _getQuantityPrice(IdType $productId, IntType $uriOffset)
    {
        // validate path arguments
        if (!array_key_exists(3 + $uriOffset->asInt(), $this->uri)) {
            throw new HttpApiV2Exception('Customer group id must be provided!', 400);
        }
        
        if (!is_numeric($this->uri[3 + $uriOffset->asInt()])) {
            throw new HttpApiV2Exception('Customer id must be an integer!', 400);
        }
        if (!array_key_exists(4 + $uriOffset->asInt(), $this->uri)) {
            throw new HttpApiV2Exception('Quantity must be provided!', 400);
        }
        
        if (!is_numeric($this->uri[4 + $uriOffset->asInt()])) {
            throw new HttpApiV2Exception('Quantity must be numeric', 400);
        }
        
        $customerGroupId = new IdType($this->uri[3 + $uriOffset->asInt()]);
        $quantity        = new DecimalType($this->uri[4 + $uriOffset->asInt()]);
        
        $graduatedPrice = $this->readService->getByQuantity($productId, $customerGroupId, $quantity);
        $serializer     = ProductPriceServiceFactory::createQuantityPriceSerializer();
        $response       = $serializer->serialize($graduatedPrice);
        
        $this->_linkResponse($response);
        $this->_writeResponse($response);
    }
    
    
    /**
     * Ensures that the product id is provided as query paramter.
     *
     * @throws HttpApiV2Exception
     */
    protected function _ensureProductIdExistsInPath()
    {
        if (!array_key_exists(1, $this->uri)) {
            throw new HttpApiV2Exception('Product id must be provided!', 400);
        }
        
        if (!is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Product id must be an integer!', 400);
        }
    }
}