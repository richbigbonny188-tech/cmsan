<?php
/* --------------------------------------------------------------
   PackingSlipService.inc.php 2018-05-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2018 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class PackingSlipService
 */
class PackingSlipService implements PackingSlipServiceInterface
{
    /**
     * @var PackingSlipRepositoryInterface
     */
    protected $packingSlipRepository;
    
    /**
     * @var AbstractFileStorage
     */
    protected $fileStorage;
    
    
    /**
     * PackingSlipService constructor.
     *
     * @param \PackingSlipRepositoryInterface $packingSlipRepository
     * @param \AbstractFileStorage            $fileStorage
     */
    public function __construct(
        PackingSlipRepositoryInterface $packingSlipRepository,
        AbstractFileStorage $fileStorage
    ) {
        $this->packingSlipRepository = $packingSlipRepository;
        $this->fileStorage           = $fileStorage;
    }
    
    
    /**
     * Gets all packing slips for all orders of the given list.
     *
     * @param \OrderListItemCollection $orderList
     *
     * @return \PackingSlipCollection
     *
     * @throws InvalidArgumentException
     */
    public function getPackingSlipsByOrderList(OrderListItemCollection $orderList)
    {
        $packingSlips = [];
        
        /**
         * @var OrderListItem $order
         */
        foreach ($orderList as $order) {
            $orderPackingSlips = $this->packingSlipRepository->getPackingSlipsByOrderId(new IdType($order->getOrderId()));
            
            /**
             * @var PackingSlip
             */
            foreach ($orderPackingSlips as $orderPackingSlip) {
                $packingSlips[] = $orderPackingSlip;
            }
        }
        
        return MainFactory::create('PackingSlipCollection', $packingSlips);
    }
    
    
    /**
     * Deletes all packing slips by a given order ID.
     *
     * @param \IdType $orderId
     *
     * @throws \InvalidArgumentException
     */
    public function deletePackingSlipsByOrderId(IdType $orderId)
    {
        $filenameCollection = $this->packingSlipRepository->getPackingSlipFilenameCollectionByOrderId($orderId);
        $this->packingSlipRepository->deletePackingSlipsByOrderId($orderId);
        
        /**
         * @var FilenameStringType $filename
         */
        foreach ($filenameCollection as $filename) {
            $this->fileStorage->deleteFile($filename);
        }
    }
}