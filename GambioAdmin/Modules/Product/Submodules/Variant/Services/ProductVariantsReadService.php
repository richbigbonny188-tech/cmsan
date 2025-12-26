<?php
/*------------------------------------------------------------------------------
 ProductVariantsReadService.php 2023-06-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2023 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Product\Submodules\Variant\Services;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Exceptions\ProductVariantDoesNotExist;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;

/**
 * Interface ProductVariantsReadService
 *
 * @package    Gambio\Admin\Modules\Product\Submodules\Variant\Services
 */
interface ProductVariantsReadService
{
    /**
     * @param int $productId
     *
     * @return ProductVariants
     */
    public function getProductVariantsByProductId(int $productId): ProductVariants;
    
    
    /**
     * @param int $variantId
     *
     * @return ProductVariant
     *
     * @throws ProductVariantDoesNotExist
     */
    public function getProductVariantById(int $variantId): ProductVariant;
}