<?php
/* --------------------------------------------------------------
   PackingSlipDeleterInterface.inc.php 2018-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PackingSlipDeleterInterface
 */
interface PackingSlipDeleterInterface
{
    /**
     * Deletes all packing slips by a given order ID.
     *
     * @param \IdType $orderId
     */
    public function deletePackingSlipsByOrderId(IdType $orderId);
}