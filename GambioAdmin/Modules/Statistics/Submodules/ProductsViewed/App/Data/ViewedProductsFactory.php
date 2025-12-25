<?php
/* --------------------------------------------------------------
   ViewedProductsFactory.php 2023-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data;

use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\Collections\ViewedProducts;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\PaginationMeta;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\ViewedProduct;

/**
 *
 * ViewedProductsFactory is a class responsible for creating instances of ViewedProduct and ViewedProducts objects.
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data
 */
class ViewedProductsFactory
{
    /**
     * Creates a new ViewedProduct object with the given name, language, and view count.
     *
     * @param int    $id       The id of the viewed product.
     * @param string $name     The name of the viewed product.
     * @param string $language The language of the viewed product.
     * @param int    $views    The number of views for the viewed product.
     *
     * @return ViewedProduct The newly created ViewedProduct object.
     */
    public function createViewedProduct(int $id, string $name, string $language, int $views): ViewedProduct
    {
        return new ViewedProduct($id, $name, $language, $views);
    }
    
    
    /**
     * Creates a new ViewedProducts object with the given pagination meta data and viewed products.
     *
     * @param PaginationMeta $paginationMeta    The pagination meta data for the viewed products.
     * @param ViewedProduct  ...$viewedProducts A variable number of viewed products to add to the ViewedProducts
     *                                          object.
     *
     * @return ViewedProducts The newly created ViewedProducts object.
     */
    public function createViewedProducts(
        PaginationMeta $paginationMeta,
        ViewedProduct  ...$viewedProducts
    ): ViewedProducts {
        return new ViewedProducts($paginationMeta, ...$viewedProducts);
    }
    
    
    /**
     * Creates a new PaginationMeta object with the given pagination data.
     *
     * @param int $page       The current page number.
     * @param int $perPage    The number of items per page.
     * @param int $totalItems The total number of items.
     *
     * @return PaginationMeta The newly created PaginationMeta object.
     */
    public function createPaginationMeta(int $page, int $perPage, int $totalItems): PaginationMeta
    {
        return new PaginationMeta($page, $perPage, $totalItems);
    }
}