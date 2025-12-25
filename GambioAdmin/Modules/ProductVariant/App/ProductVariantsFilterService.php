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

namespace Gambio\Admin\Modules\ProductVariant\App;

use Gambio\Admin\Modules\ProductVariant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\ProductVariant\Services\ProductVariantsFilterService as ProductVariantsFilterServiceInterface;
use Gambio\Admin\Modules\Product\Services as Product;

/**
 * Class ProductVariantsFilterService
 *
 * @package Gambio\Admin\Modules\ProductVariant\App\Data
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Services\ProductVariantsFilterService
 */
class ProductVariantsFilterService implements ProductVariantsFilterServiceInterface
{
    public function __construct(private Product\ProductVariantsFilterService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function filterProductVariants(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): ProductVariants {
        return $this->service->filterProductVariants($productId,
                                                     $filters,
                                                     $sorting,
                                                     $limit,
                                                     $offset);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductVariantsTotalCount(int $productId, array $filters): int
    {
        return $this->service->getProductVariantsTotalCount($productId, $filters);
    }
}