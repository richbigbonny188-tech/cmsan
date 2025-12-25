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

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Collections\SoldProducts;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\SoldProduct;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\Exceptions\SalesRecordNotFoundException;

/**
 * Interface SoldProductReadService
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services
 */
interface SoldProductReadService
{
    /**
     * Provides sales information about every product with a recorded sale
     *
     * @param int $limit
     * @param int $offset
     *
     * @return SoldProducts
     */
    public function getSoldProducts(int $limit = 25, int $offset = 0): SoldProducts;
    
    
    /**
     * @return int
     */
    public function getSoldProductsTotalCount(): int;
    
    /**
     * Provides sales information about a given product
     *
     * @param int $productId
     *
     * @return SoldProduct
     * @throws SalesRecordNotFoundException
     */
    public function getSoldProductByProductId(int $productId): SoldProduct;
}