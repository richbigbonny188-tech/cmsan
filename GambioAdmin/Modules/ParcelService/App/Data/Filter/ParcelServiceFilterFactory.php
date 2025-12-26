<?php
/* --------------------------------------------------------------
   ParcelServiceFilterFactory.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\App\Data\Filter;

use Gambio\Core\Filter\SqlPagination;

/**
 * Class ParcelServiceFilterFactory
 *
 * @package Gambio\Admin\Modules\ParcelService\App\Data\Filter
 */
class ParcelServiceFilterFactory
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
     * @return ParcelServiceFilters
     */
    public function createFilters(array $filters): ParcelServiceFilters
    {
        return ParcelServiceFilters::createFromMap($filters);
    }
    
    
    /**
     * @param string|null $sorting
     *
     * @return ParcelServiceSorting
     */
    public function createSorting(?string $sorting): ParcelServiceSorting
    {
        return ParcelServiceSorting::create($sorting);
    }
}