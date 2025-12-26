<?php
/*--------------------------------------------------------------------
 ProductOptionFilterService.php 2021-04-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2021 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\Services;

use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptions;

/**
 * Interface ProductOptionFilterService
 * @package Gambio\Admin\Modules\ProductOption\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This interface will be deleted with 4.11. Migrate usage of this interface to
 *             \Gambio\Admin\Modules\Product\Services\Proxies\AdditionalOptionFilterServiceProxy
 */
interface ProductOptionFilterService
{
    /**
     * @param int         $productId
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return ProductOptions
     */
    public function filterProductOptions(
        int $productId,
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): ProductOptions;
    
    
    /**
     * @param int   $productId
     * @param array $filters
     *
     * @return int
     */
    public function getProductOptionsTotalCount(int $productId, array $filters): int;
}