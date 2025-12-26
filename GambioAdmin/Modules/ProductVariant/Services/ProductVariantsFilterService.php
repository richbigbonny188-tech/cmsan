<?php
/*--------------------------------------------------------------
   ProductVariantsFilterService.php 2020-03-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductVariant\Services;

use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariants;

/**
 * Interface ProductVariantsFilterService
 * @package Gambio\Admin\Modules\ProductVariant\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Services\ProductVariantsFilterService
 */
interface ProductVariantsFilterService
{
    /**
     * @param int         $productId
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return ProductVariants
     */
    public function filterProductVariants(
        int $productId,
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): ProductVariants;
    
    
    /**
     * @param int   $productId
     * @param array $filters
     *
     * @return int
     */
    public function getProductVariantsTotalCount(int $productId, array $filters): int;
}