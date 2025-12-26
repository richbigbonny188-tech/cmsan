<?php
/* --------------------------------------------------------------
  ReviewsApiV2Controller.inc.php 2022-05-03
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2022 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class ReviewsApiV2Controller
 *
 * Provides a gateway to the ReviewWriteService and ReviewReadService classes, which handle the shop
 * review resources.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class ReviewsApiV2Controller extends HttpApiV2Controller
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
     * Initializes API Controller
     */
    protected function __initialize()
    {
        $this->reviewWriteService   = StaticGXCoreLoader::getService('ReviewWrite');
        $this->reviewReadService    = StaticGXCoreLoader::getService('ReviewRead');
        $this->reviewJsonSerializer = MainFactory::create('ReviewJsonSerializer', $this->reviewWriteService);
    }
    
    
    /**
     * @api             {get} /reviews/:id Get reviews
     * @apiVersion      2.6.0
     * @apiName         GetReviews
     * @apiGroup        Reviews
     *
     * @apiDescription
     * Get multiple or a single review record through the GET method
     *
     * @apiExample {curl} Get All Review records
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/reviews
     *
     * @apiExample {curl} Get Review record With ID = 982
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/reviews/982
     *
     * @apiError        400-BadRequest Reviews data were not provided or reviews record or is invalid.
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Review record ID is invalid."
     * }
     *
     * @apiError        404-NotFound Review record could not be found.
     *
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
        if (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            try {
                $review = $this->reviewReadService->getById(new IdType((int)$this->uri[1]));
                
                return $this->_writeResponse($this->reviewJsonSerializer->serialize($review, $encode = false));
            } catch (EntityNotFoundException $e) {
                throw new HttpApiV2Exception($e->getMessage(), 404, $e);
            }
        }
        
        if (isset($this->uri[1]) && !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Review record ID is invalid: ' . gettype($this->uri[1]), 400);
        }
        
        return $this->_writeResponse($this->reviewJsonSerializer->serializeReviewCollection($this->reviewReadService->getAll(),
                                                                                            $encode = false));
    }
    
    
    /**
     * @api             {post} /reviews Create review
     * @apiVersion      2.6.0
     * @apiName         CreateReview
     * @apiGroup        Reviews
     *
     * @apiDescription
     * This method enables the creation of a new review into the system.
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
        $reviewJsonString = json_encode($this->request->getParsedBody());
        if (empty($reviewJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Review data were not provided.', 400);
        }
        
        $review = $this->reviewJsonSerializer->deserialize($reviewJsonString);
        $this->reviewWriteService->store($review);
        
        $this->_writeResponse($this->reviewJsonSerializer->serialize($review, $encode = false));
    }
    
    
    /**
     * @api             {put} /reviews/:id Update review
     * @apiVersion      2.6.0
     * @apiName         UpdateReview
     * @apiGroup        Reviews
     *
     * @apiDescription
     * Use this method to update an existing review record. Take a look in the POST method for more detailed
     * explanation on every resource property.
     *
     * @apiSuccess      Response-Body If successful, this method returns the updated withdrawal resource in the
     *                  response body.
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
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
     */
    public function put()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1] ?? null)) {
            throw new HttpApiV2Exception('Review record ID was not provided or is invalid: ' . gettype($this->uri[1] ?? null),
                                         400);
        }
        
        $reviewJsonString = json_encode($this->request->getParsedBody());
        if (empty($reviewJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Review data were not provided.', 400);
        }
        
        try {
            $review = $this->reviewJsonSerializer->deserialize($reviewJsonString,
                                                               $this->reviewReadService->getById(new IdType($this->uri[1])));
            $this->reviewWriteService->store($review);
            $this->_writeResponse($this->reviewJsonSerializer->serialize($review, $encode = false));
        } catch (EntityNotFoundException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 404, $e);
        }
    }
    
    
    /**
     * @api             {delete} /reviews/:id Delete review
     * @apiVersion      2.6.0
     * @apiName         DeleteReview
     * @apiGroup        Reviews
     *
     * @apiDescription
     * Removes a review record from the system. This method will always return success
     * even if the review does not exist (due to internal ReviewWriteService architecture
     * decisions, which strive to avoid unnecessary failures).
     *
     * @apiExample {curl} Delete review with ID = 84
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/reviews/84
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Review record ID was not provided or is invalid."
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
            throw new HttpApiV2Exception('Review record ID was not provided or is invalid in the resource URL', 400);
        }
        
        try {
            $review = $this->reviewReadService->getById(new IdType($this->uri[1]));
            $this->reviewWriteService->delete($review);
        } catch (EntityNotFoundException $e) {
        }
        
        $this->_writeResponse([
                                  'code'     => 200,
                                  'status'   => 'success',
                                  'action'   => 'delete',
                                  'reviewId' => (int)$this->uri[1]
                              ]);
    }
}
