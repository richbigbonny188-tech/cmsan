<?php

/* --------------------------------------------------------------
   OrderStatusDeleterInterface.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusDeleterInterface
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Repositories
 */
interface OrderStatusDeleterInterface
{
    /**
     * Removes order status by the given order status id.
     *
     * @param \IntType $orderStatusId Order status id to be removed.
     *
     * @return $this|OrderStatusWriterInterface Same instance for chained method calls.
     */
    public function removeOrderStatus(IntType $orderStatusId);
}