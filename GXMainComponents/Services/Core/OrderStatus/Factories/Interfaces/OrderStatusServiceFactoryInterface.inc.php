<?php

/* --------------------------------------------------------------
   OrderStatusServiceFactoryInterface.inc.php 2017-03-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2017 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface OrderStatusServiceFactoryInterface
 *
 * @category   System
 * @package    OrderStatus
 * @subpackage Factories
 */
interface OrderStatusServiceFactoryInterface
{
    /**
     * Creates a new instance of OrderStatusService.
     *
     * @return OrderStatusService
     */
    public function createService();
}