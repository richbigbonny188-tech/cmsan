<?php
/* --------------------------------------------------------------
   Paypal3PaymentDetailsProvider.inc.php 2018-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class Paypal3PaymentDetailsProvider implements PaymentDetailsProvider
{
    public function getDetails(IdType $orderId)
    {
        $db           = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $paymentIdRow = $db->select('payment_id, mode')
            ->where('orders_id', $orderId->asInt())
            ->get('orders_paypal_payments')
            ->row_array();
        if (null !== $paymentIdRow) {
            try {
                $payment = MainFactory::create('PayPalPayment', $paymentIdRow['payment_id']);
                $details = [
                    'mode'       => $paymentIdRow['mode'],
                    'payment_id' => $paymentIdRow['payment_id'],
                    'details'    => $payment->json_object,
                ];
            } catch (Exception $e) {
                $details = [
                    'mode'       => $paymentIdRow['mode'],
                    'payment_id' => $paymentIdRow['payment_id'],
                    'error'      => $e->getMessage(),
                ];
            }
        } else {
            $details = [
                'error' => 'details unavailable',
            ];
        }
        
        return $details;
    }
}
