<?php
/* --------------------------------------------------------------
  CategoriesApiV2Controller.inc.php 2021-07-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class CategoriesApiV2Controller
 *
 * Provides a gateway to the CategoryWriteService and CategoryReadService classes, which handle the shop category
 * resources.
 *
 * @category   System
 * @package    ApiV2Controllers
 */
class CategoriesApiV2Controller extends HttpApiV2Controller
{
    /**
     * Category write service.
     *
     * @var CategoryWriteService
     */
    protected $categoryWriteService;
    
    /**
     * Category read service.
     *
     * @var CategoryReadService
     */
    protected $categoryReadService;
    
    /**
     * Category JSON serializer.
     *
     * @var CategoryJsonSerializer
     */
    protected $categoryJsonSerializer;
    
    /**
     * Category list item JSON serializer.
     *
     * @var CategoryListItemJsonSerializer
     */
    protected $categoryListItemJsonSerializer;
    
    /**
     * List of CategoryListItem objects
     *
     * @var array
     */
    protected $categories = [];
    
    /**
     * Sub resources.
     *
     * @var array
     */
    protected $subresource;
    
    
    /**
     * Initialize API Controller
     */
    protected function init()
    {
        $this->categoryWriteService           = StaticGXCoreLoader::getService('CategoryWrite');
        $this->categoryReadService            = StaticGXCoreLoader::getService('CategoryRead');
        $this->categoryJsonSerializer         = MainFactory::create('CategoryJsonSerializer');
        $this->categoryListItemJsonSerializer = MainFactory::create('CategoryListItemJsonSerializer');
        $this->subresource                    = [
            'products' => 'ProductsApiV2Controller',
        ];
        
        $this->_initializePagingAndSortingFields();
    }
    
    
    /**
     * @api             {post} /categories Create Category
     * @apiVersion      2.1.0
     * @apiName         CreateCategory
     * @apiGroup        Categories
     *
     * @apiDescription
     * Creates new category in the system. To see an example usage take a look at
     * `docs/REST/samples/category-service/create_category.php`
     */
    public function post()
    {
        if (($this->uri[1] ?? '') === 'search') {
            return $this->_search();
        }
        
        $categoryJsonString = json_encode($this->request->getParsedBody());
        
        if (empty($categoryJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Category data were not provided.', 400);
        }
        
        if ($this->_isBulkRequest($categoryJsonString)) {
            $response = $this->_createMultipleCategories($categoryJsonString);
            $this->_linkResponse($response['created']);
        } else {
            if (isset($this->uri[1]) && is_numeric($this->uri[1])) // Duplicate Category
            {
                $categoryJsonObject = json_decode($categoryJsonString);
                
                if ($categoryJsonObject->parentId === null || !is_numeric($categoryJsonObject->parentId)) {
                    $categoryJsonObject           = new stdClass;
                    $categoryJsonObject->parentId = 0; // Default category value.
                }
                
                $categoryId = $this->categoryWriteService->duplicateCategory(new IdType($this->uri[1]),
                                                                             new IdType($categoryJsonObject->parentId));
            } else // Create New Category
            {
                $category   = $this->categoryJsonSerializer->deserialize($categoryJsonString);
                $categoryId = $this->categoryWriteService->createCategory($category);
            }
            
            $storedCategory = $this->categoryReadService->getCategoryById(new IdType($categoryId));
            $response       = $this->categoryJsonSerializer->serialize($storedCategory, false);
            $this->_locateResource('categories', $categoryId);
            $this->_linkResponse($response);
        }
        
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 201);
    }
    
    
    /**
     * @api             {put} /categories/:id Update Category
     * @apiVersion      2.1.0
     * @apiName         UpdateCategory
     * @apiGroup        Categories
     *
     * @apiDescription
     * Use this method to update an existing category record. Take a look in the POST method for more detailed
     * explanation on every resource property. To see an example usage take a look at
     * `docs/REST/samples/category-service/update_category.php`
     */
    public function put()
    {
        $categoryJsonString = json_encode($this->request->getParsedBody());
        if (empty($categoryJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Category data were not provided.', 400);
        }
        
        if ($this->_isBulkRequest($categoryJsonString)) {
            $response = $this->_updateMultipleCategories($categoryJsonString);
            $this->_linkResponse($response['affected']);
        } else {
            if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
                throw new HttpApiV2Exception('Category record ID was not provided', 400);
            }
            
            $categoryId         = new IdType($this->uri[1]);
            $categoryJsonString = $this->_setJsonValue($categoryJsonString, 'id', $categoryId->asInt());
            $category           = $this->categoryJsonSerializer->deserialize($categoryJsonString,
                                                                             $this->categoryReadService->getCategoryById($categoryId));
            $this->categoryWriteService->updateCategory($category);
            $response = $this->categoryJsonSerializer->serialize($category, false);
            $this->_linkResponse($response);
        }
        
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 200);
    }
    
    
    /**
     * @api             {delete} /categories/:id Delete Category
     * @apiVersion      2.1.0
     * @apiName         DeleteCategory
     * @apiGroup        Categories
     *
     * @apiDescription
     * Removes a category record from the database. The products that are assigned to this category will not
     * be removed. To see an example usage take a look at
     * `docs/REST/samples/category-service/remove_category.php`
     */
    public function delete()
    {
        // Check if record ID was provided.
        if (!isset($this->uri[1])) {
            throw new HttpApiV2Exception('Category record ID was not provided in the resource URL.', 400);
        }
        
        if ($this->_isBulkDeleteRequest()) {
            $response = $this->_deleteMultipleCategories(explode(',', $this->uri[1]));
        } else {
            $response = $this->_deleteCategory((int)$this->uri[1]);
        }
        
        $this->_writeResponse($response, $this->_hasErrors($response) ? 400 : 200);
    }
    
    
    /**
     * @api             {get} /categories/:id Get Categories
     * @apiVersion      2.4.0
     * @apiName         GetCategory
     * @apiGroup        Categories
     *
     * @apiDescription
     * Get multiple or a single category records through a GET request. Use recursive GET-parameter flag to include all
     * child categories. All categories in the result will be amound themselves, so there is no nesting. This method
     * supports all the GET parameters that are mentioned in the "Introduction" section of this documentation.
     * To see an example usage take a look at `docs/REST/samples/category-service/fetch_category.php`
     */
    public function get()
    {
        if ($this->request->getQueryParam('changed') !== null || $this->request->getQueryParam('modified') !== null
            || $this->request->getQueryParam('deleted') !== null) {
            return $this->_changeHistory();
        }
        
        if ($this->_mapResponse($this->subresource)) {
            return;
        }
        
        // Parse customer status limit GET parameter.
        $customerStatusLimit = null;
        if ($this->request->getQueryParam('customer_status_limit') !== null) {
            $customerStatusLimit = new IdType($this->request->getQueryParam('customer_status_limit'));
        }
        
        // Parse language code GET parameter.
        $languageParameter = ($this->request->getQueryParam('lang')
                              !== null) ? $this->request->getQueryParam('lang') : 'de';
        $languageCode      = new LanguageCode(new NonEmptyStringType($languageParameter));
        
        $recursive = $this->request->getQueryParam('recursive') !== null;
        
        // Fetch the response data through the CategoryReadService.
        if (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            if (isset($this->uri[2]) && $this->uri[2] === 'children') // Get Category Children
            {
                $categories = $this->_getCategories($languageCode,
                                                    new IdType($this->uri[1]),
                                                    $customerStatusLimit,
                                                    $recursive);
            } else // Get Single Record
            {
                try {
                    $categories = [$this->categoryReadService->getCategoryById(new IdType($this->uri[1]))];
                } catch (UnexpectedValueException $e) {
                    throw new HttpApiV2Exception('Category does not exist.', 404);
                }
            }
        } else // Get All Categories
        {
            $categories = $this->_getCategories($languageCode, null, $customerStatusLimit, $recursive);
        }
        
        // Prepare the response array.
        $response = [];
        
        foreach ($categories as $category) {
            if ($category instanceof CategoryInterface) {
                $serialized = $this->categoryJsonSerializer->serialize($category, false);
            } else {
                $serialized = $this->categoryListItemJsonSerializer->serialize($category, false);
            }
            
            $response[] = $serialized;
        }
    
        $searchTerm = $this->request->getQueryParam('q') ?? $this->request->getQueryParam('search') ?? null;
        
        if ($searchTerm !== null) {
            $this->_searchResponse($response, $searchTerm);
        }
        
        $this->_sortResponse($response);
        $this->_paginateResponse($response);
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        
        // Return single resource to client and not array (if needed).
        if (isset($this->uri[1]) && is_numeric($this->uri[1]) && !isset($this->uri[2]) && count($response) > 0) {
            $response = $response[0];
        }
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * @param LanguageCode $languageCode
     * @param IdType|null  $id
     * @param IdType|null  $customerStatusLimit
     *
     * @return array
     */
    protected function _getCategories(
        LanguageCode $languageCode,
        IdType $id = null,
        IdType $customerStatusLimit = null,
        $recursive = false
    ) {
        $categories = $this->categoryReadService->getCategoryList($languageCode, $id, $customerStatusLimit)->getArray();
        
        /** @var CategoryListItem $category */
        foreach ($categories as $category) {
            $this->categories[] = $category;
            
            if ($recursive) {
                $this->_getCategories($languageCode,
                                      new IdType($category->getCategoryId()),
                                      $customerStatusLimit,
                                      $recursive);
            }
        }
        
        return $this->categories;
    }
    
    
    /*
    * Sub-Resource Category Search
    *
    * This method will search all products with a given search condition.
    *
    * @see CategoriesApiV2Controller::post()
    */
    protected function _search()
    {
        $languageParameter = ($this->request->getQueryParam('lang')
                              !== null) ? $this->request->getQueryParam('lang') : 'de';
        $languageCode      = new LanguageCode(new NonEmptyStringType($languageParameter));
        
        $json            = json_encode($this->request->getParsedBody());
        $searchCondition = CategorySearchCondition::createByJson(new NonEmptyStringType($json));
        
        try {
            $categories = $this->categoryReadService->searchCategories($languageCode,
                                                                       $searchCondition,
                                                                       $this->pager,
                                                                       $this->sorters)->getArray();
        } catch (Exception $e) {
            throw new HttpApiV2Exception($e->getMessage(), 400, $e);
        }
        
        // Prepare the response array.
        $response = [];
        foreach ($categories as $category) {
            if ($category instanceof CategoryInterface) {
                $serialized = $this->categoryJsonSerializer->serialize($category, false);
            } else {
                $serialized = $this->categoryListItemJsonSerializer->serialize($category, false);
            }
            
            $response[] = $serialized;
        }
        
        $this->_minimizeResponse($response);
        $this->_linkResponse($response);
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * History handler for modified, changed and deleted query parameters.
     *
     * @throws HttpApiV2Exception
     */
    protected function _changeHistory()
    {
        $changed = $this->request->getQueryParam('changed');
        if ($changed !== null) {
            $modified = $changed;
            $deleted  = $changed;
        } else {
            $modified = $this->request->getQueryParam('modified');
            $deleted  = $this->request->getQueryParam('deleted');
        }
        
        // Check format of modified and deleted date
        if ($modified !== null && !preg_match('/\d{2}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}/', $modified)) {
            throw new HttpApiV2Exception('Given modified date is invalid. Expected format: 2018-09-25 15:59:01', 400);
        }
        if ($deleted !== null && !preg_match('/\d{2}-\d{2}-\d{2} \d{2}\:\d{2}\:\d{2}/', $deleted)) {
            throw new HttpApiV2Exception('Given deleted date is invalid. Expected format: 2018-09-25 15:59:01', 400);
        }
        
        $languageParameter = ($this->request->getQueryParam('lang')
                              !== null) ? $this->request->getQueryParam('lang') : 'de';
        $languageCode      = new LanguageCode(new NonEmptyStringType($languageParameter));
        
        $response = ['deleted' => [], 'modified' => []];
        
        if ($modified !== null) {
            $searchCondition = CategorySearchCondition::createByArray(['search' => ['geq' => ['categories.last_modified' => $modified]]]);
            $categories      = $this->categoryReadService->searchCategories($languageCode,
                                                                            $searchCondition,
                                                                            $this->pager,
                                                                            $this->sorters)->getArray();
            foreach ($categories as $category) {
                $serialized             = $this->categoryListItemJsonSerializer->serialize($category, false);
                $response['modified'][] = $serialized;
            }
            $this->_linkResponse($response['modified']);
        }
        
        if ($deleted !== null) {
            $deleteHistoryReadService = DeleteHistoryServiceFactory::readService();
            $dateRange                = DateRange::create(new DateTime($deleted), new DateTime('now'));
            $deletedOrders            = $deleteHistoryReadService->findDeleted($dateRange,
                                                                               DeleteHistoryScope::categories());
            /** @var DeleteHistoryReportItem $deletedOrder */
            foreach ($deletedOrders as $deletedOrder) {
                $response['deleted'][] = [
                    'id'   => $deletedOrder->deletedId(),
                    'date' => $deletedOrder->deletedAt()->format('Y-m-d H:i:s'),
                ];
            }
        }
        
        $this->_writeResponse($response);
    }
    
    
    /**
     * Checks if the performed request was a bulk request (only for POST and PUT requests).
     *
     * @param string $jsonString The complete request body as JSON string
     *
     * @return bool
     */
    protected function _isBulkRequest($jsonString)
    {
        $json = json_decode($jsonString);
        
        if ($json === null && json_last_error() > 0) {
            throw new InvalidArgumentException('Provided JSON string is malformed and could not be parsed: '
                                               . $jsonString);
        }
        
        return is_array($json) && array_keys($json) === range(0, count($json) - 1);
    }
    
    
    /**
     * Checks if the performed delete request was a bulk request.
     *
     * @return bool
     */
    protected function _isBulkDeleteRequest()
    {
        return strpos($this->uri[1], ',') !== false;
    }
    
    
    /**
     * Checks if the response to a bulk request contains any errors.
     *
     * @param array $response The checked response
     *
     * @return bool
     */
    protected function _hasErrors($response)
    {
        return is_array($response) && array_key_exists('errors', $response) && !empty($response['errors']);
    }
    
    
    /**
     * Creates multiple categories and returns the response for this post request.
     *
     * @param string $categoryJsonString Request body as json string
     *
     * @return array Response
     */
    protected function _createMultipleCategories($categoryJsonString)
    {
        $categoryJsonArray = json_decode($categoryJsonString, true);
        $response          = [
            'created' => [],
            'errors'  => [],
        ];
        foreach ($categoryJsonArray as $categoryJsonData) {
            try {
                $category              = $this->categoryJsonSerializer->deserialize(json_encode($categoryJsonData));
                $categoryId            = $this->categoryWriteService->createCategory($category);
                $storedCategory        = $this->categoryReadService->getCategoryById(new IdType($categoryId));
                $responseData          = $this->categoryJsonSerializer->serialize($storedCategory, false);
                $response['created'][] = $responseData;
            } catch (Exception $exception) {
                $response['errors'][] = [
                    'errorMessage' => $exception->getMessage(),
                    'stacktrace'   => $exception->getTrace(),
                ];
            }
        }
        
        return $response;
    }
    
    
    /**
     * Updates multiple categories and returns the response for this put request.
     *
     * @param string $categoryJsonString Request body as json string
     *
     * @return array Response
     */
    protected function _updateMultipleCategories($categoryJsonString)
    {
        $categoryJsonArray = json_decode($categoryJsonString, true);
        $response          = [
            'affected' => [],
            'errors'   => [],
        ];
        foreach ($categoryJsonArray as $categoryData) {
            try {
                if (!isset($categoryData['id']) || !is_numeric($categoryData['id'])) {
                    throw new HttpApiV2Exception('Category record ID was not provided', 400);
                }
                
                $categoryId = new IdType((int)$categoryData['id']);
                $category   = $this->categoryJsonSerializer->deserialize(json_encode($categoryData),
                                                                         $this->categoryReadService->getCategoryById($categoryId));
                $this->categoryWriteService->updateCategory($category);
                $responseData           = $this->categoryJsonSerializer->serialize($category, false);
                $response['affected'][] = $responseData;
            } catch (Exception $exception) {
                $response['errors'][] = [
                    'errorMessage' => $exception->getMessage(),
                    'stacktrace'   => $exception->getTrace(),
                ];
            }
        }
        
        return $response;
    }
    
    
    /**
     * Deletes a single category and returns the response for this delete request.
     *
     * @param int $categoryId Id of the category that should be deleted
     *
     * @return array Response
     */
    protected function _deleteCategory($categoryId)
    {
        // Remove category record from database.
        $this->categoryWriteService->deleteCategoryById(new IdType($categoryId));
        
        // Return response JSON.
        return [
            'code'       => 200,
            'status'     => 'success',
            'action'     => 'delete',
            'resource'   => 'Category',
            'categoryId' => $categoryId
        ];
    }
    
    
    /**
     * Deletes multiple categories and returns the response for this delete request.
     *
     * @param array $categoryIds Ids of the categories that should be deleted
     *
     * @return array Response
     */
    protected function _deleteMultipleCategories($categoryIds)
    {
        $response = [
            'deleted' => [],
            'errors'  => [],
        ];
        foreach ($categoryIds as $id) {
            try {
                if (!is_numeric($id)) {
                    throw new HttpApiV2Exception('Category record ID was not provided in the resource URL.', 400);
                }
                
                $this->_deleteCategory((int)$id);
                $response['deleted'][] = (int)$id;
            } catch (Exception $exception) {
                $response['errors'][] = [
                    'errorMessage' => $exception->getMessage(),
                    'stacktrace'   => $exception->getTrace(),
                ];
            }
        }
        
        return $response;
    }

    /**
     * @param array $criteria
     *
     * @return bool
     * @throws HttpApiV2Exception
     */
    protected function _mapResponse(array $criteria)
    {
        if(count($this->uri) !== 3 || !($this->uri[0] === 'categories' && $this->uri[2] === 'products')) {
            return parent::_mapResponse($criteria);
        }

        /** @var ProductsApiV2Controller $controller */
        $controller = MainFactory::create($criteria['products'], $this->request, $this->response, $this->uri);
        $controller->getByCategories($this->uri[1]);

        return true;
    }
}
