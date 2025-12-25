<?php
/* --------------------------------------------------------------
   OrderExportRepository.php 2023-10-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\App\Data;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\AfterbuyOrderReader;
use GXModules\Gambio\Afterbuy\OrderExport\App\Data\AfterbuyOrder\OrderExportStorage;

/**
 * Class OrderExportRepository
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\App\Data
 */
class OrderExportRepository
{
    /**
     * @var AfterbuyOrderReader
     */
    private AfterbuyOrderReader $orderReader;
    
    
    /**
     * @var OrderExportStorage
     */
    private OrderExportStorage $storage;
    
    
    /**
     * OrderExportRepository constructor.
     *
     * @param AfterbuyOrderReader $orderReader
     * @param OrderExportStorage  $storage
     */
    public function __construct(AfterbuyOrderReader $orderReader, OrderExportStorage $storage)
    {
        $this->orderReader = $orderReader;
        $this->storage     = $storage;
    }
    
    
    /**
     * Returns the initial order export timestamp and null if the order was not send to afterbuy yet.
     *
     * @param OrderId $orderId
     *
     * @return int|null
     */
    public function getInitialExportTimestamp(OrderId $orderId): ?int
    {
        $datetime = $this->orderReader->getInitialExportDatetime($orderId);
        if ($datetime) {
            return strtotime($datetime);
        }
        
        return null;
    }
    
    
    /**
     * Returns the time of the last Afterbuy order export tracking link synchronization.
     * The time is returned to the format 'd.m.Y H:i:s'.
     *
     * @return string
     */
    public function getLastTrackingSyncTime(): string
    {
        return $this->storage->getLastTrackingSyncTime();
    }
    
    
    /**
     * Updates the afterbuy configuration 'last_tracking_sync' to now.
     *
     * @return void
     */
    public function updateLastTrackingSyncTime(): void
    {
        $this->storage->updateLastTrackingSyncTimeToNow();
    }
}