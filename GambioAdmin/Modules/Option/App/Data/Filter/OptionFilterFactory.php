<?php
/* --------------------------------------------------------------
   OptionFilterFactory.php 2021-03-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Option\App\Data\Filter;

use Gambio\Core\Filter\SqlPagination;

/**
 * Class OptionFilterFactory
 *
 * @package Gambio\Admin\Modules\Option\App\Data\Filter
 * @codeCoverageIgnore
 */
class OptionFilterFactory
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
     * @return OptionFilters
     */
    public function createFilters(array $filters): OptionFilters
    {
        return OptionFilters::createFromMap($filters);
    }
    
    
    /**
     * @param string|null $sorting
     *
     * @return OptionSorting
     */
    public function createSorting(?string $sorting): OptionSorting
    {
        return OptionSorting::create($sorting);
    }
}