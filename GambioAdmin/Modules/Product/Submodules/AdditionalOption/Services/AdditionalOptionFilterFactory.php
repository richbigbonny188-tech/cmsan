<?php
/* --------------------------------------------------------------
  AdditionalOptionFilterFactory.php 2023-06-06
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2023 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Services;

use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Filter\AdditionalOptionFilters;
use Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Filter\AdditionalOptionSorting;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class AdditionalOptionFilterFactory
 *
 * @package Gambio\Admin\Modules\Product\Submodules\AdditionalOption\Model\Filter
 */
class AdditionalOptionFilterFactory
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
     * @return AdditionalOptionFilters
     */
    public function createFilters(array $filters): AdditionalOptionFilters
    {
        return AdditionalOptionFilters::createFromMap($filters);
    }
    
    
    /**
     * @param string|null $sorting
     *
     * @return AdditionalOptionSorting
     */
    public function createSorting(?string $sorting): AdditionalOptionSorting
    {
        return AdditionalOptionSorting::create($sorting);
    }
}