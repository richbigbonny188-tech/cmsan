<?php
/* --------------------------------------------------------------
   TrackingCodeFactory.php 2021-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\Services;

use DateTime;
use Exception;
use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodeIds;
use Gambio\Admin\Modules\TrackingCode\Model\Collections\TrackingCodes;
use Gambio\Admin\Modules\TrackingCode\Model\TrackingCode;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\OrderId;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\ParcelServiceDetails;
use Gambio\Admin\Modules\TrackingCode\Model\ValueObjects\TrackingCodeId;
use InvalidArgumentException;

/**
 * Class TrackingCodeFactory
 *
 * @package Gambio\Admin\Modules\TrackingCode\Services
 */
class TrackingCodeFactory
{
    /**
     * Creates and returns a tracking code ID.
     *
     * @param int $id
     *
     * @return TrackingCodeId
     */
    public function createTrackingCodeId(int $id): TrackingCodeId
    {
        return TrackingCodeId::create($id);
    }
    
    
    /**
     * Creates and returns a collection of tracking code IDs.
     *
     * @param TrackingCodeId ...$ids
     *
     * @return TrackingCodeIds
     */
    public function createTrackingCodeIds(TrackingCodeId ...$ids): TrackingCodeIds
    {
        return TrackingCodeIds::create(...$ids);
    }
    
    
    /**
     * Creates and returns a tracking code.
     *
     * @param int                  $id
     * @param int                  $orderId
     * @param string               $code
     * @param ParcelServiceDetails $parcelServiceDetails
     * @param string               $createdOn
     * @param bool                 $isReturnDelivery
     *
     * @return TrackingCode
     */
    public function createTrackingCode(
        int                  $id,
        int                  $orderId,
        string               $code,
        ParcelServiceDetails $parcelServiceDetails,
        string               $createdOn,
        bool                 $isReturnDelivery
    ): TrackingCode {
        try {
            $createdOnObj = new DateTime($createdOn);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Given "created on" datetime format is invalid.');
        }
        
        return TrackingCode::create($this->createTrackingCodeId($id),
                                    $this->createOrderId($orderId),
                                    $code,
                                    $parcelServiceDetails,
                                    $createdOnObj,
                                    $isReturnDelivery);
    }
    
    
    /**
     * Creates and returns a collection of tracking codes.
     *
     * @param TrackingCode ...$trackingCodes
     *
     * @return TrackingCodes
     */
    public function createTrackingCodes(TrackingCode ...$trackingCodes): TrackingCodes
    {
        return TrackingCodes::create(...$trackingCodes);
    }
    
    
    /**
     * Creates and returns a order ID.
     *
     * @param int $id
     *
     * @return OrderId
     */
    public function createOrderId(int $id): OrderId
    {
        return OrderId::create($id);
    }
    
    
    /**
     * Creates and returns tracking code details.
     *
     * @param int    $parcelServiceId
     * @param string $languageCode
     * @param string $name
     * @param string $url
     * @param string $comment
     * @param string $shipmentType
     *
     * @return ParcelServiceDetails
     */
    public function createParcelServiceDetails(
        int    $parcelServiceId,
        string $languageCode,
        string $name,
        string $url,
        string $comment,
        string $shipmentType
    ): ParcelServiceDetails {
        return ParcelServiceDetails::create($parcelServiceId, $languageCode, $name, $url, $comment, $shipmentType);
    }
}