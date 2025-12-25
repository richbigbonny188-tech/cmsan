<?php
/*--------------------------------------------------------------
   SoldProductRepository.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Data\SoldProductMapper;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Data\SoldProductReader;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Collections\SoldProducts;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\SoldProduct;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductRepository as SoldProductRepositoryInterface;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class SoldProductRepository
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App
 */
class SoldProductRepository implements SoldProductRepositoryInterface
{
    
    /**
     * @param SoldProductReader $reader
     * @param SoldProductMapper $mapper
     */
    public function __construct(
        private SoldProductReader $reader,
        private SoldProductMapper $mapper
    ) {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSoldProducts(SqlPagination $pagination): SoldProducts
    {
        return $this->mapper->mapSoldProducts(...$this->reader->getSoldProducts($pagination));
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSoldProductsTotalCount(): int
    {
        return $this->reader->getSoldProductsTotalCount();
    }
    
    
    /**
     * @inheritDoc
     */
    public function getSoldProductByProductId(ProductId $productId): SoldProduct
    {
        return $this->mapper->mapSoldProduct($this->reader->getSoldProductByProductId($productId));
    }
}