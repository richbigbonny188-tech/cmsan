<?php
/* --------------------------------------------------------------
   WithdrawalReadService.php 2021-04-07
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
use Gambio\Admin\Modules\Withdrawal\Model\Withdrawal;
use Gambio\Admin\Modules\Withdrawal\Services\Exceptions\WithdrawalNotFoundException;

/**
 * Interface WithdrawalReadService
 *
 * @package Gambio\Admin\Modules\Withdrawal\Services
 */
interface WithdrawalReadService
{
    /**
     * Returns all available withdrawals.
     *
     * @return Withdrawals
     */
    public function getWithdrawals(): Withdrawals;
    
    
    /**
     * Returns a specific withdrawal based on the given ID.
     *
     * @param int $id
     *
     * @return Withdrawal
     *
     * @throws WithdrawalNotFoundException
     */
    public function getWithdrawalById(int $id): Withdrawal;
}