<?php
/* --------------------------------------------------------------
   ParcelServiceFilterService.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\ParcelService\Services;

use Gambio\Admin\Modules\ParcelService\Model\Collections\ParcelServices;

/**
 * Interface ParcelServiceFilterService
 *
 * @package Gambio\Admin\Modules\ParcelService\Services
 */
interface ParcelServiceFilterService
{
    /**
     * Returns a filtered and paginated collection of parcel services based on the given filter and sorting arguments.
     * The filters must be a map, that assigns an attribute it filtering pattern.
     * The sorting must be a comma-separated list of attributes. A `-` can be used to change the order to descending.
     *
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return ParcelServices
     */
    public function filterParcelServices(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): ParcelServices;
    
    
    /**
     * Returns total count of parcel services based on the given filter arguments.
     * The filters must be a map, that assigns an attribute it filtering pattern.
     *
     * @param array $filters
     *
     * @return int
     */
    public function getParcelServicesTotalCount(array $filters): int;
}