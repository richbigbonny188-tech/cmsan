<?php
/* --------------------------------------------------------------
   TrackingCodeRepository.php 2021-10-08
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
use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodes;
use Gambio\Admin\Modules\TrackingCode\Model\TrackingCode;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\ParcelServiceDetails;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\CreationOfTrackingCodesFailedException;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\DeletionOfTrackingCodesFailedException;
use Gambio\Admin\Modules\TrackingCode\Services\Exceptions\TrackingCodeNotFoundException;
use Gambio\Core\Filter\Filters;
use Gambio\Core\Filter\Pagination;
use Gambio\Core\Filter\Sorting;

/**
 * Interface TrackingCodeRepository
 *
 * @package Gambio\Admin\Modules\TrackingCode\Services
 */
interface TrackingCodeRepository
{
    /**
     * Returns a filtered and paginated collection of tracking codes based on the given filter and sorting arguments.
     *
     * @param Filters    $filters
     * @param Sorting    $sorting
     * @param Pagination $pagination
     *
     * @return TrackingCodes
     */
    public function filterTrackingCodes(Filters $filters, Sorting $sorting, Pagination $pagination): TrackingCodes;
    
    
    /**
     * Returns total count of tracking codes based on the given filter arguments.
     *
     * @param Filters $criteria
     *
     * @return int
     */
    public function getTrackingCodesTotalCount(Filters $criteria): int;
    
    
    /**
     * Returns all available tracking codes.
     *
     * @return TrackingCodes
     */
    public function getAllTrackingCodes(): TrackingCodes;
    
    
    /**
     * Returns a specific tracking code by its ID.
     *
     * @param TrackingCodeId $id
     *
     * @return TrackingCode
     *
     * @throws TrackingCodeNotFoundException
     */
    public function getTrackingCodeById(TrackingCodeId $id): TrackingCode;
    
    
    /**
     * Creates a new tracking code and returns its ID.
     *
     * @param OrderId              $orderId
     * @param string               $code
     * @param ParcelServiceDetails $parcelServiceDetails
     * @param bool                 $isReturnDelivery
     *
     * @return TrackingCodeId
     *
     * @throws CreationOfTrackingCodesFailedException
     */
    public function createTrackingCode(
        OrderId              $orderId,
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
     *
     * @return TrackingCodeIds
     *
     * @throws CreationOfTrackingCodesFailedException
     */
    public function createMultipleTrackingCodes(array ...$creationArguments): TrackingCodeIds;
    
    
    /**
     * Deletes tracking codes based on the given IDs.
     *
     * @param TrackingCodeId ...$ids
     *
     * @throws DeletionOfTrackingCodesFailedException
     */
    public function deleteTrackingCodes(TrackingCodeId ...$ids): void;
    
    
    /**
     * Returns all tracking code, that belong to a specific order ID.
     *
     * @param OrderId $orderId
     *
     * @return TrackingCodes
     */
    public function getTrackingCodesByOrderId(OrderId $orderId): TrackingCodes;
}