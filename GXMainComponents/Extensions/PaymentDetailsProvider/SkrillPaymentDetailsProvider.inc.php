<?php
/* --------------------------------------------------------------
   SkrillPaymentDetailsProvider.inc.php 2018-06-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class SkrillPaymentDetailsProvider implements PaymentDetailsProvider
{
    public function getDetails(IdType $orderId)
    {
        $db         = StaticGXCoreLoader::getDatabaseQueryBuilder();
        $skrillData = $db->get_where('payment_skrill', ['skrill_ORDERID' => $orderId->asInt()])->row_array();
        $details    = $skrillData;
        
        return $details;
    }
}
