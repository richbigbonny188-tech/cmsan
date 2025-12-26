<?php
/*--------------------------------------------------------------
   SoldProductFactory.php 2023-09-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services;

use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Collections\SoldProducts;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\Entities\Category;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\SoldProduct;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\CategoryId;
use Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Model\ValueObjects\ProductId;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class SoldProductFactory
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\SoldProduct\Services
 */
class SoldProductFactory
{
    /**
     * @param SoldProduct ...$products
     *
     * @return SoldProducts
     */
    public function createSoldProducts(SoldProduct ...$products): SoldProducts
    {
       return SoldProducts::create(...$products);
    }
    
    
    /**
     * @param CategoryId $id
     * @param string     $name
     *
     * @return Category
     */
    public function createCategory(CategoryId $id, string $name): Category
    {
       return Category::create($id, $name);
    }
    
    
    /**
     * @param int $id
     *
     * @return CategoryId
     */
    public function createCategoryId(int $id): CategoryId
    {
        return CategoryId::create($id);
    }
    
    
    /**
     * @param int $id
     *
     * @return ProductId
     */
    public function createProductId(int $id): ProductId
    {
        return ProductId::create($id);
    }
    
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return SqlPagination
     */
    public function createPagination(int $limit, int $offset): SqlPagination
    {
        return SqlPagination::createWithLimitAndOffset($limit, $offset);
    }
}