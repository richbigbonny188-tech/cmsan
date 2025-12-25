<?php
/* --------------------------------------------------------------
   AmazonadvpayPaymentDetailsProvider.inc.php 2018-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class AmazonadvpayPaymentDetailsProvider implements PaymentDetailsProvider
{
    public function getDetails(IdType $orderId)
    {
        $details     = [
            'details' => [
                'orders' => [],
            ],
        ];
        $db          = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $ordersQuery = $db->get_where('amzadvpay_orders', ['orders_id' => $orderId->asInt()])->result_array();
        foreach ($ordersQuery as $amzPayOrder) {
            $orderDetails        = simplexml_load_string($amzPayOrder['last_details']);
            $authorizations      = [];
            $authorizationsQuery = $db->get_where('amzadvpay_authorizations',
                                                  ['order_reference_id' => $amzPayOrder['order_reference_id']])
                ->result_array();
            foreach ($authorizationsQuery as $amzPayAuthorization) {
                $captures = [];
                if (!empty($amzPayAuthorization['last_details'])) {
                    $authorization = simplexml_load_string($amzPayAuthorization['last_details']);
                    $capturesQuery = $db->get_where('amzadvpay_captures',
                                                    ['authorization_reference_id' => $amzPayAuthorization['authorization_reference_id']])
                        ->result_array();
                    foreach ($capturesQuery as $amzPayCapture) {
                        if (!empty($amzPayCapture['last_details'])) {
                            $capture      = simplexml_load_string($amzPayCapture['last_details']);
                            $refundsQuery = $db->get_where('amzadvpay_refunds',
                                                           ['capture_reference_id' => $amzPayCapture['capture_reference_id']])
                                ->result_array();
                            $refunds      = [];
                            foreach ($refundsQuery as $amzPayRefund) {
                                if (!empty($amzPayRefund['last_details'])) {
                                    $refund = simplexml_load_string($amzPayRefund['last_details']);
                                } else {
                                    $refund = null;
                                }
                                
                                $refunds[] = [
                                    'refund_reference_id' => $amzPayRefund['refund_reference_id'],
                                    'state'               => $amzPayRefund['state'],
                                    'last_update'         => $amzPayRefund['last_update'],
                                    'details'             => $refund,
                                ];
                            }
                        } else {
                            $capture = null;
                            $refunds = [];
                        }
                        $captures[] = [
                            'capture_reference_id' => $amzPayCapture['capture_reference_id'],
                            'state'                => $amzPayCapture['state'],
                            'last_update'          => $amzPayCapture['last_update'],
                            'details'              => $capture,
                            'refunds'              => $refunds,
                        ];
                    }
                } else {
                    $authorization = null;
                }
                $authorizations[] = [
                    'authorization_reference_id' => $amzPayAuthorization['authorization_reference_id'],
                    'state'                      => $amzPayAuthorization['state'],
                    'last_update'                => $amzPayAuthorization['last_update'],
                    'details'                    => $authorization,
                    'captures'                   => $captures,
                ];
            }
            $details['details']['orders'][] = [
                'order_reference_id' => $amzPayOrder['order_reference_id'],
                'state'              => $amzPayOrder['state'],
                'last_update'        => $amzPayOrder['last_update'],
                'details'            => $orderDetails,
                'authorizations'     => $authorizations,
            ];
        }
        
        return $details;
    }
}
