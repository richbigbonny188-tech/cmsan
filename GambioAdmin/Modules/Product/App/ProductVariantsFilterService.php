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

namespace Gambio\Admin\Modules\Product\App;

use Gambio\Admin\Modules\Product\Services\ProductVariantsFilterService as ProductVariantsFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services as Submodule;

/**
 * Class ProductVariantsFilterService
 *
 * @package Gambio\Admin\Modules\Product\App
 */
class ProductVariantsFilterService implements ProductVariantsFilterServiceInterface
{
    public function __construct(private Submodule\ProductVariantsFilterService $service) { }
    
    
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