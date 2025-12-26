<?php
/* --------------------------------------------------------------
   ProductsApiV2Controller.inc.php 2023-03-06
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

use Gambio\MainComponents\Services\Core\AdditionalProductFields\Services\AdditionalProductFieldReadService as AdditionalProductFieldReadServiceInterface;

/**
 * Class ProductsApiV2Controller
 *
 * Provides a gateway to the ProductWriteService and ProductReadService classes, which handle the shop
 * product resources.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class ProductsApiV2Controller extends HttpApiV2Controller
{
    /**
     * Product write service.
     *
     * @var ProductWriteService
     */
    protected $productWriteService;
    
    /**
     * Product read service.
     *
     * @var ProductReadService
     */
    protected $productReadService;
    
    /**
     * Product JSON serializer.
     *
     * @var ProductJsonSerializer
     */
    protected $productJsonSerializer;
    
    /**
     * Product list item JSON serializer.
     *
     * @var ProductListItemJsonSerializer
     */
    protected $productListItemJsonSerializer;
    
    /**
     * Sub resources.
     *
     * @var array $subresource
     */
    protected $subresource;
    
    
    /**
     * Language of the current request.
     *
     * @var LanguageCode languageCode
     */
    protected $languageCode;
    
    /**
     * @var AdditionalProductFieldReadServiceInterface
     */
    protected $productFieldsReadService;
    
    
    /**
     * Initializes API Controller
     */
    protected function init()
    {
        $this->productWriteService           = StaticGXCoreLoader::getService('ProductWrite');
        $this->productReadService            = StaticGXCoreLoader::getService('ProductRead');
        $this->productFieldsReadService      = StaticGXCoreLoader::getService('AdditionalProductFieldRead');
        $this->productJsonSerializer         = MainFactory::create('ProductJsonSerializer');
        $this->productListItemJsonSerializer = MainFactory::create('ProductListItemJsonSerializer');
        $this->subresource                   = [
            'links'             => 'ProductsLinksApiV2Controller',
            'reviews'           => 'ProductReviewsApiV2Controller',
            'reviews_avg'       => 'ProductReviewsApiV2Controller',
            'product_prices'    => 'ProductPricesApiV2Controller',
            'additional_fields' => 'AdditionalProductFieldsApiV2Controller',
        ];
        
        $langParameter      = ($this->request->getQueryParam('lang')
                               !== null) ? $this->request->getQueryParam('lang') : 'de';
        $this->languageCode = new LanguageCode(new NonEmptyStringType($langParameter));
        
        $this->_initializePagingAndSortingFields();
    }
    
    
    /**
     * @api             {post} /products Create Product
     * @apiVersion      2.6.0
     * @apiName         CreateProduct
     * @apiGroup        Products
     *
     * @apiDescription
     * Creates a new product record or multiple new product records in the system. To see an example usage take a look
     * at
     * `docs/REST/samples/product-service/create_product.php`
     */
    public function post()
    {
        if (($this->uri[1] ?? '') === 'search') {
            return $this->_search();
        }
        
        if ($this->_mapResponse($this->subresource)) {
            return;
        }
        
        $productJsonString = json_encode($this->request->getParsedBody());
        
        if ($this->_isBulkRequest(new StringType($productJsonString))) // Create multiple new Products
        {
            $response = $this->_processBulkInsert(new StringType($productJsonString));
            $this->_linkResponse($response['created']);
        } else // Create new Product
        {
            $response = $this->_processSingleInsert(new StringType($productJsonString));
            $this->_linkResponse($response);
        }
        
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 201);
    }
    
    
    /**
     * @api             {put} /products/:id Update Product
     * @apiVersion      2.1.0
     * @apiName         ProductCategory
     * @apiGroup        Products
     *
     * @apiDescription
     * Use this method to update an existing product record. Take a look in the POST method for more detailed
     * explanation on every resource property. To see an example usage consider
     * `docs/REST/samples/product-service/update_product.php`
     */
    public function put()
    {
        if ($this->_mapResponse($this->subresource)) {
            return;
        }
        
        $productJsonString = json_encode($this->request->getParsedBody());
        
        if (empty($productJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Product data were not provided.', 400);
        }
        
        if ($this->_isBulkRequest(new StringType($productJsonString))) {
            $response = $this->_processBulkUpdate(new StringType($productJsonString));
            $this->_linkResponse($response['affected']);
        } elseif (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            $response = $this->_processSingleUpdate(new StringType($productJsonString), new IdType($this->uri[1]));
            $this->_linkResponse($response);
        } else {
            throw new HttpApiV2Exception('Product record ID was not provided or is invalid: ' . gettype($this->uri[1]),
                                         400);
        }
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 200);
    }
    
    
    /**
     * @api             {delete} /products/:id Delete Product
     * @apiVersion      2.1.0
     * @apiName         DeleteProduct
     * @apiGroup        Products
     *
     * @apiDescription
     * Removes a product record from the database. To see an example usage take a look at
     * `docs/REST/samples/product-service/remove_product.php`
     */
    public function delete()
    {
        if ($this->_mapResponse($this->subresource)) {
            return;
        }
        
        // Check if record ID was provided.
        if (!isset($this->uri[1])) {
            throw new HttpApiV2Exception('Product record ID was not provided in the resource URL.', 400);
        }
        
        if ($this->_isBulkDeleteRequest()) {
            $response = $this->_processBulkDelete(explode(',', $this->uri[1]));
        } elseif (is_numeric($this->uri[1])) {
            $response = $this->_processSingleDelete(new IdType($this->uri[1]));
        } else {
            throw new InvalidArgumentException('Product record ID is invalid.', 400);
        }
        
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 200);
    }
    
    
    protected function _isSeachByHistory()
    {
        return $this->request->getQueryParam('changed') !== null
               || $this->request->getQueryParam('modified') !== null
               || $this->request->getQueryParam('deleted') !== null;
    }
    
    
    /**
     * @api             {get} /products/:id Get Products
     * @apiVersion      2.6.0
     * @apiName         GetProduct
     * @apiGroup        Products
     *
     * @apiDescription
     * Get multiple or a single product records through a GET request. This method supports all the GET parameters
     * that are mentioned in the "Introduction" section of this documentation. To see an example usage take a look at
     * `docs/REST/samples/product-service/remove_product.php`
     */
    public function get()
    {
        //if its is requesting information from mapped controllers then return
        if ($this->_mapResponse($this->subresource)) {
            return;
        }
        
        //Search products by history (modification and exclusion)
        if ($this->_isSeachByHistory()) {
            
            $this->_searchByHistory();
            
            return;
        }
        
        $products   = $this->productReadService->getPagedList($this->languageCode, $this->pager, $this->sorters)
            ->getArray();
        $totalCount = $this->productReadService->getProductsCount();
        
        $response = [];
        
        foreach ($products as $product) {
    
            $productData = $this->productListItemJsonSerializer->serialize($product, false);
            $response[]  = $this->addAdditionalFieldsData($productData);
        }
    
        $searchTerm = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
        
        //TODO: This filter must be refactored to filter on database and not at the array
        if ($searchTerm !== null) {
            $this->_searchResponse($response, $searchTerm);
        }
        
        //call this method just to ad pagination headers to the result
        $this->_setPaginationHeaderByPage($this->pager, $totalCount);
        //remove unasked fields
        $this->_minimizeResponse($response);
        //link default related information
        $this->_linkResponse($response);
        //output the answer
        $this->_writeResponse($response);
    }
    
    
    /**
     * Get products by its categories search by categories
     *
     * This method will search all products with a with an given category.
     *
     * @param $category_id the category ID to be searched
     *
     * @throws HttpApiV2Exception if something gets wrong while searching the database
     *
     */
    public function getByCategories($category_id = null)
    {
        if ($category_id === null) {
            $category_id = (int)$this->uri[1];
        }
        
        $condition = ['search' => ['match' => ['products_to_categories.categories_id' => (int)$category_id]]];
        if ($this->request->getQueryParam('recursive') !== null) {
            /* @var CategoryReadService $categoryReadService */
            $categoryReadService = StaticGXCoreLoader::getService('CategoryRead');
            $categories          = $categoryReadService->getCategoryIdsTree(new IdType((int)$this->uri[1]));
            $categories          = $categories->getArray();
            $condition           = ['search' => ['in' => ['products_to_categories.categories_id' => $categories]]];
        }
        $this->_search(ProductSearchCondition::createByArray($condition));
    }
    
    
    /**
     * Get product using it's ID
     *
     * @param int $id product ID to be searched
     *
     * @throws HttpApiV2Exception if the product does not exists
     */
    public function getBy($id = null)
    {
        if (!is_numeric($id)) // Get Single Record
        {
            throw new HttpApiV2Exception('Product record ID was not provided or is invalid: ' . gettype($this->uri[1]),
                                         400);
        }
        
        try {
            $product = $this->productReadService->getProductById(new IdType((int)$this->uri[1]));
        } catch (UnexpectedValueException $e) {
            throw new HttpApiV2Exception('Product does not exist.', 404);
        }
        
        $response[] = $this->addAdditionalFieldsData($this->productJsonSerializer->serialize($product, false));
        
        $searchTerm = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
        
        if ($searchTerm !== null) {
            $this->_searchResponse($response, $searchTerm);
        }
        
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        $this->_writeResponse($response[0]);
    }
    
    
    /**
     * Sub-Resource Products Search
     *
     * This method will search all products with a with an given search condition.
     *
     * @param $searchCondition
     *
     * @throws HttpApiV2Exception
     *
     * @see ProductsApiV2Controller::post()
     */
    protected function _search($searchCondition = null)
    {
        $json = json_encode($this->request->getParsedBody());
        if ($searchCondition === null) {
            $searchCondition = ProductSearchCondition::createByJson(new NonEmptyStringType($json));
        }
        
        try {
            $products   = $this->productReadService->searchProducts($this->languageCode,
                                                                    $searchCondition,
                                                                    $this->pager,
                                                                    $this->sorters)->getArray();
            $totalCount = $this->productReadService->searchProductsCount($this->languageCode, $searchCondition);
        } catch (Exception $e) {
            throw new HttpApiV2Exception($e->getMessage(), 400, $e);
        }
        
        $response = [];
        
        foreach ($products as $product) {
            $response[] = $this->productListItemJsonSerializer->serialize($product, false);
        }
        
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        $this->_setPaginationHeaderByPage($this->pager, $totalCount->asInt());
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * Search products by its changing history and the modified, changed and deleted query parameters.
     *
     */
    protected function _searchByHistory()
    {
        $response             = [];
        $response['deleted']  = $this->_searchDeletedProducts();
        $response['modified'] = $this->_searchModifiedProducts();
        $this->_writeResponse($response);
    }
    
    
    protected function _searchModifiedProducts()
    {
        $result = [];
        
        $modificationDate = $this->_getAndValidateDateParam(new StringType('modified'));
        
        if ($modificationDate !== null) {
            $searchCondition = ProductSearchCondition::createByArray(['search' => ['geq' => ['products.products_last_modified' => $modificationDate]]]);
            $products        = $this->productReadService->searchProducts($this->languageCode,
                                                                         $searchCondition,
                                                                         $this->pager,
                                                                         $this->sorters);
            $totalCount      = $this->productReadService->searchProductsCount($this->languageCode, $searchCondition);
            foreach ($products as $order) {
                $serialized = $this->productListItemJsonSerializer->serialize($order, false);
                $result[]   = $serialized;
            }
            $this->_linkResponse($result);
            $this->_setPaginationHeaderByPage($this->pager, $totalCount->asInt());
        }
        
        return $result;
    }
    
    
    protected function _searchDeletedProducts()
    {
        $result = [];
        
        $deletedDate = $this->_getAndValidateDateParam(new StringType('deleted'));
        
        if ($deletedDate !== null) {
            $deleteHistoryReadService = DeleteHistoryServiceFactory::readService();
            $dateRange                = DateRange::create(new DateTime($deletedDate), new DateTime('now'));
            $deletedProducts          = $deleteHistoryReadService->findDeleted($dateRange,
                                                                               DeleteHistoryScope::products());
            /** @var DeleteHistoryReportItem $deletedProduct */
            foreach ($deletedProducts as $deletedProduct) {
                $result[] = [
                    'id'   => $deletedProduct->deletedId(),
                    'date' => $deletedProduct->deletedAt()->format('Y-m-d H:i:s'),
                ];
            }
        }
        
        return $result;
    }
    
    
    /**
     * Read the date param and validate it's content.
     *
     * @param StringType $paramName the date param to be readed
     *
     * @return bool
     * @throws HttpApiV2Exception
     *
     */
    protected function _getAndValidateDateParam(StringType $paramName)
    {
        if ($this->request->getQueryParam('changed') !== null) {
            $paramName = new StringType('changed');
        }
        
        $dateValue = $this->request->getQueryParam($paramName->asString());
        
        // Check format of modified and deleted date
        if ($dateValue !== null && !preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $dateValue)) {
            throw new HttpApiV2Exception('Given ' . $paramName->asString()
                                         . ' date is invalid. Expected format: 2018-09-25 15:59:01', 400);
        }
        
        return $dateValue;
    }
    
    
    /**
     * Checks if the performed request was a bulk request (only for POST and PUT requests).
     *
     * @param StringType $jsonString The complete request body as JSON string
     *
     * @return bool
     */
    protected function _isBulkRequest(StringType $jsonString)
    {
        $json = json_decode($jsonString->asString());
        
        if ($json === null && json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: '
                                               . $jsonString->asString());
        }
        
        return is_array($json) && array_keys($json) === range(0, count($json) - 1);
    }
    
    
    /**
     * Checks if the performed request was a bulk request (only for DELETE requests).
     *
     * @return bool
     */
    protected function _isBulkDeleteRequest()
    {
        return strpos($this->uri[1], ',') !== false;
    }
    
    
    /**
     * Performs the creation of multiple products from the JSON request body and creates a response array.
     *
     * @param StringType $jsonString The complete request body as JSON string
     *
     * @return array The response body
     */
    protected function _processBulkInsert(StringType $jsonString)
    {
        $products       = $this->productJsonSerializer->decodeJson($jsonString->asString());
        $storedProducts = [];
        $errors         = [];
        
        foreach ($products as $product) {
            try {
                // if there is no "mainCategoryId", the TOP category ID is set by default
                if (!isset($product->mainCategoryId)) {
                    $product->mainCategoryId = 0;
                }
                
                $productId        = $this->productWriteService->createProduct($this->productJsonSerializer->deserialize($product));
                $storedProducts[] = $this->productReadService->getProductById(new IdType($productId));
            } catch (Exception $exception) {
                $errors[] = $this->_getJsonErrorMessage($exception);
            }
        }
        
        return [
            'created' => $this->productJsonSerializer->serializeProductArray($storedProducts, false),
            'errors'  => $errors
        ];
    }
    
    
    /**
     * Performs the creation of a single product from the JSON request body and creates a response array.
     *
     * @param StringType $jsonString The complete request body as JSON string
     *
     * @return array The response body
     */
    protected function _processSingleInsert(StringType $jsonString)
    {
        try {
            if (isset($this->uri[1]) && is_numeric($this->uri[1])) // Duplicate Product
            {
                $productJsonObject = json_decode($jsonString->asString());
                
                if ($productJsonObject->categoryId === null || !is_numeric($productJsonObject->categoryId)) {
                    $productJsonObject = new stdClass;
                    
                    $productJsonObject->categoryId = 0; // Default category value.
                }
    
                // if there is no "mainCategoryId", the TOP category ID is set by default
                if (!isset($productJsonObject->mainCategoryId)) {
                    $productJsonObject->mainCategoryId = $productJsonObject->categoryId;
                }
                
                $productId = $this->productWriteService->duplicateProduct(new IdType($this->uri[1]),
                                                                          new IdType($productJsonObject->categoryId));
            } else // Create new Product
            {
                $decodedProduct = $this->productJsonSerializer->decodeJson($jsonString->asString());
    
                // if there is no "mainCategoryId", the TOP category ID is set by default
                if (!isset($decodedProduct->mainCategoryId)) {
                    $decodedProduct->mainCategoryId = 0;
                }
                
                $product   = $this->productJsonSerializer->deserialize($decodedProduct);
                $productId = $this->productWriteService->createProduct($product);
            }
            
            $this->_locateResource('products', $productId);
            $storedProduct = $this->productReadService->getProductById(new IdType($productId));
            
            return $this->productJsonSerializer->serialize($storedProduct, false);
        } catch (Exception $exception) {
            return $this->_getJsonErrorMessage($exception);
        }
    }
    
    
    /**
     * Performs the update of multiple products from the JSON request body and creates a response array.
     *
     * @param StringType $jsonString The complete request body as JSON string
     *
     * @return array The response body
     */
    protected function _processBulkUpdate(StringType $jsonString)
    {
        $products       = $this->productJsonSerializer->decodeJson($jsonString->asString());
        $storedProducts = [];
        $errors         = [];
        
        foreach ($products as $product) {
            try {
                if (!isset($product->id) || !is_numeric($product->id)) {
                    throw new HttpApiV2Exception('Product record ID was not provided or is invalid: '
                                                 . gettype($product->id), 400);
                }
                
                $productId     = new IdType($product->id);
                $baseProduct   = $this->productReadService->getProductById($productId);
                $storedProduct = $this->productJsonSerializer->deserialize($product, $baseProduct);
                
                $this->productWriteService->updateProduct($storedProduct,$product);
                $storedProducts[] = $this->productReadService->getProductById($productId);
            } catch (Exception $exception) {
                $errors[] = $this->_getJsonErrorMessage($exception);
            }
        }
        
        return [
            'affected' => $this->productJsonSerializer->serializeProductArray($storedProducts, false),
            'errors'   => $errors
        ];
    }
    
    
    /**
     * Performs the update of a single product by a given ID from the JSON request body and creates a response array.
     *
     * @param StringType $jsonString The complete request body as JSON string
     * @param IdType     $productId  The ID of the updated product
     *
     * @return array The response body
     */
    protected function _processSingleUpdate(StringType $jsonString, IdType $productId)
    {
        // Ensure that the product has the correct product id of the request url
        $productJsonString = $this->_setJsonValue($jsonString->asString(), 'id', $productId->asInt());
        $rawProduct        = $this->productJsonSerializer->decodeJson($productJsonString);
        $product           = $this->productJsonSerializer->deserialize($rawProduct,
                                                                       $this->productReadService->getProductById($productId));
        
        $this->productWriteService->updateProduct($product,$rawProduct);
        
        return $this->productJsonSerializer->serialize($this->productReadService->getProductById($productId), false);
    }
    
    
    /**
     * Performs the removal of multiple products from a list of product IDs and creates a response array.
     *
     * @param array $ids The IDs of the products that should be removed
     *
     * @return array The response body
     */
    protected function _processBulkDelete(array $ids)
    {
        $deletedProducts = [];
        $errors          = [];
        foreach ($ids as $id) {
            try {
                $this->_deleteProduct(new IdType($id));
                $deletedProducts[] = $id;
            } catch (Exception $exception) {
                $errors[] = $this->_getJsonErrorMessage($exception);
            }
        }
        
        return [
            'deleted' => $deletedProducts,
            'errors'  => $errors
        ];
    }
    
    
    /**
     * Performs the removal of a single product by a given product ID and creates a response array.
     *
     * @param IdType $productId The ID of the product that should be deleted
     *
     * @return array The response body
     */
    protected function _processSingleDelete(IdType $productId)
    {
        try {
            $this->_deleteProduct(new IdType($this->uri[1]));
        } catch (Exception $exception) {
            return $this->_getJsonErrorMessage($exception);
        }
        
        // Return response JSON.
        return [
            'code'      => 200,
            'status'    => 'success',
            'action'    => 'delete',
            'resource'  => 'Product',
            'productId' => $productId->asInt()
        ];
    }
    
    
    /**
     * Builds a default response body from a given Exception.
     *
     * @param Exception $exception The Exception that is to be described
     *
     * @return array The response body
     */
    protected function _getJsonErrorMessage(Exception $exception)
    {
        return [
            'errorMessage' => $exception->getMessage(),
            'stacktrace'   => $exception->getTrace()
        ];
    }
    
    
    /**
     * Checks if the response of a bulk request contains any errors.
     *
     * @param $response The checked response
     *
     * @return bool
     */
    protected function _hasErrors($response)
    {
        return is_array($response) && array_key_exists('errors', $response) && !empty($response['errors']);
    }
    
    
    /**
     * Deletes a product by its ID.
     *
     * @param IdType $id The ID of the product that is to be deleted
     */
    protected function _deleteProduct(IdType $id)
    {
        $this->productWriteService->deleteProductById($id);
    }
    
    
    /**
     * @param array $product
     *
     * @return array
     */
    protected function addAdditionalFieldsData(array $product): array
    {
        $productId               = $product['id'];
        $additionalProductFields = $this->productFieldsReadService->getAdditionalProductFields($productId)->toArray();
        $additionalFieldIds       = array_map(static function (array $additionalProductField): int {
        
            return $additionalProductField['id'];
        },
            $additionalProductFields);
        
        $product['additionalFields'] = $additionalFieldIds;
        
        return $product;
    }
}
