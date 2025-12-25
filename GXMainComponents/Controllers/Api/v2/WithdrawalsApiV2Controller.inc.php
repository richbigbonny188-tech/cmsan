<?php
/* --------------------------------------------------------------
  WithdrawalsApiV2Controller.inc.php 2019-11-11
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2019 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

/**
 * Class WithdrawalsApiV2Controller
 *
 * Provides a gateway to the WithdrawalWriteService and WithdrawalReadService classes, which handle the shop
 * withdrawal resources.
 *
 * @category System
 * @package  ApiV2Controllers
 */
class WithdrawalsApiV2Controller extends HttpApiV2Controller
{
    /**
     * Withdrawal write service.
     *
     * @var WithdrawalWriteService
     */
    protected $withdrawalWriteService;
    
    /**
     * Withdrawal read service.
     *
     * @var WithdrawalReadService
     */
    protected $withdrawalReadService;
    
    /**
     * Review JSON serializer.
     *
     * @var WithdrawalJsonSerializer
     */
    protected $withdrawalJsonSerializer;
    
    
    /**
     * Initializes API Controller
     */
    protected function __initialize()
    {
        $this->withdrawalWriteService   = StaticGXCoreLoader::getService('WithdrawalWrite');
        $this->withdrawalReadService    = StaticGXCoreLoader::getService('WithdrawalRead');
        $this->withdrawalJsonSerializer = MainFactory::create('WithdrawalJsonSerializer',
                                                              $this->withdrawalWriteService);
    }
    
    
    /**
     * @api             {get} /withdrawals/:id Get withdrawals
     * @apiVersion      2.6.0
     * @apiName         GetWithdrawals
     * @apiGroup        Withdrawals
     *
     * @apiDescription
     * Get multiple or a single withdrawal record through the GET method
     *
     * @apiExample {curl} Get All Withdrawal records
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/withdrawals
     *
     * @apiExample {curl} Get Withdrawal record With ID = 982
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/withdrawals/982
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Withdrawal record ID is invalid."
     * }
     *
     * @apiError        404-NotFound Withdrawal record could not be found.
     *
     * @apiErrorExample Error-Response (Withdrawal Not Found)
     * HTTP/1.1 404 Not Found
     * {
     *     "code": 404,
     *     "status": "error",
     *     "message": "Withdrawal record could not be found."
     * }
     */
    public function get()
    {
        if (isset($this->uri[1]) && is_numeric($this->uri[1])) {
            try {
                $withdrawal = $this->withdrawalReadService->getById(new IdType((int)$this->uri[1]));
                
                return $this->_writeResponse($this->withdrawalJsonSerializer->serialize($withdrawal, $encode = false));
            } catch (EntityNotFoundException $e) {
                throw new HttpApiV2Exception($e->getMessage(), 404, $e);
            }
        }
        
        if (isset($this->uri[1]) && !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Withdrawal record ID is invalid: ' . gettype($this->uri[1]), 400);
        }
        
        return $this->_writeResponse($this->withdrawalJsonSerializer->serializeWithdrawalCollection($this->withdrawalReadService->getAll(),
                                                                                                    $encode = false));
    }
    
    
    /**
     * @api             {post} /withdrawals Create withdrawal
     * @apiVersion      2.6.0
     * @apiName         CreateWithdrawal
     * @apiGroup        Withdrawals
     *
     * @apiDescription
     * This method enables the creation of a new withdrawal into the system.
     *
     * @apiParamExample {json} Withdrawal entity
     * {
     *     "withdrawalDate": "2018-01-05 00:00:00",
     *     "content": "Hiermit widerrufe(n) ich/wir den von mir/uns abgeschlossenen Vertrag ...",
     *     "order": {
     *         "orderId": 400212,
     *         "customerId": 1,
     *         "customerGender": "Herr",
     *         "customerFirstName": "John",
     *         "customerLastName": "Doe",
     *         "customerStreetAddress": "Parallelweg 30",
     *         "customerPostCode": "28219",
     *         "customerCity": "Bremen",
     *         "customerCountry": "Germany",
     *         "customerEmail": "john@doe.com",
     *         "orderDate": "2018-01-05 00:00:00",
     *         "deliveryDate": "1970-01-01 00:00:00"
     *     },
     *     "createdByAdmin": true
     * }
     *
     * @apiParam {String} withdrawalDate The withdrawal date. Must have the 'Y-m-d H:i:s' format.
     * @apiParam {String} content The withdrawal content.
     * @apiParam {Object} order Contains various order specific information.
     * @apiParam {Number} order.orderId Order ID.
     * @apiParam {Number} order.customerId Customer's ID.
     * @apiParam {String} order.customerGender Customer's gender.
     * @apiParam {String} order.customerFirstName Customer's first name.
     * @apiParam {String} order.customerLastName Customer's last name.
     * @apiParam {String} order.customerStreetAddress Customer's street address with house number.
     * @apiParam {String} order.customerPostCode Customer's post code.
     * @apiParam {String} order.customerCity Customer's city.
     * @apiParam {String} order.customerCountry Customer's country.
     * @apiParam {String} order.customerEmail Customer's email address.
     * @apiParam {String} order.orderDate Order date. Must have the 'Y-m-d H:i:s' format.
     * @apiParam {String} order.deliveryDate Delivery date. Must have the 'Y-m-d H:i:s' format.
     * @apiParam {Boolean} createdByAdmin Whether the withdrawal was created by customer or by an admin.
     *
     * @apiSuccess (201) Request-Body If successful, this method returns the complete withdrawal resource
     * in the response body.
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *     "id": 3,
     *     "withdrawalDate": "2018-01-05 00:00:00",
     *     "content": "Hiermit widerrufe(n) ich/wir den von mir/uns abgeschlossenen Vertrag ...",
     *     "order": {
     *         "orderId": 400212,
     *         "customerId": 1,
     *         "customerGender": "Herr",
     *         "customerFirstName": "John",
     *         "customerLastName": "Doe",
     *         "customerStreetAddress": "Parallelweg 30",
     *         "customerPostCode": "28219",
     *         "customerCity": "Bremen",
     *         "customerCountry": "Germany",
     *         "customerEmail": "john@doe.com",
     *         "orderDate": "2018-01-05 00:00:00",
     *         "deliveryDate": "1970-01-01 00:00:00"
     *     },
     *     "dateCreated": "2018-01-10 19:10:00",
     *     "createdByAdmin": true
     *     }
     *
     * @apiError        400-BadRequest The body of the request was empty.
     * @apiErrorExample Error-Response
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Withdrawal data were not provided."
     * }
     */
    public function post()
    {
        $withdrawalJsonString = json_encode($this->request->getParsedBody());
        if (empty($withdrawalJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Withdrawal data were not provided.', 400);
        }
        
        $withdrawal = $this->withdrawalJsonSerializer->deserialize($withdrawalJsonString);
        $this->withdrawalWriteService->store($withdrawal);
        
        $this->_writeResponse($this->withdrawalJsonSerializer->serialize($withdrawal, $encode = false));
    }
    
    
    /**
     * @api             {put} /withdrawals/:id Update withdrawal
     * @apiVersion      2.6.0
     * @apiName         UpdateWithdrawal
     * @apiGroup        Withdrawals
     *
     * @apiDescription
     * Use this method to update an existing withdrawal record. Take a look in the POST method for more detailed
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
     *     "message": "Withdrawal record ID was not provided or is invalid."
     * }
     *
     * @apiError        400-BadRequest Withdrawal data were not provided.
     * @apiErrorExample Error-Response (No data)
     * HTTP/1.1 400 Bad Request
     * {
     *   "code": 400,
     *   "status": "error",
     *   "message": "Withdrawal data were not provided."
     * }
     */
    public function put()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Withdrawal record ID was not provided or is invalid: '
                                         . gettype($this->uri[1]), 400);
        }
        
        $withdrawalJsonString = json_encode($this->request->getParsedBody());
        if (empty($withdrawalJsonString) || $this->request->getParsedBody() === null) {
            throw new HttpApiV2Exception('Withdrawal data were not provided.', 400);
        }
        
        try {
            $withdrawal = $this->withdrawalJsonSerializer->deserialize($withdrawalJsonString,
                                                                       $this->withdrawalReadService->getById(new IdType($this->uri[1])));
            $this->withdrawalWriteService->store($withdrawal);
            $this->_writeResponse($this->withdrawalJsonSerializer->serialize($withdrawal, $encode = false));
        } catch (EntityNotFoundException $e) {
            throw new HttpApiV2Exception($e->getMessage(), 404, $e);
        }
    }
    
    
    /**
     * @api             {delete} /withdrawals/:id Delete withdrawal
     * @apiVersion      2.6.0
     * @apiName         DeleteWithdrawal
     * @apiGroup        Withdrawals
     *
     * @apiDescription
     * Removes a withdrawal record from the system. This method will always return success
     * even if the withdrawal does not exist (due to internal WithdrawalWriteService architecture
     * decisions, which strive to avoid unnecessary failures).
     *
     * @apiExample {curl} Delete Withdrawal with ID = 84
     *             curl -X DELETE --user admin@example.org:12345 https://example.org/api.php/v2/withdrawals/84
     *
     * @apiSuccessExample {json} Success-Response
     * {
     *     "code": 200,
     *     "status": "success",
     *     "action": "delete",
     *     "withdrawalId": 84
     * }
     *
     * @apiErrorExample Error-Response (Missing or invalid ID)
     * HTTP/1.1 400 Bad Request
     * {
     *     "code": 400,
     *     "status": "error",
     *     "message": "Withdrawal record ID was not provided or is invalid."
     * }
     */
    public function delete()
    {
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Withdrawal record ID was not provided or is invalid.', 400);
        }
        
        try {
            $withdrawal = $this->withdrawalReadService->getById(new IdType($this->uri[1]));
            $this->withdrawalWriteService->delete($withdrawal);
        } catch (EntityNotFoundException $e) {
        }
        
        $this->_writeResponse([
                                  'code'         => 200,
                                  'status'       => 'success',
                                  'action'       => 'delete',
                                  'withdrawalId' => (int)$this->uri[1]
                              ]);
    }
}
