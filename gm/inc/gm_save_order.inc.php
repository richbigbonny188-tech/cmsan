<?php
/* --------------------------------------------------------------------
   gm_save_order.inc.php 2023-06-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------------*/

/**
 * @param int    $orderId
 * @param string $orderMailBodyHtml deprecated, mail body is not stored anymore
 * @param string $orderMailBodyTxt  deprecated, mail body is not stored anymore
 * @param int    $gmSendOrderStatus
 *
 * @return void
 */
function gm_save_order($orderId, $orderMailBodyHtml, $orderMailBodyTxt, $gmSendOrderStatus)
{
    xtc_db_query('UPDATE `orders`
                    SET
                        `gm_send_order_status` = ' . (int)$gmSendOrderStatus . ',
                        `gm_order_send_date`   = NOW()
                    WHERE `orders_id` = ' . (int)$orderId);
}
