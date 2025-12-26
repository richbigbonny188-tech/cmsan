<?php
/* --------------------------------------------------------------
   PackingSlipFactoryInterface.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface PackingSlipFactoryInterface
 */
interface PackingSlipFactoryInterface
{
    /**
     * Creates a packing slip instance.
     *
     * @param \IdType             $id
     * @param \StringType         $number
     * @param \DateTime           $date
     * @param \FilenameStringType $filename
     * @param \IdType             $orderId
     *
     * @return bool|\PackingSlip
     */
    public function createPackingSlip(
        IdType $id,
        StringType $number,
        DateTime $date,
        FilenameStringType $filename,
        IdType $orderId
    );
}