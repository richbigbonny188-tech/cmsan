<?php
/* --------------------------------------------------------------
   ParcelServiceSorting.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App\Data\Filter;

use Gambio\Core\Filter\SqlSorting;

/**
 * Class ParcelServiceSorting
 *
 * @package Gambio\Admin\Modules\ParcelService\App\Data\Filter
 * @codeCoverageIgnore
 */
class ParcelServiceSorting extends SqlSorting
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'                   => 'parcel_services.parcel_service_id',
            'name'                 => 'parcel_services.name',
            'shipmentType'         => 'parcel_services.shipment_type',
            'isDefault'            => 'parcel_services.default',
            'descriptions.url'     => 'parcel_services_description.url',
            'descriptions.comment' => 'parcel_services_description.comment',
        ];
    }
}