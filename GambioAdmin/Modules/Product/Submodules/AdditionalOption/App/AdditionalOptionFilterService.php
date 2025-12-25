<?php
/* --------------------------------------------------------------
  AdditionalOptionFilterService.php 2023-06-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Collections\AdditionalOptions;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFactory;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFilterFactory;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionFilterService as AdditionalOptionFilterServiceInterface;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services\AdditionalOptionRepository as AdditionalOptionRepositoryInterface;

/**
 * Class AdditionalOptionFilterService
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\App;
 */
class AdditionalOptionFilterService implements AdditionalOptionFilterServiceInterface
{
    /**
     * AdditionalOptionFilterService constructor.
     *
     * @param AdditionalOptionFilterFactory       $filterFactory
     * @param AdditionalOptionRepositoryInterface $repository
     * @param AdditionalOptionFactory             $domainFactory
     */
    public function __construct(
        private AdditionalOptionFilterFactory       $filterFactory,
        private AdditionalOptionRepositoryInterface $repository,
        private AdditionalOptionFactory             $domainFactory
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function filterAdditionalOptions(
        int     $productId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): AdditionalOptions {
        $productIdObj = $this->domainFactory->createProductId($productId);
        $filtersObj   = $this->filterFactory->createFilters($filters);
        $sortingObj   = $this->filterFactory->createSorting($sorting);
        $pagination   = $this->filterFactory->createPagination($limit, $offset);
        
        return $this->repository->filterAdditionalOptions($productIdObj, $filtersObj, $sortingObj, $pagination);
    }
    
    
    /**
     * @inheritDoc
     */
    public function getAdditionalOptionsTotalCount(int $productId, array $filters): int
    {
        $productIdObj = $this->domainFactory->createProductId($productId);
        $filtersObj   = $this->filterFactory->createFilters($filters);
        
        return $this->repository->getAdditionalOptionsTotalCount($productIdObj, $filtersObj);
    }
}