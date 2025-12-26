<?php
/* --------------------------------------------------------------
   LanguageFilterFactory.php 2020-10-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\App\Data\Filter;

use Gambio\Core\Filter\SqlPagination;

/**
 * Class LanguageFilterFactory
 *
 * @package Gambio\Admin\Modules\Language\App\Data\Filter
 */
class LanguageFilterFactory
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
     * @return LanguageFilters
     */
    public function createFilters(array $filters): LanguageFilters
    {
        return LanguageFilters::createFromMap($filters);
    }
    
    
    /**
     * @param string|null $sorting
     *
     * @return LanguageSorting
     */
    public function createSorting(?string $sorting): LanguageSorting
    {
        return LanguageSorting::create($sorting);
    }
}