<?php
/*------------------------------------------------------------------------------
 OptionFilterService.php 2020-03-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2020 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 -----------------------------------------------------------------------------*/

namespace Gambio\Admin\Modules\Option\Services;

use Gambio\Admin\Modules\Option\Model\Collections\Options;

/**
 * Interface OptionFilterService
 *
 * @package Gambio\Admin\Modules\Option\Services
 *
 * @deprecated Since 4.7, the gambio shop-system supports submodules. Those product domains will be refactored into
 *             submodules too. All important changes will be documented in the developer journal as soon as they are
 *             implemented.
 */
interface OptionFilterService
{
    /**
     * Returns a filtered, sorted, paginated collection of options.
     *
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return Options
     */
    public function filterOptions(array $filters, ?string $sorting = null, int $limit = 25, int $offset = 0): Options;
    
    
    /**
     * Returns the total count of filtered options.
     *
     * @param array $filters
     *
     * @return int
     */
    public function getOptionsTotalCount(array $filters): int;
}