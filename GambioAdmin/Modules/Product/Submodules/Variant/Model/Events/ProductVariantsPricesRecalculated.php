<?php
/*--------------------------------------------------------------
   ProductVariantsPricesRecalculated.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariantIds;

/**
 * Class ProductVariantsPricesRecalculated
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class ProductVariantsPricesRecalculated
{
    /**
     * ProductVariantsPricesRecalculated constructor.
     *
     * @param ProductVariantIds $variantIds
     */
    public function __construct(protected ProductVariantIds $variantIds) { }
    
    
    /**
     * @param ProductVariantIds $variantIds
     *
     * @return ProductVariantsPricesRecalculated
     */
    public static function create(ProductVariantIds $variantIds): ProductVariantsPricesRecalculated
    {
        return new static($variantIds);
    }
    
    
    /**
     * @return ProductVariantIds
     */
    public function variantIds(): ProductVariantIds
    {
        return $this->variantIds;
    }
}