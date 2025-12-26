<?php
/* --------------------------------------------------------------
  ProductReviewsApiV2Controller.inc.php 2022-05-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class ProductReviewsApiV2Controller
 *
 * Provides a gateway to the ReviewWriteService and ReviewReadService classes, which handle the shop
 * review resources.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class ProductReviewsApiV2Controller extends HttpApiV2Controller
{
    /**
     * Reviews write service.
     *
     * @var ReviewWriteService
     */
    protected $reviewWriteService;
    
    /**
     * Reviews read service.
     *
     * @var ReviewReadService
     */
    protected $reviewReadService;
    
    /**
     * Review JSON serializer.
     *
     * @var ReviewJsonSerializer
     */
    protected $reviewJsonSerializer;
    
    /**
     * @var LanguageProviderInterface
     */
    protected $languageProvider;
    
    
    /**
     * Initializes API Controller
     */
    protected function __initialize()
    {
        $this->reviewWriteService   = StaticGXCoreLoader::getService('ReviewWrite');
        $this->reviewReadService    = StaticGXCoreLoader::getService('ReviewRead');
        $this->reviewJsonSerializer = MainFactory::create('ReviewJsonSerializer', $this->reviewWriteService);
        $this->languageProvider     = MainFactory::create('LanguageProvider',
                                                          StaticGXCoreLoader::getDatabaseQueryBuilder());
    }
    
    
    /**
     * @api             {get} /products/:id/reviews/:id Get reviews or average rating for single product
     * @apiVersion      2.6.0
     * @apiName         GetReviewsForProduct
     * @apiGroup        Products
     *
     * @apiDescription
     * Get multiple, a single review record or the average rating for a single product through the GET method.
     * Use optional lang (e.g.: lang=en) GET-parameter flag to filter based on the provided language code.
     *
     * @apiExample {curl} Get All Review records
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/products/1/reviews
     *
     * @apiExample {curl} Get All English Review records
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/products/1/reviews?lang=en
     *
     * @apiExample {curl} Get Review record with ID = 982
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/products/1/reviews/982
     *
     * @apiExample {curl} Get Average Rating For Product With ID = 1
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/products/1/reviews_avg
     *
     * @apiError        400-BadRequest Product ID is missing or invalid.
     * @apiErrorExample Error-Response (Missing or invalid product ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Product record ID was not provided or is invalid."
     * }
     *
     * @apiError        400-BadRequest Review ID is missing or invalid.
     * @apiErrorExample Error-Response (Missing or invalid review ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Review record ID is invalid."
     * }
     *
     * @apiError        404-NotFound Review record could not be found.
     * @apiErrorExample Error-Response (Review Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "status": "error",
     *     "message": "Review record could not be found."
     * }
     */
    public function get()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Product record ID was not provided or is invalid: ' . gettype($this->uri[1]),
                                         400);
        }
        
        if (isset($this->uri[2]) && $this->uri[2] === 'reviews_avg') {
            $productId = new IdType($this->uri[1]);
            
            return $this->_writeResponse([
                                             'action'    => 'reviews_avg',
                                             'productId' => $productId->asInt(),
                                             'average'   => $this->reviewReadService->getAverageRatingByProductId($productId)
                                         ]);
        }
        
        if (isset($this->uri[3]) && is_numeric($this->uri[3])) {
            try {
                $review = $this->reviewReadService->getById(new IdType((int)$this->uri[3]));
                
                return $this->_writeResponse($this->reviewJsonSerializer->serialize($review, $encode = false));
            } catch (EntityNotFoundException $e) {
                throw new HttpApiV2Exception($e->getMessage(), 404, $e);
            }
        }
        
        if (isset($this->uri[3]) && !is_numeric($this->uri[3])) {
            throw new HttpApiV2Exception('Review record ID is invalid: ' . gettype($this->uri[3]), 400);
        }
        
        $productId = new IdType($this->uri[1]);
        if ($this->request->getQueryParam('lang') !== null) {
            $langParameter = $this->request->getQueryParam('lang');
            $languageCode  = new LanguageCode(new NonEmptyStringType($langParameter));
            $languageId    = new IdType($this->languageProvider->getIdByCode($languageCode));
            
            $reviewCollection = $this->reviewReadService->getReviewsByProductId($productId, $languageId);
        } else {
            $reviewCollection = new ReviewCollection();
            foreach ($this->languageProvider->getIds() as $languageId) {
                $productReviews = $this->reviewReadService->getReviewsByProductId($productId, $languageId);
                foreach ($productReviews as $review) {
                    $reviewCollection->addItem($review);
                }
            }
        }
        
        return $this->_writeResponse($this->reviewJsonSerializer->serializeReviewCollection($reviewCollection,
                                                                                            $encode = false));
    }
    
    
    /**
     * @api             {post} /products/:id/reviews Create review for single product
     * @apiVersion      2.6.0
     * @apiName         CreateReviewForProduct
     * @apiGroup        Products
     *
     * @apiDescription
     * This method enables the creation of a new review into the system for the product with the ID that is provided in
     * the request URI. The productId in the request URI will override the productId of the request body.
     *
     * @apiParamExample {json} Review entity
     * {
     *     "productId": 2,
     *     "rating": 5,
     *     "languageId": 1,
     *     "text": "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam",
     *     "customer": {
     *         "customerId": 3,
     *         "customerName": "John Doe"
     *     }
     * }
     *
     * @apiParam {Number} productId The ID of the reviewed product.
     * @apiParam {Number} rating The numeric rating value. All integers from 1 to 5 are valid.
     * @apiParam {Number} languageId The language ID.
     * @apiParam {String} text The review content.
     * @apiParam {Object} customer Contains various customer specific information.
     * @apiParam {Number} customer.customerId Customer's ID.
     * @apiParam {String} customer.customerName Customer's name.
     *
     * @apiSuccess (201) Request-Body If successful, this method returns the complete review resource in the response
     *             body.
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *     "id": 3,
     *     "productId": 2,
     *     "rating": 5,
     *     "dateAdded": "2018-01-10 19:10:00",
     *     "lastModified": "2018-01-10 19:10:00",
     *     "languageId": 1,
     *     "text": "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam",
     *     "customer": {
     *         "customerId": 3,
     *         "customerName": "John Doe"
     *     }
     * }
     *
     * @apiError        400-BadRequest Product ID is missing or invalid.
     * @apiErrorExample Error-Response (Missing or invalid product ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Product record ID was not provided or is invalid."
     * }
     *
     * @apiError        400-BadRequest The body of the request was empty.
     * @apiErrorExample Error-Response
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Review data were not provided."
     * }
     */
    public function post()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Product record ID was not provided or is invalid: ' . gettype($this->uri[1]),
                                         400);
        }
        
        $reviewJsonString = json_encode($this->request->getParsedBody());
        if (empty($reviewJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Review data were not provided.', 400);
        }
        
        $productId = new IdType($this->uri[1]);
        $review    = $this->reviewJsonSerializer->deserialize($reviewJsonString);
        $review->setProductId($productId);
        
        $this->reviewWriteService->store($review);
        
        $this->_writeResponse($this->reviewJsonSerializer->serialize($review, $encode = false));
    }
    
    
    /**
     * @api             {put} /products/:id/reviews/:id Update review for single product
     * @apiVersion      2.6.0
     * @apiName         UpdateReviewForProduct
     * @apiGroup        Products
     *
     * @apiDescription
     * Use this method to update an existing review record. Take a look in the POST method for more detailed
     * explanation on every resource property.
     *
     * @apiSuccess      Response-Body If successful, this method returns the updated withdrawal resource in the
     *                  response body.
     *
     * @apiError        400-BadRequest Product ID is missing or invalid.
     * @apiErrorExample Error-Response (Missing or invalid product ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Product record ID was not provided or is invalid."
     * }
     *
     * @apiError        400-BadRequest Review ID is missing or invalid.
     * @apiErrorExample Error-Response (Missing or invalid review ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Review record ID was not provided or is invalid."
     * }
     *
     * @apiError        400-BadRequest Review data were not provided.
     * @apiErrorExample Error-Response (No data)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Review data were not provided."
     * }
     *
     * @apiError        404-NotFound Review record with provided ID not associated with provided product record ID.
     * @apiErrorExample Error-Response (Wrong product-to-review association)
     * HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "status": "error",
     *     "message": "Provided review record ID ([:reviewId]) is not associated with provided product record ID
     *     ([:productId])."
     * }
     */
    public function put()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Product record ID was not provided or is invalid: ' . gettype($this->uri[1]),
                                         400);
        }
        
        if (!isset($this->uri[3]) || !is_numeric($this->uri[3])) {
            throw new HttpApiV2Exception('Review record ID was not provided or is invalid: ' . gettype($this->uri[3] ?? null),
                                         400);
        }
        
        $reviewJsonString = json_encode($this->request->getParsedBody());
        if (empty($reviewJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Review data were not provided.', 400);
        }
        
        try {
            $review = $this->reviewReadService->getById(new IdType($this->uri[3]));
            if ($review->getProductId() !== (int)$this->uri[1]) {
                throw new EntityNotFoundException('Provided review record ID (' . $this->uri[3]
                                                  . ') is not associated with provided product record ID ('
                                                  . $this->uri[1] . ').', 404);
            }
            
            $productId = new IdType($this->uri[1]);
            $review    = $this->reviewJsonSerializer->deserialize($reviewJsonString, $review);
            $review->setProductId($productId);
            
            $this->reviewWriteService->store($review);
            
            $this->_writeResponse($this->reviewJsonSerializer->serialize($review, $encode = false));
        } catch (EntityNotFoundException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 404, $e);
        }
    }
    
    
    /**
     * @api             {delete} /products/:1/reviews/:id Delete review
     * @apiVersion      2.6.0
     * @apiName         DeleteReviewForProduct
     * @apiGroup        Products
     *
     * @apiDescription
     * Removes a review record from the system. Assuming that the provided product-to-review association (product ID
     * and review Id in request URI) is correct this method will always return success even if the review does not
     * exist (due to internal ReviewWriteService architecture decisions, which strive to avoid unnecessary failures).
     *
     * @apiExample {curl} Delete review with ID = 84 for product with ID = 23
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/products/23/reviews/84
     *
     *
     * @apiError        400-BadRequest Product ID is missing or invalid.
     * @apiErrorExample Error-Response (Missing or invalid product ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Product record ID was not provided or is invalid."
     * }
     *
     * @apiError        400-BadRequest Review ID is missing or invalid.
     * @apiErrorExample Error-Response (Missing or invalid review ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Review record ID was not provided or is invalid."
     * }
     *
     * @apiError        404-NotFound Review record with provided ID not associated with provided product record ID.
     * @apiErrorExample Error-Response (Wrong product-to-review association)
     * HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "status": "error",
     *     "message": "Provided review record ID ([:reviewId]) is not associated with provided product record ID
     *     ([:productId])."
     * }
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *     "code": 200,
     *     "status": "success",
     *     "action": "delete",
     *     "reviewId": 84
     * }
     */
    public function delete()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Product record ID was not provided or is invalid: ' . gettype($this->uri[1]),
                                         400);
        }
        
        if (!isset($this->uri[3]) || !is_numeric($this->uri[3])) {
            throw new HttpApiV2Exception('Review record ID was not provided or is invalid in the resource URL', 400);
        }
        
        try {
            $review = $this->reviewReadService->getById(new IdType($this->uri[3]));
            if ($review->getProductId() !== (int)$this->uri[1]) {
                throw new HttpApiV2Exception('Provided review record ID (' . $this->uri[3]
                                             . ') is not associated with provided product record ID (' . $this->uri[1]
                                             . ').', 404);
            }
            
            $this->reviewWriteService->delete($review);
        } catch (EntityNotFoundException $e) {
        }
        
        $this->_writeResponse([
                                  'code'     => 200,
                                  'status'   => 'success',
                                  'action'   => 'delete',
                                  'reviewId' => (int)$this->uri[3]
                              ]);
    }
}
