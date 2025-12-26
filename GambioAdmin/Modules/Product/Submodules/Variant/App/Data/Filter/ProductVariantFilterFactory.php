<?php
/*--------------------------------------------------------------
   ProductVariantFilterFactory.php 2023-06-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2023 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\Filter;

use Gambio\Core\Filter\SqlPagination;

/**
 * Class ProductVariantFilterFactory
 *
 * @package Gambio\Admin\Modules\Product\Submodules\Variant\App\Data\Filter
 * @codeCoverageIgnore
 */
class ProductVariantFilterFactory
{
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
    
    
    /**
     * @param array $filters
     *
     * @return ProductVariantFilters
     */
    public function createFilters(array $filters): ProductVariantFilters
    {
        return ProductVariantFilters::createFromMap($filters);
    }
    
    
    /**
     * @param string|null $sorting
     *
     * @return ProductVariantSorting
     */
    public function createSorting(?string $sorting): ProductVariantSorting
    {
        return ProductVariantSorting::create($sorting);
    }
}