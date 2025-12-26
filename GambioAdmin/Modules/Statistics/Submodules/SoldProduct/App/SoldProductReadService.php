<?php
/*--------------------------------------------------------------
   SoldProductReadService.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Collections\SoldProducts;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\SoldProduct;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductFactory;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductReadService as SoldProductReadServiceInterface;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductRepository as SoldProductRepositoryInterface;

/**
 * Class SoldProductReadService
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App
 */
class SoldProductReadService implements SoldProductReadServiceInterface
{
    
    /**
     * @param SoldProductFactory             $factory
     * @param SoldProductRepositoryInterface $repository
     */
    public function __construct(
        private SoldProductFactory             $factory,
        private SoldProductRepositoryInterface $repository
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSoldProducts(int $limit = 25, int $offset = 0): SoldProducts
    {
        return $this->repository->getSoldProducts($this->factory->createPagination($limit, $offset));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSoldProductsTotalCount(): int
    {
        return $this->repository->getSoldProductsTotalCount();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSoldProductByProductId(int $productId): SoldProduct
    {
        return $this->repository->getSoldProductByProductId($this->factory->createProductId($productId));
    }
}