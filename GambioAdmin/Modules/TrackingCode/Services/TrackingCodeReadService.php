<?php
/* --------------------------------------------------------------
   TrackingCodeReadService.php 2021-04-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Services;

use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodes;
use Gambio\Admin\Modules\TrackingCode\Model\TrackingCode;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\TrackingCodeNotFoundException;

/**
 * Interface TrackingCodeReadService
 *
 * @package Gambio\Admin\Modules\TrackingCode\Services
 */
interface TrackingCodeReadService
{
    /**
     * Returns all available tracking codes.
     *
     * @return TrackingCodes
     */
    public function getTrackingCodes(): TrackingCodes;
    
    
    /**
     * Returns a specific tracking code by its ID.
     *
     * @param int $id
     *
     * @return TrackingCode
     *
     * @throws TrackingCodeNotFoundException
     */
    public function getTrackingCodeById(int $id): TrackingCode;
    
    
    /**
     * Returns all tracking code, that belong to a specific order ID.
     *
     * @param int $orderId
     *
     * @return TrackingCodes
     */
    public function getTrackingCodesByOrderId(int $orderId): TrackingCodes;
}