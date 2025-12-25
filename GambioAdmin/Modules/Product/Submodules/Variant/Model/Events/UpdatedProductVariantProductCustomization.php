<?php
/*--------------------------------------------------------------
   UpdatedProductVariantProductCustomization.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductCustomization;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class UpdatedProductVariantProductCustomization
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class UpdatedProductVariantProductCustomization
{
    /**
     * UpdatedProductVariantProductCustomization constructor.
     *
     * @param ProductVariantId     $variantId
     * @param ProductCustomization $productCustomization
     */
    private function __construct(
        protected ProductVariantId $variantId,
        protected ProductCustomization $productCustomization
    ) {
    }
    
    
    /**
     * @param ProductVariantId     $variantId
     * @param ProductCustomization $productCustomization
     *
     * @return UpdatedProductVariantProductCustomization
     */
    public static function create(
        ProductVariantId     $variantId,
        ProductCustomization $productCustomization
    ): UpdatedProductVariantProductCustomization {
        return new static($variantId, $productCustomization);
    }
    
    
    /**
     * @return ProductCustomization
     */
    public function productCustomization(): ProductCustomization
    {
        return $this->productCustomization;
    }
    
    
    /**
     * @return ProductVariantId
     */
    public function variantId(): ProductVariantId
    {
        return $this->variantId;
    }
}