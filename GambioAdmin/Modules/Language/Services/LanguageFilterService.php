<?php
/* --------------------------------------------------------------
   LanguageFilterService.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Language\Services;

use Gambio\Admin\Modules\Language\Model\Collections\Languages;

/**
 * Interface LanguageFilterService
 *
 * @package Gambio\Admin\Modules\Language\Services
 */
interface LanguageFilterService
{
    /**
     * Returns a filtered and paginated collection of languages based on the given filter and sorting arguments.
     * The filters must be a map, that assigns an attribute it filtering pattern.
     * The sorting must be a comma-separated list of attributes. A `-` can be used to change the order to descending.
     *
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return Languages
     */
    public function filterLanguages(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): Languages;
    
    
    /**
     * Returns total count of languages based on the given filter arguments.
     * The filters must be a map, that assigns an attribute it filtering pattern.
     *
     * @param array $filters
     *
     * @return int
     */
    public function getLanguagesTotalCount(array $filters): int;
}