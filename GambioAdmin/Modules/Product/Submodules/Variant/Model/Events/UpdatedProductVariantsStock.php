<?php
/*--------------------------------------------------------------
   UpdatedProductVariantsStock.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantStock;

/**
 * Class UpdatedProductVariantsStock
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class UpdatedProductVariantsStock
{
    /**
     * UpdatedProductVariantsStock constructor.
     *
     * @param ProductVariantId    $variantId
     * @param ProductVariantStock $stock
     */
    private function __construct(private ProductVariantId $variantId, private ProductVariantStock $stock)
    {
    }
    
    
    /**
     * @param ProductVariantId    $variantId
     * @param ProductVariantStock $stock
     *
     * @return UpdatedProductVariantsStock
     */
    public static function create(ProductVariantId $variantId, ProductVariantStock $stock): UpdatedProductVariantsStock
    {
        return new static($variantId, $stock);
    }
    
    
    /**
     * @return ProductVariantId
     */
    public function variantId(): ProductVariantId
    {
        return $this->variantId;
    }
    
    
    /**
     * @return float
     */
    public function stock(): float
    {
        return $this->stock->stock();
    }
    
    
    /**
     * @return string
     */
    public function stockType(): string
    {
        return $this->stock->stockType();
    }
}