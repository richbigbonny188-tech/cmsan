<?php
/* --------------------------------------------------------------
   PayonePaymentDetailsProvider.inc.php 2018-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PayonePaymentDetailsProvider implements PaymentDetailsProvider
{
    public function getDetails(IdType $orderId)
    {
        $db              = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $transactionData = $db->get_where('payone_transactions', ['orders_id' => $orderId->asInt()])->row_array();
        $details         = $transactionData;
        unset($details['payone_transactions_id'], $details['orders_id']);
        
        return $details;
    }
}
