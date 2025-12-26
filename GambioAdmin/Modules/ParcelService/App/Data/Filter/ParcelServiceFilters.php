<?php
/* --------------------------------------------------------------
   ParcelServiceCriteria.php 2021-10-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App\Data\Filter;

use Gambio\Core\Filter\SqlFilters;

/**
 * Class ParcelServiceFilters
 *
 * @package Gambio\Admin\Modules\ParcelService\App\Data\Filter
 * @codeCoverageIgnore
 */
class ParcelServiceFilters extends SqlFilters
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'                        => 'parcel_services.parcel_service_id',
            'name'                      => 'parcel_services.name',
            'isDefault'                 => 'parcel_services.default',
            'shipmentType'              => 'parcel_services.shipment_type',
            'descriptions.languageCode' => 'languages.code',
            'descriptions.url'          => 'parcel_services_description.url',
            'descriptions.comment'      => 'parcel_services_description.comment',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForLikeOperation(): array
    {
        return [
            'name',
            'shipmentType',
            'descriptions.languageCode',
            'descriptions.url',
            'descriptions.comment',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForNumericOperations(): array
    {
        return [
            'id',
        ];
    }
}