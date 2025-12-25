<?php
/* --------------------------------------------------------------
  ProductOptionFilterService.php 2023-06-20
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ProductOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFilterService;
use Gambio\Admin\Modules\ProductOption\App\Data\Filter\ProductOptionFilterFactory;
use Gambio\Admin\Modules\ProductOption\Model\Collections\ProductOptions;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionFactory;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionFilterService as ProductOptionFilterServiceInterface;
use Gambio\Admin\Modules\ProductOption\Services\ProductOptionRepository as ProductOptionRepositoryInterface;

/**
 * Class ProductOptionFilterService
 *
 * @package    Gambio\Admin\Modules\ProductOption\App
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains were refactored into
 *             submodules too. This class will be deleted with 4.11. Migrate usage of this class to
 *             \Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFilterService
 */
class ProductOptionFilterService implements ProductOptionFilterServiceInterface
{
    public function __construct(private AdditionalOptionFilterService $service) { }
    
    
    /**
     * @inheritDoc
     */
    public function filterProductOptions(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): ProductOptions {
        return $this->service->filterAdditionalOptions($productId,
                                                       $filters,
                                                       $sorting,
                                                       $limit,
                                                       $offset);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductOptionsTotalCount(int $productId, array $filters): int
    {
        return $this->service->getAdditionalOptionsTotalCount($productId, $filters);
    }
}