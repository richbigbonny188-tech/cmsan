<?php
/* --------------------------------------------------------------
   ViewedProducts.php 2023-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\Collections;

use ArrayIterator;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\PaginationMeta;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\ViewedProduct;
use IteratorAggregate;

/**
 * Class ViewedProducts
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\Collections
 */
class ViewedProducts implements IteratorAggregate
{
    /**
     * @var ViewedProduct[]
     */
    private array $viewedProducts;
    
    
    /**
     * ViewedProducts constructor.
     *
     * @param PaginationMeta $paginationMeta
     * @param ViewedProduct  ...$viewedProducts
     */
    public function __construct(private PaginationMeta $paginationMeta, ViewedProduct ...$viewedProducts)
    {
        $this->viewedProducts = $viewedProducts;
    }
    
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        $callback = static fn(ViewedProduct $viewedProduct): array => $viewedProduct->toArray();
        
        return [
            'products' => array_map($callback, $this->viewedProducts),
            '_meta'    => [
                'pagination' => $this->paginationMeta->toArray(),
            ],
        ];
    }
    
    
    /**
     * @inheritDoc
     * @return ViewedProduct[]|iterable
     */
    public function getIterator(): iterable
    {
        return new ArrayIterator($this->viewedProducts);
    }
}