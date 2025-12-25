<?php
/*--------------------------------------------------------------
   UpdatedProductVariantsSortOrder.php 2023-06-27
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
 * Class UpdatedProductVariantsSortOrder
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\Model\Events
 * @codeCoverageIgnore
 */
class UpdatedProductVariantsSortOrder
{
    /**
     * UpdatedProductVariantsSortOrder constructor.
     *
     * @param ProductVariantId $variantId
     * @param int              $sortOrder
     */
    private function __construct(private ProductVariantId $variantId, private int $sortOrder)
    {
    }
    
    
    /**
     * @param ProductVariantId $variantId
     * @param int              $sortOrder
     *
     * @return UpdatedProductVariantsSortOrder
     */
    public static function create(ProductVariantId $variantId, int $sortOrder): UpdatedProductVariantsSortOrder
    {
        return new static($variantId, $sortOrder);
    }
    
    
    /**
     * @return ProductVariantId
     */
    public function variantId(): ProductVariantId
    {
        return $this->variantId;
    }
    
    
    /**
     * @return int
     */
    public function sortOrder(): int
    {
        return $this->sortOrder;
    }
}