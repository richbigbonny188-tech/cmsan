<?php
/* --------------------------------------------------------------
   GiftVouchersOrderWriteService.inc.php 2021-07-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
declare(strict_types=1);

class GiftVouchersOrderWriteService extends GiftVouchersOrderWriteService_parent
{
    public function updateOrderStatus(
        IdType $orderId,
        IntType $newOrderStatusId,
        StringType $comment,
        BoolType $customerNotified,
        IdType $customerId = null
    )
    {
        $rc = parent::updateOrderStatus($orderId, $newOrderStatusId, $comment, $customerNotified, $customerId);
    
        if (defined('ACTIVATE_GIFT_SYSTEM') && filter_var(ACTIVATE_GIFT_SYSTEM, FILTER_VALIDATE_BOOLEAN)) {
            $eventHandler = new GiftVouchersOrderStatusChangeEventHandler();
            $eventHandler($orderId, $newOrderStatusId);
        }
        
        return $rc;
    }
}
