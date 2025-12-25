<?php
/* --------------------------------------------------------------
   CustomerAddonValueFilterService.php 2022-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2022 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\AddonValues\Services;

use Gambio\Admin\Modules\Customer\Submodules\AddonValues\Model\Collections\CustomerAddonValues;

/**
 * Interface CustomerAddonValueFilterService
 *
 * @package Gambio\Admin\Modules\CustomerAddonValue\Services
 */
interface CustomerAddonValueFilterService
{
    /**
     * Returns a filtered and paginated collection of customer addon values based on the given filter and sorting
     * arguments. The filters must be a map, that assigns an attribute its filtering pattern. The sorting must be a
     * comma-separated list of attributes. A `-` can be used to change the order to descend.
     *
     * @param int         $customerId
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return CustomerAddonValues
     */
    public function filterCustomerAddonValues(
        int     $customerId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): CustomerAddonValues;
    
    
    /**
     * Returns total count of customer addon values based on the given filter arguments.
     * The filters must be a map, that assigns an attribute it's filtering pattern.
     *
     * @param int   $customerId
     * @param array $filters
     *
     * @return int
     */
    public function getCustomerAddonValuesTotalCount(int $customerId, array $filters): int;
}