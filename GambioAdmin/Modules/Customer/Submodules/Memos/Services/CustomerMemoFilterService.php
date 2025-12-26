<?php
/*--------------------------------------------------------------
   CustomerMemoFilterService.php 2021-12-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2021 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
 -------------------------------------------------------------*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Customer\Submodules\Memos\Services;

use Gambio\Admin\Modules\Customer\Submodules\Memos\Model\Collections\CustomerMemos;

/**
 * Interface CustomerMemoFilterService
 *
 * @package Gambio\Admin\Modules\Customer\Submodules\Memos\Services
 */
interface CustomerMemoFilterService
{
    /**
     * Returns a filtered and paginated collection of customer memos based on the given filter and sorting arguments.
     * The filters must be a map, that assigns an attribute its filtering pattern.
     * The sorting must be a comma-separated list of attributes. A `-` can be used to change the order to descending.
     *
     * @param int         $customerId
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return CustomerMemos
     */
    public function filterCustomerMemos(
        int     $customerId,
        array   $filters,
        ?string $sorting = null,
        int     $limit = 25,
        int     $offset = 0
    ): CustomerMemos;
    
    
    /**
     * Returns total count of customer memos based on the given filter arguments.
     * The filters must be a map, that assigns an attribute it's filtering pattern.
     *
     * @param int   $customerId
     * @param array $filters
     *
     * @return int
     */
    public function getCustomerMemosTotalCount(int $customerId, array $filters): int;
}