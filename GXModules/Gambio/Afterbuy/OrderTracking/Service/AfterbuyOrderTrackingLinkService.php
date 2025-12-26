<?php
/* --------------------------------------------------------------
   AfterbuyOrderTrackingLinkService.php 2023-01-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace GXModules\Gambio\Afterbuy\OrderTracking\Service;

use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotEnabledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Exceptions\AfterbuyNotInstalledException;
use GXModules\Gambio\Afterbuy\AfterbuyCommon\Model\OrderId;
use GXModules\Gambio\Afterbuy\OrderExport\Exceptions\AfterbuyResponseException;
use GXModules\Gambio\Afterbuy\OrderTracking\Exceptions\SyncTrackingLinksFailedException;

/**
 * Interface AfterbuyOrderTrackingLinkService
 *
 * @package GXModules\Gambio\Afterbuy\OrderTracking\Service
 */
interface AfterbuyOrderTrackingLinkService
{
    /**
     * Synchronizes Afterbuy order tracking links.
     *
     * Request orders from Afterbuy and checks if they contain a tracking number
     * and shipping method. If the shipping method matches one of the configured parcel
     * services, a new tracking link for the order will be generated (in the orders_parcel_tracking_codes table).
     *
     * @param string $lastTrackingSyncTime
     *
     * @throws SyncTrackingLinksFailedException
     * @throws AfterbuyNotInstalledException|AfterbuyNotEnabledException
     * @throws AfterbuyResponseException
     */
    public function syncTrackingLinks(string $lastTrackingSyncTime): void;
    
    
    /**
     * Synchronizes Afterbuy order tracking links for the given order.
     *
     * @param OrderId $orderId
     *
     * @throws SyncTrackingLinksFailedException
     * @throws AfterbuyNotInstalledException|AfterbuyNotEnabledException
     * @throws AfterbuyResponseException
     */
    public function syncTrackingLinksByOrderId(OrderId $orderId): void;
}