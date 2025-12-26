<?php

/* --------------------------------------------------------------
   OrderStatusReaderInterface.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusReaderInterface
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Repositories
 */
interface OrderStatusReaderInterface
{
    /**
     * Returns the data of the expected order status entity by the given order status id.
     *
     * @param \IntType $orderStatusId Id of expected order status entity
     *
     * @return array Data of order status entity or empty array, if no data was found.
     */
    public function getOrderStatusById(IntType $orderStatusId);
    
    
    /**
     * Returns the data of all order status resources in the storage.
     *
     * @return array Data of all order status entities.
     */
    public function getAllOrderStatus();
}