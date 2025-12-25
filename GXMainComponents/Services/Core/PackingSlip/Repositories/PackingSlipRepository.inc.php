<?php
/* --------------------------------------------------------------
   PackingSlipRepository.inc.php 2018-05-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PackingSlipRepository
 */
class PackingSlipRepository implements PackingSlipRepositoryInterface
{
    /**
     * @var PackingSlipReader
     */
    protected $packingSlipReader;
    
    /**
     * @var PackingSlipDeleter
     */
    protected $packingSlipDeleter;
    
    
    /**
     * PackingSlipRepository constructor.
     *
     * @param \PackingSlipReader  $packingSlipReader
     * @param \PackingSlipDeleter $packingSlipDeleter
     */
    public function __construct(PackingSlipReader $packingSlipReader, PackingSlipDeleter $packingSlipDeleter)
    {
        $this->packingSlipReader  = $packingSlipReader;
        $this->packingSlipDeleter = $packingSlipDeleter;
    }
    
    
    /**
     * Gets all packing slips for a specific order.
     *
     * @param \IdType $orderId
     *
     * @return \PackingSlipCollection
     */
    public function getPackingSlipsByOrderId(IdType $orderId)
    {
        return $this->packingSlipReader->getPackingSlipsByOrderId($orderId);
    }
    
    
    /**
     * Gets all file names of packing slips for a given order ID.
     *
     * @param \IdType $orderId
     *
     * @return \StringCollection
     *
     * @throws \InvalidArgumentException
     */
    public function getPackingSlipFilenameCollectionByOrderId(IdType $orderId)
    {
        return $this->packingSlipReader->getPackingSlipFileNameCollectionByOrderId($orderId);
    }
    
    
    /**
     * Deletes all packing slips by a given order ID.
     *
     * @param \IdType $orderId
     */
    public function deletePackingSlipsByOrderId(IdType $orderId)
    {
        $this->packingSlipDeleter->deletePackingSlipsByOrderId($orderId);
    }
}