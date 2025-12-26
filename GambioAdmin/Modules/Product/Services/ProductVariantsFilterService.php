<?php
/*--------------------------------------------------------------
   ProductVariantsFilterService.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Services;

use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;

/**
 * Interface ProductVariantsFilterService
 *
 * @package Gambio\Admin\Modules\Product\Services
 */
interface ProductVariantsFilterService
{
    /**
     * @param int $productId
     * @param array $filters
     * @param string|null $sorting
     * @param int $limit
     * @param int $offset
     *
     * @return ProductVariants
     */
    public function filterProductVariants(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): ProductVariants;
    
    
    /**
     * @param int $productId
     * @param array $filters
     *
     * @return int
     */
    public function getProductVariantsTotalCount(int $productId, array $filters): int;
}