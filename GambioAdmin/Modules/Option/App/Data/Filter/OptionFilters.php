<?php
/* --------------------------------------------------------------
   OptionFilters.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Data\Filter;

use Gambio\Core\Filter\SqlFilters;

/**
 * Class OptionFilters
 *
 * @package Gambio\Admin\Modules\Option\App\Data\Filter
 */
class OptionFilters extends SqlFilters
{
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'                           => 'properties.properties_id',
            'type'                         => 'properties.display_type',
            'sortOrder'                    => 'properties.sort_order',
            'details.languageCode'         => 'languages.code',
            'details.label'                => 'properties_description.properties_name',
            'details.adminLabel'           => 'properties_description.properties_admin_name',
            'details.description'          => 'properties_description.description',
            'values.id'                    => 'properties_values.properties_values_id',
            'values.sortOrder'             => 'properties_values.sort_order',
            'values.image'                 => 'properties_values.display_image',
            'values.modelNumber'           => 'properties_values.value_model',
            'values.weightType'            => 'properties_values.weight_type',
            'values.weight'                => 'properties_values.weight',
            'values.priceType'             => 'properties_values.price_type',
            'values.price'                 => 'properties_values.value_price',
            'values.stockType'             => 'properties_values.stock_type',
            'values.stock'                 => 'properties_values.stock',
            'values.stockCentrallyManaged' => 'properties_values.stock_centrally_managed',
            'values.details.languageCode'  => 'languages2.code',
            'values.details.label'         => 'properties_values_description.values_name',
            'values.details.description'   => 'properties_values_description.description',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForLikeOperation(): array
    {
        return [
            'type',
            'details.languageCode',
            'details.label',
            'details.adminLabel',
            'details.description',
            'values.image',
            'values.modelNumber',
            'values.weightType',
            'values.priceType',
            'values.stockType',
            'values.details.languageCode',
            'values.details.label',
            'values.details.description',
        ];
    }
    
    
    /**
     * @inheritDoc
     */
    public static function attributesForNumericOperations(): array
    {
        return [
            'id',
            'sortOrder',
            'values.id',
            'values.sortOrder',
            'values.weight',
            'values.price',
            'values.stock',
            'values.stockCentrallyManaged',
        ];
    }
}