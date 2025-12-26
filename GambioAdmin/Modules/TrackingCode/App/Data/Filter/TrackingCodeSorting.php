<?php
/* --------------------------------------------------------------
   TrackingCodeSorting.php 2021-10-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\TrackingCode\App\Data\Filter;

use Gambio\Core\Filter\SqlSorting;

/**
 * Class TrackingCodeSorting
 *
 * @package Gambio\Admin\Modules\TrackingCode\App\Data\Filter
 * @codeCoverageIgnore
 */
class TrackingCodeSorting extends SqlSorting
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'                         => 'orders_parcel_tracking_codes.orders_parcel_tracking_code_id',
            'orderId'                    => 'orders_parcel_tracking_codes.order_id',
            'code'                       => 'orders_parcel_tracking_codes.tracking_code',
            'isReturnDelivery'           => 'orders_parcel_tracking_codes.is_return_delivery',
            'parcelService.id'           => 'orders_parcel_tracking_codes.parcel_service_id',
            'parcelService.languageId'   => 'orders_parcel_tracking_codes.language_id',
            'parcelService.languageCode' => 'languages.code',
            'parcelService.name'         => 'orders_parcel_tracking_codes.parcel_service_name',
            'parcelService.url'          => 'orders_parcel_tracking_codes.url',
            'parcelService.comment'      => 'orders_parcel_tracking_codes.comment',
            'parcelService.shipmentType' => 'orders_parcel_tracking_codes.shipment_type',
            'createdOn'                  => 'orders_parcel_tracking_codes.creation_date',
        ];
    }
}