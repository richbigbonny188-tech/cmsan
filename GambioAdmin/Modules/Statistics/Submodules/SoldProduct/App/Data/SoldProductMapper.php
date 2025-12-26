<?php
/*--------------------------------------------------------------
   SoldProductMapper.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Data;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Collections\SoldProducts;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\SoldProduct;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services\SoldProductFactory;

/**
 * Class SoldProductMapper
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\App\Data
 */
class SoldProductMapper extends SoldProductFactory
{
    /**
     * @param array ...$data
     *
     * @return SoldProducts
     */
    public function mapSoldProducts(array ...$data): SoldProducts
    {
        return $this->createSoldProducts(...array_map([$this, 'mapSoldProduct'], $data));
    }
    
    
    /**
     * @param array $data
     *
     * @return SoldProduct
     */
    public function mapSoldProduct(array $data): SoldProduct
    {
        $productId  = $this->createProductId((int)$data['products_id']);
        $orderCount = (float)$data['products_ordered'];
        $name       = $data['products_name'];
        $categoryId = $this->createCategoryId((int)$data['categories_id']);
        $category   = $this->createCategory($categoryId, $data['categories_name']);
        
        return SoldProduct::create($productId, $orderCount, $name, $category);
    }
}