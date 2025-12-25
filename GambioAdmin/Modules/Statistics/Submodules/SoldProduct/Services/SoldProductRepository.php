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

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Collections\SoldProducts;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\SoldProduct;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\ProductId;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\Exceptions\SalesRecordNotFoundException;
use Gambio\Core\Filter\SqlPagination;

/**
 * Interface SoldProductRepository
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services
 */
interface SoldProductRepository
{
    /**
     * Provides sales information about every product with a recorded sale
     *
     * @param SqlPagination $pagination
     *
     * @return SoldProducts
     */
    public function getSoldProducts(SqlPagination $pagination): SoldProducts;
    
    /**
     * @return int
     */
    public function getSoldProductsTotalCount(): int;
    
    /**
     * Provides sales information about a given product
     *
     * @param ProductId $productId
     *
     * @return SoldProduct
     * @throws SalesRecordNotFoundException
     */
    public function getSoldProductByProductId(ProductId $productId): SoldProduct;
}