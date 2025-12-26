<?php
/* --------------------------------------------------------------
   PackingSlipReaderInterface.inc.php 2018-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PackingSlipReaderInterface
 */
interface PackingSlipReaderInterface
{
    /**
     * Gets all packing slips for a given order ID.
     *
     * @param \IdType $orderId
     *
     * @return \PackingSlipCollection
     *
     * @throws \InvalidArgumentException
     */
    public function getPackingSlipsByOrderId(IdType $orderId);
    
    
    /**
     * Gets all file names of packing slips for a given order ID.
     *
     * @param \IdType $orderId
     *
     * @return \StringCollection
     *
     * @throws \InvalidArgumentException
     */
    public function getPackingSlipFileNameCollectionByOrderId(IdType $orderId);
}