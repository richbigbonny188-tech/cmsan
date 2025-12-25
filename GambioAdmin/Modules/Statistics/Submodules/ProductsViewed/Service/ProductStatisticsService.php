<?php
/* --------------------------------------------------------------
   ProductStatisticsService.php 2023-04-25
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Service;

use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\Collections\ViewedProducts;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Model\ValueObjects\Pagination;
use Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Service\Exceptions\RetrieveViewedProductsFailedException;

/**
 * Interface ProductStatisticsService
 *
 * @package Gambio\Admin\Modules\Statistics\Submodules\ProductsViewed\Service
 */
interface ProductStatisticsService
{
    /**
     * Returns paginated statistic data about viewed products.
     * Most frequently viewed products appear first in the collection.
     *
     * Throws exception if retrieving viewed product failed. e.g. due to unavailable database.
     * The exception only occurs on rare edge cases and if so and used in HTTP context, it should
     * return a 500 status code.
     *
     * @param Pagination $pagination
     *
     * @return ViewedProducts
     * @throws RetrieveViewedProductsFailedException
     */
    public function getProductViewStatistic(Pagination $pagination): ViewedProducts;
}