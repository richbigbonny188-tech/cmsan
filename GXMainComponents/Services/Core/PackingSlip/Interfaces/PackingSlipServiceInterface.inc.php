<?php
/* --------------------------------------------------------------
   PackingSlipServiceInterface.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PackingSlipServiceInterface
 */
interface PackingSlipServiceInterface
{
    /**
     * Gets all packing slips for all orders of the given list.
     *
     * @param \OrderListItemCollection $orderList
     *
     * @return \PackingSlipCollection
     *
     * @throws InvalidArgumentException
     */
    public function getPackingSlipsByOrderList(OrderListItemCollection $orderList);
    
    
    /**
     * Deletes all packing slips by a given order ID.
     *
     * @param \IdType $orderId
     *
     * @throws \InvalidArgumentException
     */
    public function deletePackingSlipsByOrderId(IdType $orderId);
}