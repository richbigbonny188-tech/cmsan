<?php
/*--------------------------------------------------------------
   UpdatedProductVariantProductIdentificationNumbers.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductIdentificationNumbers;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class UpdatedProductVariantProductIdentificationNumbers
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class UpdatedProductVariantProductIdentificationNumbers
{
    /**
     * UpdatedProductVariantProductIdentificationNumbers constructor.
     *
     * @param ProductVariantId             $variantId
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     */
    private function __construct(
        private ProductVariantId             $variantId,
        private ProductIdentificationNumbers $productIdentificationNumbers
    ) {
    }
    
    
    /**
     * @param ProductVariantId             $variantId
     * @param ProductIdentificationNumbers $productIdentificationNumbers
     *
     * @return UpdatedProductVariantProductIdentificationNumbers
     */
    public static function create(
        ProductVariantId             $variantId,
        ProductIdentificationNumbers $productIdentificationNumbers
    ): UpdatedProductVariantProductIdentificationNumbers {
        return new static($variantId, $productIdentificationNumbers);
    }
    
    
    /**
     * @return ProductIdentificationNumbers
     */
    public function productIdentificationNumbers(): ProductIdentificationNumbers
    {
        return $this->productIdentificationNumbers;
    }
    
    
    /**
     * @return ProductVariantId
     */
    public function variantId(): ProductVariantId
    {
        return $this->variantId;
    }
}