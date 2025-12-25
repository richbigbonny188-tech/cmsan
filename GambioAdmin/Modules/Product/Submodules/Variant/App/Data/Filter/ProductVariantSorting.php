<?php
/*--------------------------------------------------------------
   ProductVariantSorting.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\Filter;

use Gambio\Core\Filter\SqlSorting;

/**
 * Class ProductVariantSorting
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\Filter
 */
class ProductVariantSorting extends SqlSorting
{
    
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'             => 'ppc.products_properties_combis_id',
            'combination'    => 'combination', // example value 1-1|2-4
            'sortOrder'      => 'ppc.sort_order',
            'modelNumber'    => 'ppc.combi_model',
            'GTIN'           => 'ppc.gtin',
            'ASIN'           => 'ppc.asin',
            'EAN'            => 'ppc.combi_ean',
            'stockType'      => 'combi_quantity_type', // todo: add to DB table
            'stock'          => 'ppc.combi_quantity',
            'weightType'     => 'ppc.combi_weight_type',
            'weight'         => 'ppc.combi_weight',
            'priceType'      => 'ppc.combi_price_type',
            'price'          => 'ppc.combi_price',
            'vpeScalarValue' => 'ppc.vpe_value',
            'vpeUnitId'      => 'ppc.products_vpe_id',
            'deliveryTimeId' => 'ppc.combi_shipping_status_id',
            'imageListId'    => 'pilc.product_image_list_id',
        ];
    }
}