<?php
/* --------------------------------------------------------------
   ProductsViewCountService.php 2023-04-26
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App;

use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App\Data\ViewedProductsRepository;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\Collections\ViewedProducts;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\Pagination;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Service\ProductStatisticsService;

/**
 * Class ProductsViewCountService
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\App
 */
class ProductsViewCountService implements ProductStatisticsService
{
    /**
     * ProductsViewCountService constructor.
     *
     * @param ViewedProductsRepository $repository
     */
    public function __construct(private ViewedProductsRepository $repository)
    {
    }
    
    
    /**
     * @inheritDoc
     */
    public function getProductViewStatistic(Pagination $pagination): ViewedProducts
    {
        return $this->repository->getViewedProducts($pagination);
    }
}