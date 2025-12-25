<?php
/* --------------------------------------------------------------
  CustomerMemoFilterFactory.php 2022-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2021 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Services;

use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\Filter\CustomerMemoFilters;
use Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\Filter\CustomerMemoSorting;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Filter\CustomerMemoFilters as CustomerMemoFiltersInterface;
use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Filter\CustomerMemoSorting as CustomerMemoSortingInterface;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class CustomerMemoFilterFactory
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\App\Data\Filter
 * @codeCoverageIgnore
 */
class CustomerMemoFilterFactory
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
     * @param string|null $sorting
     *
     * @return CustomerMemoSortingInterface
     */
    public function createSorting(?string $sorting): CustomerMemoSortingInterface
    {
        return CustomerMemoSorting::create($sorting);
    }
    
    
    /**
     * @param array $filters
     *
     * @return CustomerMemoFiltersInterface
     */
    public function createFilters(array $filters): CustomerMemoFiltersInterface
    {
        return CustomerMemoFilters::createFromMap($filters);
    }
}