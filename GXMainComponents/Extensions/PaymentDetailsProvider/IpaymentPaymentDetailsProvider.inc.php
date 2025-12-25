<?php
/* --------------------------------------------------------------
   IpaymentPaymentDetailsProvider.inc.php 2018-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class IpaymentPaymentDetailsProvider implements PaymentDetailsProvider
{
    public function getDetails(IdType $orderId)
    {
        $db          = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $ipaymentLog = $db->get_where('ipayment_log', ['orders_id' => $orderId->asInt()])->row_array();
        unset($ipaymentLog['ipayment_log_id'], $ipaymentLog['orders_id']);
        $details = $ipaymentLog;
        
        return $details;
    }
}
