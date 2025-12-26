<?php
/* --------------------------------------------------------------
   ViewedProductsRepository.php 2023-04-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data;

use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Exceptions\UnexpectedViewedProductsDbException;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\Collections\ViewedProducts;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\Pagination;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Service\Exceptions\RetrieveViewedProductsFailedException;

/**
 * Class ViewedProductsRepository
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data
 */
class ViewedProductsRepository
{
    /**
     * ViewedProductsRepository constructor.
     *
     * @param ViewedProductsReader  $reader
     * @param ViewedProductsFactory $factory
     */
    public function __construct(private ViewedProductsReader $reader, private ViewedProductsFactory $factory)
    {
    }
    
    
    /**
     * Returns a collection of viewed products, ordered by products with most view count.
     *
     * @param Pagination $pagination
     *
     * @return ViewedProducts
     * @throws RetrieveViewedProductsFailedException
     */
    public function getViewedProducts(Pagination $pagination): ViewedProducts
    {
        try {
            $data           = $this->reader->fetchData($pagination);
            $viewedProducts = [];
            foreach ($data as $dataset) {
                $id       = (int)$dataset['id'];
                $name     = $dataset['name'];
                $language = $dataset['language'];
                $views    = (int)$dataset['views'];
                
                $viewedProducts[] = $this->factory->createViewedProduct($id, $name, $language, $views);
            }
            
            $total          = $this->reader->fetchTotal();
            $paginationMeta = $this->factory->createPaginationMeta($pagination->currentPage(),
                                                                   $pagination->itemsPerPage(),
                                                                   $total);
            
            return $this->factory->createViewedProducts($paginationMeta, ...$viewedProducts);
        } catch (UnexpectedViewedProductsDbException $e) {
            $message = "Data for visited products could not be retrieved. {$e->getMessage()}";
            throw new RetrieveViewedProductsFailedException($message, previous: $e);
        }
    }
}