<?php
/* --------------------------------------------------------------
   PaymentDetailsApiV2Controller.inc.php 2018-06-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('HttpApiV2Controller');

class PaymentDetailsApiV2Controller extends HttpApiV2Controller
{
    /**
     * Order read service.
     *
     * @var OrderReadService
     */
    protected $orderReadService;
    
    /**
     * Sub resources.
     *
     * @var array
     */
    protected $subresource;
    
    
    /**
     * Initializes API Controller
     *
     * @throws HttpApiV2Exception On missing order ID.
     */
    protected function __initialize()
    {
        // Check if the order ID was provided
        if (!isset($this->uri[1]) || !is_numeric($this->uri[1])) {
            throw new HttpApiV2Exception('Order record ID was not provided in the resource URL.', 400);
        }
        $this->orderReadService = StaticGXCoreLoader::getService('OrderRead');
        $this->subresource      = [];
    }
    
    
    /**
     * @api        {get} /orders/:id/payment_details Get Order Payment Details
     * @apiVersion 2.5.0
     * @apiName    GetPaymentDetails
     * @apiGroup   Orders
     *
     * @apiDescription
     * Get details regarding the payment associated with an order.
     *
     * @apiExample {curl} Get Payment Details
     *             curl -i --user admin@example.org:12345 https://example.org/api.php/v2/orders/400785/payment_details
     *
     * @apiParam {Number} [id] Order ID
     */
    public function get()
    {
        if ($this->_mapResponse($this->subresource)) {
            return;
        }
        
        $orderId       = new IdType($this->uri[1]);
        $order         = $this->orderReadService->getOrderById($orderId);
        $paymentModule = $order->getPaymentType()->getModule();
        
        $providerClass = implode('', array_map('ucfirst', explode('_', $paymentModule))) . 'PaymentDetailsProvider';
        if (class_exists($providerClass)) {
            /** @var \PaymentDetailsProvider $provider */
            $provider = MainFactory::create($providerClass);
            $details  = $provider->getDetails($orderId);
        } else {
            $details = [
                'error' => 'details unavailable for this payment module',
            ];
        }
        
        $paymentInstructions = $this->getPaymentInstructions($orderId);
        if (!empty($paymentInstructions)) {
            $details['payment_instructions'] = $paymentInstructions;
        }
        
        $response = [
            'module'  => $paymentModule,
            'details' => $details,
        ];
        $this->_writeResponse($response);
    }
    
    
    protected function getPaymentInstructions(IdType $orderId)
    {
        $db                  = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $paymentInstructions = $db->get_where('orders_payment_instruction', ['orders_id' => $orderId->asInt()])
            ->result_array();
        
        return $paymentInstructions;
    }
}
