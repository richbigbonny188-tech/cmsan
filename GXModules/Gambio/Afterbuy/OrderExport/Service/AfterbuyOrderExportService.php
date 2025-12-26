<?php
/* --------------------------------------------------------------
   AfterbuyOrderExportService.php 2022-11-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderExport\Service;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;

/**
 * Interface AfterbuyOrderExportService
 *
 * @package GXModules\Gambio\Afterbuy\OrderExport\Service
 */
interface AfterbuyOrderExportService
{
    /**
     * Returns the initial order export timestamp and null if the order was not send to afterbuy yet.
     *
     * @param OrderId $orderId
     *
     * @return int|null
     */
    public function getInitialExportTimestamp(OrderId $orderId): ?int;
    
    
    /**
     * Updates the Afterbuy configuration last tracking sync (last_tracking_sync) to now.
     */
    public function updateLastTrackingSyncTime(): void;
    
    
    /**
     * Returns the last afterbuy tracking link synchronization time in format 'd.m.Y H:i:s'.
     *
     * @return string
     */
    public function getLastTrackingSyncTime(): string;
}
