<?php
/* --------------------------------------------------------------
   SepaPaymentDetailsProvider.inc.php 2018-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SepaPaymentDetailsProvider implements PaymentDetailsProvider
{
    public function getDetails(IdType $orderId)
    {
        /** @var \OrderReadService $orderReadService */
        $orderReadService = StaticGXCoreLoader::getService('OrderRead');
        $order            = $orderReadService->getOrderById($orderId);
        $db               = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $sepaData         = $db->get_where('sepa', ['orders_id' => $orderId->asInt()])->row_array();
        $keys             = [
            'sepa_owner',
            'sepa_iban',
            'sepa_bic',
            'sepa_bankname',
            'sepa_status',
            'sepa_prz',
            'sepa_fax'
        ];
        $details          = [
            'creditor_id'       => (string)@constant('MODULE_PAYMENT_SEPA_CREDITOR_ID'),
            'mandate_reference' => $order->getCustomerNumber(),
        ];
        foreach ($keys as $key) {
            $details[$key] = array_key_exists($key, $sepaData) ? $sepaData[$key] : '';
        }
        
        return $details;
    }
}
