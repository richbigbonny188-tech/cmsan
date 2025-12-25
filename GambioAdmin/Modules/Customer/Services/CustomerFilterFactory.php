<?php
/*--------------------------------------------------------------
   CustomerFilterFactory.php 2022-08-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/
declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Services;

use Gambio\Admin\Modules\Customer\App\Data\Filter\CustomerFilters;
use Gambio\Admin\Modules\Customer\App\Data\Filter\CustomerSearch;
use Gambio\Admin\Modules\Customer\App\Data\Filter\CustomerSorting;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerFilters as CustomerFiltersInterface;
use Gambio\Admin\Modules\Customer\Model\Filter\CustomerSorting as CustomerSortingInterface;
use Gambio\Core\Filter\SqlPagination;

/**
 * Class CustomerFilterFactory
 *
 * @package Gambio\Admin\Modules\Customer\App\Data\Filter
 * @codeCoverageIgnore
 */
class CustomerFilterFactory
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
     * @return CustomerSortingInterface
     */
    public function createSorting(?string $sorting): CustomerSortingInterface
    {
        return CustomerSorting::create($sorting);
    }
    
    
    /**
     * @param string $keyword
     *
     * @return CustomerSearch
     */
    public function createSearch(string $keyword): CustomerSearch
    {
        return CustomerSearch::create($keyword);
    }
    
    
    /**
     * @param array $filters
     * @param bool  $useAndConcatenation
     *
     * @return CustomerFiltersInterface
     */
    public function createFilters(array $filters, bool $useAndConcatenation = true): CustomerFiltersInterface
    {
        return CustomerFilters::createFromMap($filters, $useAndConcatenation);
    }
}