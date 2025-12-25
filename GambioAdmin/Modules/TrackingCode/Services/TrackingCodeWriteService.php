<?php
/* --------------------------------------------------------------
   TrackingCodeWriteService.php 2021-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Services;

use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodeIds;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\ParcelServiceDetails;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\CreationOfTrackingCodesFailedException;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\DeletionOfTrackingCodesFailedException;

/**
 * Interface TrackingCodeWriteService
 *
 * @package Gambio\Admin\Modules\TrackingCode\Services
 */
interface TrackingCodeWriteService
{
    /**
     * Creates a new tracking code and returns its ID.
     *
     * @param int                  $orderId
     * @param string               $code
     * @param ParcelServiceDetails $parcelServiceDetails
     * @param bool                 $isReturnDelivery
     *
     * @return TrackingCodeId
     *
     * @throws CreationOfTrackingCodesFailedException
     */
    public function createTrackingCode(
        int                  $orderId,
        string               $code,
        ParcelServiceDetails $parcelServiceDetails,
        bool                 $isReturnDelivery
    ): TrackingCodeId;
    
    
    /**
     * Creates multiple tracking codes and returns their IDs.
     *
     * @param array ...$creationArguments Provided array must contain arguments like they are used in the single
     *                                    creation method. Provide multiple arrays for multi creation.
     *
     * @return TrackingCodeIds
     *
     * @throws CreationOfTrackingCodesFailedException
     */
    public function createMultipleTrackingCodes(array ...$creationArguments): TrackingCodeIds;
    
    
    /**
     * Deletes tracking codes based on the given IDs.
     *
     * @param int ...$ids
     *
     * @throws DeletionOfTrackingCodesFailedException
     */
    public function deleteTrackingCodes(int ...$ids): void;
}