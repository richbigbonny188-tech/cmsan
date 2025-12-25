<?php
/*--------------------------------------------------------------
   ImageListFilterFactory.php 2021-05-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ImageList\App\Data\Filter;

use Gambio\Core\Filter\SqlPagination;

/**
 * Class ImageListFilterFactory
 * @package Gambio\Admin\Modules\ImageList\App\Data\Filter
 * @codeCoverageIgnore
 */
class ImageListFilterFactory
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
     * @return ImageListFilters
     */
    public function createFilters(array $filters): ImageListFilters
    {
        return ImageListFilters::createFromMap($filters);
    }
    
    
    /**
     * @param string|null $sorting
     *
     * @return ImageListSorting
     */
    public function createSorting(?string $sorting): ImageListSorting
    {
        return ImageListSorting::create($sorting);
    }
}