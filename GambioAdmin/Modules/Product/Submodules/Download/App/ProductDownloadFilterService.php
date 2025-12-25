<?php
/* --------------------------------------------------------------
  ProductDownloadFilterService.php 2023-06-26
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Download\App;

use Gambio\Admin\Modules\Product\Submodules\Download\App\Data\Filter\ProductDownloadFilterFactory;
use Gambio\Admin\Modules\Product\Submodules\Download\Model\Collections\ProductDownloads;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFactory;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadFilterService as ProductDownloadFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\Download\Services\ProductDownloadRepository as ProductDownloadRepositoryInterface;

/**
 * Class ProductDownloadFilterService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Download\App
 */
class ProductDownloadFilterService implements ProductDownloadFilterServiceInterface
{
    /**
     * ProductOptionFilterService constructor.
     *
     * @param ProductDownloadFilterFactory       $filterFactory
     * @param ProductDownloadRepositoryInterface $repository
     * @param ProductDownloadFactory             $domainFactory
     */
    public function __construct(
        private ProductDownloadFilterFactory       $filterFactory,
        private ProductDownloadRepositoryInterface $repository,
        private ProductDownloadFactory             $domainFactory
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterProductDownloads(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): ProductDownloads {
        $productIdObj = $this->domainFactory->createProductId($productId);
        $filtersObj   = $this->filterFactory->createFilters($filters);
        $sortingObj   = $this->filterFactory->createSorting($sorting);
        $pagination   = $this->filterFactory->createPagination($limit, $offset);
        
        return $this->repository->filterProductDownloads($productIdObj, $filtersObj, $sortingObj, $pagination);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductDownloadsTotalCount(int $productId, array $filters): int
    {
        $productIdObj = $this->domainFactory->createProductId($productId);
        $filtersObj   = $this->filterFactory->createFilters($filters);
        
        return $this->repository->getProductDownloadsTotalCount($productIdObj, $filtersObj);
    }
}