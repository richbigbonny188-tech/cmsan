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

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App;

use Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\Filter\ProductVariantFilterFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantFactory;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsFilterService as ProductVariantsFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Variant\Services\ProductVariantsRepository;

/**
 * Class ProductVariantsFilterService
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\Data
 */
class ProductVariantsFilterService implements ProductVariantsFilterServiceInterface
{
    /**
     * ProductVariantsFilterService constructor.
     *
     * @param ProductVariantFilterFactory $filterFactory
     * @param ProductVariantsRepository $repository
     * @param ProductVariantFactory $domainFactory
     */
    public function __construct(
        private ProductVariantFilterFactory $filterFactory,
        private ProductVariantsRepository   $repository,
        private ProductVariantFactory       $domainFactory
    )
    {
    }


    /**
     * @inheritDoc
     */
    public function filterProductVariants(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): ProductVariants
    {
        $productIdObj = $this->domainFactory->createProductId($productId);
        $filtersObj = $this->filterFactory->createFilters($filters);
        $sortingObj = $this->filterFactory->createSorting($sorting);
        $pagination = $this->filterFactory->createPagination($limit, $offset);

        return $this->repository->filterProductVariants($productIdObj, $filtersObj, $sortingObj, $pagination);
    }


    /**
     * @inheritDoc
     */
    public function getProductVariantsTotalCount(int $productId, array $filters): int
    {
        $productIdObj = $this->domainFactory->createProductId($productId);
        $filtersObj = $this->filterFactory->createFilters($filters);

        return $this->repository->getProductVariantsTotalCount($productIdObj, $filtersObj);
    }
}