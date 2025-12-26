<?php
/* --------------------------------------------------------------
   AfterbuyCheckPaidStatusService.php 2022-11-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderStatus\Service;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderStatus\Exceptions\AfterbuyOrderStatusPaidException;
use GXModules\Gambio\Afterbuy\OrderStatus\Model\AfterbuyOrderStatus;

/**
 * Interface AfterbuyCheckPaidStatusService
 *
 * @package GXModules\Gambio\Afterbuy\OrderStatus\Service
 */
interface AfterbuyCheckPaidStatusService
{
    /**
     * Returns an instance of `OrderStatusPaid` check, containing information if the order was already marked
     * as paid for Afterbuy, unpaid or unknown.
     * Throws an `OrderNotFoundException` if no order was found.
     *
     * @param OrderId $orderId
     *
     * @return AfterbuyOrderStatus
     * @throws AfterbuyOrderStatusPaidException
     */
    public function getPaidStatus(OrderId $orderId): AfterbuyOrderStatus;
}