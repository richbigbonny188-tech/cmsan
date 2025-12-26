<?php
/*--------------------------------------------------------------
   ProductVariantDeleted.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ValueObjects\ProductVariantId;

/**
 * Class ProductVariantDeleted
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class ProductVariantDeleted
{
    /**
     * ProductVariantDeleted constructor.
     *
     * @param ProductVariantId $variantId
     */
    public function __construct(protected ProductVariantId $variantId) { }
    
    
    /**
     * @param ProductVariantId $variantId
     *
     * @return ProductVariantDeleted
     */
    public static function create(ProductVariantId $variantId): ProductVariantDeleted
    {
        return new static($variantId);
    }
    
    
    /**
     * @return ProductVariantId
     */
    public function variantId(): ProductVariantId
    {
        return $this->variantId;
    }
}