<?php
/* --------------------------------------------------------------
  ProductOptionSorting.php 2023-06-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App\Data\Filter;

use Gambio\Core\Filter\SqlSorting;

/**
 * Class ProductOptionSorting
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App\Data\Filter
 */
class ProductDownloadSorting extends SqlSorting
{
    
    /**
     * @inheritDoc
     */
    public static function attributeColumnMapping(): array
    {
        return [
            'id'            => 'products_attributes_id',
            'productId'     => 'products_id',
            'optionId'      => 'options_id',
            'optionValueId' => 'option_value_id',
            'imageListId'   => 'product_image_list_id',
            'modelNumber'   => 'attributes_model',
            'weight'        => 'options_values_weight',
            'price'         => 'options_values_price',
            'stockType'     => 'stock_type',
            'stock'         => 'attributes_stock',
            'sortOrder'     => 'sortorder',
        ];
    }
}