<?php
/* --------------------------------------------------------------
   WithdrawalFilterService.php 2020-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2020 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

declare(strict_types=1);

namespace Gambio\Admin\Modules\Withdrawal\Services;

use Gambio\Admin\Modules\Withdrawal\Model\Collections\Withdrawals;

/**
 * Interface WithdrawalFilterService
 *
 * @package Gambio\Admin\Modules\Withdrawal\Services
 */
interface WithdrawalFilterService
{
    /**
     * Returns a filtered and paginated collection of withdrawals based on the given filter and sorting arguments.
     * The filters must be a map, that assigns an attribute it filtering pattern.
     * The sorting must be a comma-separated list of attributes. A `-` can be used to change the order to descending.
     *
     * @param array       $filters
     * @param string|null $sorting
     * @param int         $limit
     * @param int         $offset
     *
     * @return Withdrawals
     */
    public function filterWithdrawals(
        array $filters,
        ?string $sorting = null,
        int $limit = 25,
        int $offset = 0
    ): Withdrawals;
    
    
    /**
     * Returns total count of withdrawals based on the given filter arguments.
     * The filters must be a map, that assigns an attribute it filtering pattern.
     *
     * @param array $filters
     *
     * @return int
     */
    public function getWithdrawalsTotalCount(array $filters): int;
}